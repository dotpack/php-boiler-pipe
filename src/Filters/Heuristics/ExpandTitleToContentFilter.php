<?php

namespace Pforret\PhpArticleExtractor\Filters\Heuristics;

use Pforret\PhpArticleExtractor\Filters\IFilter;
use Pforret\PhpArticleExtractor\Formats\TextDocument;
use Pforret\PhpArticleExtractor\Naming\TextLabels;

class ExpandTitleToContentFilter implements IFilter
{
    public function process(TextDocument $doc): bool
    {
        $i = 0;
        $title = -1;
        $contentStart = -1;
        foreach ($doc->getTextBlocks() as $tb) {
            if ($contentStart == -1 && $tb->hasLabel(TextLabels::TITLE)) {
                $title = $i;
                $contentStart = -1;
            }
            if ($contentStart == -1 && $tb->isContent()) {
                $contentStart = $i;
            }
            $i++;
        }

        if ($contentStart <= $title || $title == -1) {
            return false;
        }

        $hasChanges = false;
        foreach ($doc->getTextBlocks() as $key => $tb) {
            if ($key < $title) {
                continue;
            }
            if ($key > $contentStart) {
                continue;
            }
            if ($tb->hasLabel(TextLabels::MIGHT_BE_CONTENT)) {
                $hasChanges = $tb->setIsContent(true) || $hasChanges;
            }
        }

        return $hasChanges;
    }
}
