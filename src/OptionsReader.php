<?php

namespace Jankx\Adapter\Options;

use Jankx\Adapter\Options\Specs\Options;
use Jankx\Adapter\Options\Repositories\ConfigRepository;

class OptionsReader
{
    protected $sections = array();
    protected $configRepository;

    public function __construct()
    {
        $this->configRepository = new ConfigRepository();
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
