<?php
namespace Jankx\Adapter\Options;

use Jankx\Adapter\Options\Specs\Options;

class OptionsReader
{
    protected $sections = array();

    protected function getOptionsDirectoryPath()
    {
        return apply_filters(
            'jankx/option/directory/path',
            sprintf('%s/includes/options', constant('JANKX_ABSPATH'))
        );
    }

    protected function mergeFields(&$section, $fields, $override = false)
    {
        foreach ($fields as $key => $field) {
            if (isset($section[$key]) && !$override) {
                continue;
            }
            $section[$key] = $field;
        }
        return $section;
    }

    protected function appendFields(&$sectionFields, $fieldOrFields)
    {
        $firstValue = array_get(array_values($fieldOrFields), 0);
        if (is_array($firstValue)) {
            foreach ($fieldOrFields as $field) {
                $this->appendFields($sectionFields, $field);
            }
        } else {
            if (!isset($fieldOrFields['id'])) {
                return;
            }
            $fieldId = array_get($fieldOrFields, 'id');
            $sectionFields[$fieldId] = $fieldOrFields;
        }
    }


    public function readOptionFromFiles($path = null, &$options = array(), $isFields = false, $section_id = 'general')
    {
        $dirPath = is_null($path) ? $this->getOptionsDirectoryPath() : $path;
        $files   = glob(sprintf('%s/{*}', $dirPath), GLOB_BRACE);
        if (count($files) <= 0) {
            return array();
        }

        if (!isset($options[$section_id])) {
            $options[$section_id] = array();
        }
        if (!isset($options[$section_id]['fields'])) {
            $options[$section_id]['fields'] = array();
        }

        foreach ($files as $file) {
            $dirName = basename($file);
            if (is_dir($file)) {
                if ($dirName !== 'fields') {
                    $section_id = $dirName;
                }
                $this->readOptionFromFiles($file, $options, $dirName === 'fields', $section_id);
            } else {
                $fields = include $file;
                if (empty($fields)) {
                    continue;
                }
                if ($isFields || is_null($path)) {
                    if (isset($fields['fields'])) {
                        $this->mergeFields($options[$section_id], $fields);
                    } else {
                        $this->appendFields($options[$section_id]['fields'], $fields);
                    }
                } else {
                    $options[$section_id] = array_merge($options[$section_id], $fields);
                }
            }
        }

        // Settings general args
        $this->mergeFields($options['general'], array(
            'id' => 'general',
            'title' => __('General'),
            'icon' => 'dashicons dashicons-admin-generic',
            'priority' => 5
        ));

        return $options;
    }

    public function readAllOptions()
    {
        $rawOptions = $this->readOptionFromFiles();

        $options = new Options($rawOptions);

        // Convert array to sections
        $options->transformToSections();

        // Get jankx Options instance
        return apply_filters(
            'jankx/option/options',
            $options
        );
    }
}
