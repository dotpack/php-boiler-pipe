<?php

namespace DotPack\PhpBoilerPipe\Filters\Heuristics;

use DotPack\PhpBoilerPipe\Filters\IFilter;
use DotPack\PhpBoilerPipe\TextDocument;
use DotPack\PhpBoilerPipe\TextLabels;

class ListAtEndFilter implements IFilter
{
    public function process(TextDocument $doc)
    {
        $changes = false;

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
                    $changes = true;
                } else {
                    $level = PHP_INT_MAX;
                }
            }
        }

        return $changes;
    }
}

