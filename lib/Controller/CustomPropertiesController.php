<?php

namespace OCA\CustomProperties\Controller;

use Cassandra\Custom;
use OCA\CustomProperties\AppInfo\Application;
use OCA\CustomProperties\Db\CustomPropertiesMapper;
use OCA\CustomProperties\Db\CustomProperty;
use OCP\AppFramework\Controller;
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
            $prop->propertynameWithNamespace = "{" . Application::NS_PREFIX_CUSTOMPROPERTIES . "}" . $prop->propertyname;
            return $prop;
        }, $res);
        return new JSONResponse($entities);
    }

    /**
     * @NoCSRFRequired
     * @param string $propertylabel
     * @return CustomProperty
     */
    public function create(string $propertylabel): CustomProperty
    {
        $customProperty = new CustomProperty();
        $customProperty->setPropertylabel($propertylabel);
        $customProperty->setPropertyname(CustomProperty::createSlug($propertylabel));

        return $this->customPropertiesMapper->insert($customProperty);
    }

    /**
     * @NoCSRFRequired
     * @param CustomProperty $customProperty
     * @return CustomProperty
     */
    public function update(int $id, $customProperty): CustomProperty
    {
        $entity = $this->customPropertiesMapper->findById($customProperty["id"]);
        $entity->setPropertylabel($customProperty["propertylabel"]);
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
