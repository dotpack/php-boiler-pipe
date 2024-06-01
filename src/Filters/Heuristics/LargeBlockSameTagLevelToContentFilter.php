<?php

namespace Pforret\PhpArticleExtractor\Filters\Heuristics;

use Pforret\PhpArticleExtractor\Filters\IFilter;
use Pforret\PhpArticleExtractor\Formats\TextDocument;
use Pforret\PhpArticleExtractor\Naming\TextLabels;

final class LargeBlockSameTagLevelToContentFilter implements IFilter
{
    public function process(TextDocument $doc): bool
    {
        $hasChanges = false;

        $level = -1;
        foreach ($doc->getTextBlocks() as $tb) {
            if ($tb->isContent() && $tb->hasLabel(TextLabels::MIGHT_BE_CONTENT)) {
                $level = $tb->getLevel();
                break;
            }
        }

        if ($level == -1) {
            return false;
        }
        foreach ($doc->getTextBlocks() as $tb) {
            if (! $tb->isContent()) {
                if ($tb->getWordCount() >= 100 && $tb->getLevel() == $level) {
                    $tb->setIsContent(true);
                    $hasChanges = true;
                }
            }
        }

        return $hasChanges;
    }
}
