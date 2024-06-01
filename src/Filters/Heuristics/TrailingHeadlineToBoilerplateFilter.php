<?php

namespace Pforret\PhpArticleExtractor\Filters\Heuristics;

use Pforret\PhpArticleExtractor\Filters\IFilter;
use Pforret\PhpArticleExtractor\Formats\TextBlock;
use Pforret\PhpArticleExtractor\Formats\TextDocument;
use Pforret\PhpArticleExtractor\Naming\TextLabels;

final class TrailingHeadlineToBoilerplateFilter implements IFilter
{
    public function process(TextDocument $doc): bool
    {
        $hasChanges = false;

        /**
         * @var TextBlock[] $textBlocks
         */
        $textBlocks = $doc->getTextBlocks();
        $textBlocks = array_reverse($textBlocks);

        foreach ($textBlocks as $tb) {
            if ($tb->isContent()) {
                if ($tb->hasLabel(TextLabels::HEADING)) {
                    $tb->setIsContent(false);
                    $hasChanges = true;
                } else {
                    break;
                }
            }
        }

        return $hasChanges;
    }
}
