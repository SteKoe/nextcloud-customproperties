<?php

namespace OCA\CustomProperties\Controller;

use Exception;
use OCA\CustomProperties\Db\CustomPropertiesMapper;
use OCA\CustomProperties\Db\PropertiesMapper;
use OCA\CustomProperties\Db\Property;
use OCA\CustomProperties\Service\PropertyService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use Psr\Log\LoggerInterface;

class CustomPropertiesController extends Controller
{
    const NS_PREFIX = "{http://owncloud.org/ns}";

    /**
     * @var PropertyService
     */
    private $propertyService;
    /**
     * @var CustomPropertiesMapper
     */
    private $customPropertiesMapper;
    /**
     * @var PropertiesMapper
     */
    private $propertiesMapper;
    /**
     * @var LoggerInterface
     */
    private $logger;

    private $userId;

    public function __construct($AppName, IRequest $request, PropertyService $propertyService, CustomPropertiesMapper $customPropertiesMapper, PropertiesMapper $propertiesMapper, LoggerInterface $logger, $UserId)
    {
        parent::__construct($AppName, $request);
        $this->propertyService = $propertyService;
        $this->customPropertiesMapper = $customPropertiesMapper;
        $this->propertiesMapper = $propertiesMapper;
        $this->logger = $logger;
        $this->userId = $UserId;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param string $path
     * @param string $name
     * @return JSONResponse
     */
    public function index(string $path, string $name): JSONResponse
    {
        $res = $this->propertyService->getProperties($this->userId, $path, $name);
        return new JSONResponse($res);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function update(string $propertypath, string $propertyname, string $propertyvalue): \OCP\AppFramework\Db\Entity
    {
        $propertyname = CustomPropertiesController::NS_PREFIX . $propertyname;

        try {
            $res = $this->propertiesMapper->findByPathAndName($propertypath, $propertyname, $this->userId);
            $res->setPropertyvalue($propertyvalue);
            return $this->propertiesMapper->update($res);
        } catch (Exception $exception) {
            $property = new Property();
            $property->setUserid($this->userId);
            $property->setPropertypath($propertypath);
            $property->setPropertyname($propertyname);
            $property->setPropertyvalue($propertyvalue);

            return $this->propertiesMapper->insert($property);
        }
    }
}
