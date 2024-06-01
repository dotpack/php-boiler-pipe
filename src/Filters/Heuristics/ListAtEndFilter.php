<?php

namespace Pforret\PhpArticleExtractor\Filters\Heuristics;

use Pforret\PhpArticleExtractor\Filters\IFilter;
use Pforret\PhpArticleExtractor\Formats\TextDocument;
use Pforret\PhpArticleExtractor\Naming\TextLabels;

final class ListAtEndFilter implements IFilter
{
    public function process(TextDocument $doc): bool
    {
        $hasChanges = false;

        $level = PHP_INT_MAX;
        foreach ($doc->getTextBlocks() as $tb) {
            if ($tb->isContent() && $tb->hasLabel(TextLabels::VERY_LIKELY_CONTENT)) {
                $level = $tb->getLevel();
            } else {
                if (
                    $tb->getLevel() > $level &&
                    $tb->hasLabel(TextLabels::MIGHT_BE_CONTENT) &&
                    $tb->hasLabel(TextLabels::LI) &&
                    $tb->getLinkDensity() == 0
                ) {
                    $tb->setIsContent(true);
                    $hasChanges = true;
                } else {
                    $level = PHP_INT_MAX;
                }
            }
        }

        return $hasChanges;
    }
}
