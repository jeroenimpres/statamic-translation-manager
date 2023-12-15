<?php

namespace Statamic\src\Exporting\Collectors;

use Statamic\Addons\TranslationManager\Exporting\Collectors\Collection;
use Statamic\API\Taxonomy;
use Statamic\API\Term;
use Statamic\src\Exporting\Collectors\Contracts\Collector;

use function Statamic\Addons\TranslationManager\Exporting\Collectors\collect;

class TaxonomyCollector implements Collector
{
    /**
     * Retrieves all terms in all taxonomies.
     *
     * @return Collection
     */
    public function all($config)
    {
        $taxonomies = Taxonomy::handles();
        $terms = collect();

        foreach ($taxonomies as $handle) {
            $items = Term::whereTaxonomy($handle);

            foreach ($items as $entry) {
                $terms = $terms->push($entry);
            }
        }

        return $terms;
    }

    /**
     * Returns all terms in the selected taxonomy.
     *
     * @param string $handle
     * @return Collection
     */
    public function find($handle)
    {
        return Term::whereTaxonomy($handle);
    }
}
