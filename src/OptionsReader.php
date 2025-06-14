<?php

namespace Jankx\Adapter\Options;

if (!defined('ABSPATH')) {
    exit('Cheatin huh?');
}

use Jankx\Adapter\Options\Repositories\ConfigRepository;

class OptionsReader
{
    protected static $instance = null;
    protected $sections = array();
    protected $configRepository;

    private function __construct()
    {
        $this->configRepository = new ConfigRepository();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function getOptionsDirectoryPath()
    {
        return apply_filters(
            'jankx/option/directory/path',
            sprintf('%s/includes/options', constant('JANKX_ABSPATH'))
        );
    }

    public function getPages()
    {
        return $this->configRepository->getPages();
    }

    public function getSections($pageTitle)
    {
        return $this->configRepository->getSections($pageTitle);
    }

    public function getFields($sectionTitle)
    {
        return $this->configRepository->getFields($sectionTitle);
    }
}
