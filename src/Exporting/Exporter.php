<?php

namespace Statamic\src\Exporting;

use Statamic\src\Exporting\Collectors\DataCollector;
use Statamic\src\Exporting\Exporters\Xliff;
use Statamic\src\Exporting\Preparators\DataPreparator;
use Statamic\src\Helpers\Config;

class Exporter
{
    protected $config;

    public function __construct($config, $options)
    {
        $this->config = $this->parseConfig($config);
        $this->dataCollector = new DataCollector($config, $options);
        $this->dataPreparator = new DataPreparator($options);
    }

    public function run()
    {
        // Clear out the result directory.
        $this->clearExportsDirectory();

        $data = $this->dataCollector->collect($this->config);
        $data = $this->dataPreparator->prepare($data);

        $files = [];
        foreach ($data as $locale => $data) {
            $files[] = (new Xliff($this->config))->create($locale, $data);
        }

        if (count($files) > 1) {
            return FileZipper::zip($files);
        }

        return $files[0];
    }

    protected function parseConfig($config)
    {
        if (is_string($config['exclude_page_ids'])) {
            $config['exclude_page_ids'] = explode(',', $config['exclude_page_ids']);
        }

        if (is_string($config['exclude_collection_slugs'])) {
            $config['exclude_collection_slugs'] = explode(',', $config['exclude_collection_slugs']);
        }

        return $config;
    }

    /**
     * Removes all files from the export directory to make
     * room for the new files.
     *
     * @return void
     */
    protected function clearExportsDirectory()
    {
        $exportPath = Config::get('export_path');
        $files = scandir($exportPath);
        $filesToKeep = ['.', '..', '.DS_Store', '.gitkeep'];

        foreach ($files as $file) {
            if (!in_array($file, $filesToKeep)) {
                unlink($exportPath . $file);
            }
        }
    }
}
