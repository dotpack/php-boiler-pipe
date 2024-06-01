<?php

namespace Pforret\PhpArticleExtractor\Filters\Simple;

use Pforret\PhpArticleExtractor\Filters\IFilter;
use Pforret\PhpArticleExtractor\Formats\TextDocument;

final class BoilerplateBlockFilter implements IFilter
{
    private string $labelToKeep;

    public function __construct(?string $labelToKeep = null)
    {
        $this->labelToKeep = $labelToKeep;
    }

    public function process(TextDocument $doc): bool
    {
        $hasChanges = false;
        $textBlocks = $doc->getTextBlocks();
        foreach ($textBlocks as $tb) {
            if (! $tb->isContent() && ($this->labelToKeep == null || ! $tb->hasLabel($this->labelToKeep))) {
                $doc->removeTextBlock($tb);
                $hasChanges = true;
            }
        }

        return $hasChanges;
    }
}
