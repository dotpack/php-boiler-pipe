<?php

namespace DotPack\PhpBoilerPipe\Filters\Heuristics;

use DotPack\PhpBoilerPipe\Filters\IFilter;
use DotPack\PhpBoilerPipe\TextDocument;
use DotPack\PhpBoilerPipe\TextLabels;

class ExpandTitleToContentFilter implements IFilter
{
    public function process(TextDocument $doc)
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

        $changes = false;
        foreach ($doc->getTextBlocks() as $key => $tb) {
            if ($key < $title) continue;
            if ($key > $contentStart) continue;
            if ($tb->hasLabel(TextLabels::MIGHT_BE_CONTENT)) {
                $changes = $tb->setIsContent(true) || $changes;
            }
        }
        return $changes;
    }
}

