<?php

namespace DotPack\PhpBoilerPipe\Filters\Heuristics;

use DotPack\PhpBoilerPipe\Filters\IFilter;
use DotPack\PhpBoilerPipe\TextDocument;
use DotPack\PhpBoilerPipe\TextLabels;
use DotPack\PhpBoilerPipe\TextBlock;

class TrailingHeadlineToBoilerplateFilter implements IFilter
{
    public function process(TextDocument $doc)
    {
        $change = false;

        /**
         * @var TextBlock[] $textBlocks
         */
        $textBlocks = $doc->getTextBlocks();
        $textBlocks = array_reverse($textBlocks);

        foreach ($textBlocks as $tb) {
            if ($tb->isContent()) {
                if ($tb->hasLabel(TextLabels::HEADING)) {
                    $tb->setIsContent(false);
                    $change = true;
                } else {
                    break;
                }
            }
        }
        return $change;
    }
}
