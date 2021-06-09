<?php

namespace OCA\CustomProperties\Plugin;

use OCA\CustomProperties\Db\PropertiesMapper;
use OCA\CustomProperties\Db\Property;
use OCP\Files\FileInfo;
use OCP\Files\IMimeTypeDetector;
use OCP\Files\IRootFolder;
use OCP\Files\Node;
use OCP\Files\NotFoundException;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\Search\IProvider;
use OCP\Search\ISearchQuery;
use OCP\Search\SearchResult;
use OCP\Search\SearchResultEntry;

class SearchProvider implements IProvider
{
    /**
     * @var IL10N
     */
    private $l10n;
    /**
     * @var IURLGenerator
     */
    private $urlGenerator;
    /**
     * @var IRootFolder
     */
    private $rootFolder;
    /**
     * @var IMimeTypeDetector
     */
    private $mimeTypeDetector;
    /**
     * @var PropertiesMapper
     */
    private $propertiesMapper;

    /**
     * SearchProvider constructor.
     *
     * @param IL10N $l10n
     * @param IURLGenerator $urlGenerator
     * @param IMimeTypeDetector $mimeTypeDetector
     * @param IRootFolder $rootFolder
     * @param PropertiesMapper $propertiesMapper
     */
    public function __construct(IL10N $l10n,
                                IURLGenerator $urlGenerator,
                                IMimeTypeDetector $mimeTypeDetector,
                                IRootFolder $rootFolder,
                                PropertiesMapper $propertiesMapper)
    {
        $this->l10n = $l10n;
        $this->urlGenerator = $urlGenerator;
        $this->rootFolder = $rootFolder;
        $this->mimeTypeDetector = $mimeTypeDetector;
        $this->propertiesMapper = $propertiesMapper;
    }

    public function getId(): string
    {
        return 'customproperties';
    }

    public function getName(): string
    {
        return $this->l10n->t('Custom Properties');
    }

    public function getOrder(string $route, array $routeParameters): int
    {
        if ($route === 'files.View.index') {
            return 0;
        }
        return 10;
    }

    public function search(IUser $user, ISearchQuery $query): SearchResult
    {
        $userFolder = $this->rootFolder->getUserFolder($user->getUID());

        $searchTerm = $query->getTerm();

        if (str_contains($searchTerm, ":")) {
            [$propertyname, $propertyvalue] = preg_split("/\s*:\s*/", $searchTerm);

            $properties = $this->propertiesMapper->findByPropertyNameLikeAndPropertyValueLike(
                $propertyname,
                $propertyvalue,
                $user->getUID(),
                (int)$query->getCursor(),
                $query->getLimit()
            );
        } else {
            $properties = $this->propertiesMapper->findByPropertyValueLike(
                $searchTerm,
                $user->getUID(),
                (int)$query->getCursor(),
                $query->getLimit()
            );
        }

        $files = array_map(function ($result) use ($user, $userFolder) {
            $path = str_replace("files/" . $user->getUID(), '', $result->propertypath);
            try {
                $node = $userFolder->get($path);
                return new CustomPropertySearchResult(
                    $node,
                    $result
                );
            } catch (NotFoundException $e) {
                return null;
            }
        }, $properties);

        $files = array_filter($files, function ($f) {
            return $f !== null;
        });

        $entries = array_map(function (CustomPropertySearchResult $result) use ($query, $userFolder) {
            // Generate thumbnail url
            $thumbnailUrl = $this->urlGenerator->linkToRouteAbsolute('core.Preview.getPreviewByFileId', ['x' => 32, 'y' => 32, 'fileId' => $result->node->getId()]);
            $path = $userFolder->getRelativePath($result->node->getPath());
            $link = $this->urlGenerator->linkToRoute(
                'files.view.index',
                [
                    'dir' => dirname($path),
                    'scrollto' => $result->node->getName(),
                ]
            );

            $searchResultEntry = new SearchResultEntry(
                $thumbnailUrl,
                $result->node->getName(),
                $this->formatSubline($result),
                $this->urlGenerator->getAbsoluteURL($link),
                $result->node->getMimetype() === FileInfo::MIMETYPE_FOLDER ? 'icon-folder' : $this->mimeTypeDetector->mimeTypeIcon($result->node->getMimetype())
            );
            $searchResultEntry->addAttribute('fileId', (string)$result->node->getId());
            $searchResultEntry->addAttribute('path', $path);
            return $searchResultEntry;
        }, $files);

        return SearchResult::paginated(
            $this->getName(),
            $entries,
            $query->getCursor() + $query->getLimit());
    }

    /**
     * Format subline for files
     *
     * @param string $searchResult
     * @return string
     */
    private function formatSubline(CustomPropertySearchResult $searchResult): string
    {
        $propertyname = preg_split("/}/", $searchResult->property->propertyname)[1];
        $propertyvalue = $searchResult->property->propertyvalue;
        return "$propertyname: $propertyvalue";
    }
}

