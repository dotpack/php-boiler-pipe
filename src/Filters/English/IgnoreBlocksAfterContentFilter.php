<?php

namespace DotPack\PhpBoilerPipe\Filters\English;

use DotPack\PhpBoilerPipe\Filters\IFilter;
use DotPack\PhpBoilerPipe\TextDocument;
use DotPack\PhpBoilerPipe\TextLabels;
use DotPack\PhpBoilerPipe\TextBlock;

class IgnoreBlocksAfterContentFilter implements IFilter
{
    protected $minWordCount = 0;

    public function __construct($minWordCount = 60)
    {
        $this->minWordCount = $minWordCount;
    }

    protected function getFullTextWordCount(TextBlock $block, $minTextDensity = 9) {
        if ($block->getTextDensity() < $minTextDensity) {
            return 0;
        } else {
            return $block->getWordCount();
        }
    }

    public function process(TextDocument $doc)
    {
        $change = false;
        $wordCount = 0;
        $foundEndOfText = false;
        foreach ($doc->getTextBlocks() as $tb) {
            $endOfText = $tb->hasLabel(TextLabels::INDICATES_END_OF_TEXT);
            if ($tb->isContent()) $wordCount += $this->getFullTextWordCount($tb);
            if ($endOfText && $wordCount >= $this->minWordCount) $foundEndOfText = true;
            if ($foundEndOfText) {
                $change = true;
                $tb->setIsContent(false);
            }
        }
        return $change;
    }
}
