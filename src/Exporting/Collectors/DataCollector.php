<?php

namespace Statamic\src\Exporting\Collectors;

use function Statamic\Addons\TranslationManager\Exporting\Collectors\app;
use function Statamic\Addons\TranslationManager\Exporting\Collectors\collect;

class DataCollector
{
    protected $options;

    /**
     * The available collectors.
     * This array determines what datatypes will be collected.
     *
     * @var array
     */
    protected $collectors = [
        'page' => PageCollector::class,
        'global' => GlobalCollector::class,
        'collection' => CollectionCollector::class,
        'taxonomy' => TaxonomyCollector::class,
    ];

    public function __construct($config, $options)
    {
        $this->config = $config;
        $this->options = $options;
    }

    public function collect()
    {
        $data = collect();

        foreach ($this->collectors as $key => $collector) {
            // "Export everything" overrides any specific selections.
            if ($this->options['content'] === 'everything') {
                $this->options[$key] = 'all';
            }

            if ($this->options[$key] === 'no') {
                continue;
            }

            if ($this->options[$key] === 'all') {
                $data = $data->merge(app($collector)->all($this->config));
            } else {
                $item = app($collector)->find($this->options[$key]);

                // If the returned value is a collection, for example
                // multiple entries in a selected collection set, add
                // all of them to the data. Otherwise, just push the one.
                if (in_array(class_basename($item), ['EntryCollection', 'TermCollection'])) {
                    foreach ($item as $object) {
                        $data = $data->push($object);
                    }
                } else {
                    $data = $data->push($item);
                }
            }
        }

        return $data;
    }
}
