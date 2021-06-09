<?php
declare(strict_types=1);

namespace OCA\CustomProperties\Controller;

use OCA\CustomProperties\AppInfo\Application;
use OCA\CustomProperties\Db\CustomPropertiesMapper;
use OCA\CustomProperties\Db\CustomProperty;
use OCA\CustomProperties\Error\CustomPropertyInvalidError;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class CustomPropertiesController extends Controller
{
    /**
     * @var CustomPropertiesMapper
     */
    private $customPropertiesMapper;

    /**
     * CustomPropertiesController constructor.
     * @param $AppName
     * @param IRequest $request
     * @param CustomPropertiesMapper $customPropertiesMapper
     */
    public function __construct($AppName, IRequest $request, CustomPropertiesMapper $customPropertiesMapper)
    {
        parent::__construct($AppName, $request);
        $this->customPropertiesMapper = $customPropertiesMapper;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function index(): JSONResponse
    {
        $entities = $this->customPropertiesMapper->findAll();
        return new JSONResponse($entities);
    }

    /**
     * @NoCSRFRequired
     * @param string $propertylabel
     * @return CustomProperty|Entity
     */
    public function create(array $customProperty): Entity
    {
        $newCustomProperty = new CustomProperty();
        $newCustomProperty->setPropertyname($customProperty['propertyname']);
        $newCustomProperty->setPropertylabel($customProperty['propertylabel']);
        $newCustomProperty->setPropertytype($customProperty['propertytype']);

        if (!$newCustomProperty->isValid()) {
            throw new CustomPropertyInvalidError();
        }

        return $this->customPropertiesMapper->insert($newCustomProperty);
    }

    /**
     * @NoCSRFRequired
     * @param CustomProperty $customProperty
     * @return CustomProperty|Entity
     */
    public function update(array $customProperty)
    {
        $entity = $this->customPropertiesMapper->findById($customProperty['id']);

        $entity->setPropertyname($customProperty['propertyname']);
        $entity->setPropertylabel($customProperty['propertylabel']);

        if (!$entity->isValid()) {
            throw new CustomPropertyInvalidError();
        }

        return $this->customPropertiesMapper->update($entity);
    }

    /**
     * @NoCSRFRequired
     * @param int $id
     * @return CustomProperty|Entity
     */
    public function delete(int $id)
    {
        $customProperty = $this->customPropertiesMapper->findById($id);
        return $this->customPropertiesMapper->delete($customProperty);
    }

}
