<?php

namespace OCA\CustomProperties\Controller;

use OCA\CustomProperties\Db\CustomPropertiesMapper;
use OCA\CustomProperties\Db\CustomProperty;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class AdminSettingsController extends Controller
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
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function index(): JSONResponse
    {
        $res = $this->customPropertiesMapper->findAll();
        return new JSONResponse($res);
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
     * @param int $id
     * @return CustomProperty
     */
    public function delete(int $id): CustomProperty
    {
        $customProperty = $this->customPropertiesMapper->findById($id);
        return $this->customPropertiesMapper->delete($customProperty);
    }

}