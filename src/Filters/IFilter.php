<?php

namespace DotPack\PhpBoilerPipe\Filters;

use DotPack\PhpBoilerPipe\TextDocument;

interface IFilter
{
    public function process(TextDocument $doc);
}
