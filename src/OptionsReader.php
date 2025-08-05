<?php

namespace Jankx\Adapter\Options;

if (!defined('ABSPATH')) {
    exit('Cheating huh?');
}

use Jankx\Adapter\Options\Repositories\ConfigRepository;

class OptionsReader
{
    protected static $instance = null;
    protected $sections = array();
    protected $configRepository;

    protected $optionsDirectoryPath = null;

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

    public function setOptionsDirectoryPath($optionsDirectoryPath)
    {
        $this->optionsDirectoryPath = $optionsDirectoryPath;
    }


    public function getOptionsDirectoryPath()
    {
        if (is_null($this->optionsDirectoryPath)) {
            $this->optionsDirectoryPath = sprintf('%s/includes/options', constant('JANKX_ABSPATH'));
        }

        return apply_filters(
            'jankx/option/directory/path',
            $this->optionsDirectoryPath
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
