<?php

namespace Backend\Modules\Filter\Installer;

use Backend\Core\Installer\ModuleInstaller;

/**
 * Class Installer
 * @package Backend\Modules\Filter\Installer
 */
class Installer extends ModuleInstaller
{

    /**
     * Install the module
     */
    public function Install()
    {
        $this->addModule('Filter');
        $this->setModuleRights(1, 'Filter');
    }
}
