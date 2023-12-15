<?php

namespace Statamic\src\Exporting\Collectors\Contracts;

interface Collector
{
    public function all($config);

    public function find($handle);
}
