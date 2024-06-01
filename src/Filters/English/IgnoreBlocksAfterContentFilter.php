<?php

namespace Pforret\PhpArticleExtractor\Filters\English;

use Pforret\PhpArticleExtractor\Filters\IFilter;
use Pforret\PhpArticleExtractor\Formats\TextBlock;
use Pforret\PhpArticleExtractor\Formats\TextDocument;
use Pforret\PhpArticleExtractor\Naming\TextLabels;

final class IgnoreBlocksAfterContentFilter implements IFilter
{
    private int $minWordCount = 0;

    public function __construct(int $minWordCount = 60)
    {
        $this->minWordCount = $minWordCount;
    }

    private function getFullTextWordCount(TextBlock $block, float $minTextDensity = 9): int
    {
        if ($block->getTextDensity() < $minTextDensity) {
            return 0;
        } else {
            return $block->getWordCount();
        }
    }

    public function process(TextDocument $doc): bool
    {
        $hasChanges = false;
        $wordCount = 0;
        $foundEndOfText = false;
        foreach ($doc->getTextBlocks() as $tb) {
            $endOfText = $tb->hasLabel(TextLabels::INDICATES_END_OF_TEXT);
            if ($tb->isContent()) {
                $wordCount += $this->getFullTextWordCount($tb);
            }
            if ($endOfText && $wordCount >= $this->minWordCount) {
                $foundEndOfText = true;
            }
            if ($foundEndOfText) {
                $hasChanges = true;
                $tb->setIsContent(false);
            }
        }

        return $hasChanges;
    }
}
