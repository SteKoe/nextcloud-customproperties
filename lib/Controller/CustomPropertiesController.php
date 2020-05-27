<?php

namespace OCA\CustomProperties\Controller;

use Exception;
use OC\Files\Filesystem;
use OCA\CustomProperties\Db\CustomPropertiesMapper;
use OCA\CustomProperties\Db\PropertiesMapper;
use OCA\CustomProperties\Db\Property;
use OCA\CustomProperties\Service\PropertyService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http\JSONResponse;
use OCP\ILogger;
use OCP\IRequest;
use function Aws\map;

class CustomPropertiesController extends Controller {
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
	 * @var ILogger
	 */
	private $logger;
	private $userId;

	public function __construct($AppName, IRequest $request, PropertyService $propertyService, CustomPropertiesMapper $customPropertiesMapper, PropertiesMapper $propertiesMapper, ILogger $logger, $UserId) {
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
	public function index(string $path, string $name): JSONResponse {
		$res = $this->propertyService->getProperties($this->userId, $path, $name);
		return new JSONResponse($res);
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(string $propertypath, string $propertyname, string $propertyvalue): Property {
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
