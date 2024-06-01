<?php

namespace Pforret\PhpArticleExtractor\Filters;

use Pforret\PhpArticleExtractor\Formats\TextDocument;

interface IFilter
{
    public function process(TextDocument $doc): bool;
}
