<?php

namespace OCA\CustomProperties\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;

class AdminSettings implements ISettings
{
    public function getForm()
    {
        return new TemplateResponse('customproperties', 'settings/admin');
    }

    /**
     * @return string the section ID
     */
    public function getSection()
    {
        return 'server';
    }

    /**
     * @return int whether the form should be rather on the top or bottom of
     * the admin section. The forms are arranged in ascending order of the
     * priority values. It is required to return a value between 0 and 100.
     */
    public function getPriority()
    {
        return 50;
    }
}
