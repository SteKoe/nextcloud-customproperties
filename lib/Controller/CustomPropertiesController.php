<?php

namespace OCA\CustomProperties\Controller;

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
        $res = $this->customPropertiesMapper->findAll();
        $entities = array_map(function ($prop) {
            $prop->propertyname = "oc:" . $prop->propertyname;
            return $prop;
        }, $res);
        return new JSONResponse($entities);
    }

    /**
     * @NoCSRFRequired
     * @param string $propertylabel
     * @return CustomProperty|Entity
     */
    public function create(CustomProperty $customProperty): Entity
    {
        if (!$customProperty->isValid()) {
            throw new CustomPropertyInvalidError();
        }

        return $this->customPropertiesMapper->insert($customProperty);
    }

    /**
     * @NoCSRFRequired
     * @param CustomProperty $customProperty
     * @return CustomProperty|Entity
     */
    public function update(CustomProperty $customProperty): Entity
    {
        if (!$customProperty->isValid()) {
            throw new CustomPropertyInvalidError();
        }

        $entity = $this->customPropertiesMapper->findById($customProperty->id);

        $entity->setPropertyname($customProperty->propertyname);
        $entity->setPropertylabel($customProperty->propertylabel);

        return $this->customPropertiesMapper->update($entity);
    }

    /**
     * @NoCSRFRequired
     * @param int $id
     * @return CustomProperty
     */
    public function delete(int $id): CustomProperty
    {
        $customProperty = $this->customPropertiesMapper->findById($id);
        return $this->customPropertiesMapper->delete($customProperty);
    }

}
