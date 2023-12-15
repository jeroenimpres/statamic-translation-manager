<?php

namespace Statamic\src\Exporting\Collectors;

use Statamic\Addons\TranslationManager\Exporting\Collectors\Collection;
use Statamic\API\GlobalSet;
use Statamic\src\Exporting\Collectors\Contracts\Collector;

class GlobalCollector implements Collector
{
    /**
     * Returns all global sets.
     *
     * @return Collection
     */
    public function all($config)
    {
        return GlobalSet::all();
    }

    /**
     * Returns a single global set.
     *
     * @param string|int $handle
     * @return GlobalSet
     */
    public function find($handle)
    {
        return GlobalSet::find($handle);
    }
}
