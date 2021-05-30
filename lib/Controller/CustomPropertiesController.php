<?php

namespace OCA\CustomProperties\Controller;

use OCA\CustomProperties\AppInfo\Application;
use OCA\CustomProperties\Service\PropertyService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use Psr\Log\LoggerInterface;

class CustomPropertiesController extends Controller
{
    /**
     * @var PropertyService
     */
    private $propertyService;
    /**
     * @var LoggerInterface
     */
    private $logger;

    private $userid;

    public function __construct($AppName, IRequest $request, PropertyService $propertyService, LoggerInterface $logger, $UserId)
    {
        parent::__construct($AppName, $request);
        $this->propertyService = $propertyService;
        $this->logger = $logger;
        $this->userid = $UserId;
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
        $res = $this->propertyService->getProperties($this->userid, $path, $name);
        return new JSONResponse($res);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function update(string $propertypath, string $propertyname, string $propertyvalue): Entity
    {
        $propertyname = Application::NS_PREFIX_CUSTOMPROPERTIES . $propertyname;
        return $this->propertyService->upsertProperty($propertypath, $propertyname, $propertyvalue, $this->userid);
    }
}
