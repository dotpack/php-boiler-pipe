<?php

namespace DotPack\PhpBoilerPipe\Filters\Heuristics;

use DotPack\PhpBoilerPipe\Filters\IFilter;
use DotPack\PhpBoilerPipe\TextDocument;
use DotPack\PhpBoilerPipe\TextLabels;

class KeepLargestBlockFilter implements IFilter
{
    protected $labelToKeep;

    public function __construct($expandToSameLevelText, $minWords)
    {
        $this->expandToSameLevelText = $expandToSameLevelText;
        $this->minWords = $minWords;
    }

    public function process(TextDocument $doc)
    {
        $blocks = $doc->getTextBlocks();
        if (count($blocks) < 2) return false;

        $maxNumWords = -1;
        $largestBlock = null;

        $level = -1; $i = 0; $n = -1;
        foreach ($doc->getTextBlocks() as $tb) {
            $wc = $tb->getWordCount();
            if ($wc > $maxNumWords) {
                $largestBlock = $tb;
                $maxNumWords = $wc;
                $n = $i;
                if ($this->expandToSameLevelText) {
                    $level = $tb->getLevel();
                }
            }
            $i++;
        }

        foreach ($doc->getTextBlocks() as $tb) {
            if ($tb == $largestBlock) {
                $tb->setIsContent(true);
                $tb->addLabel(TextLabels::VERY_LIKELY_CONTENT);
            } else {
                $tb->setIsContent(false);
                $tb->addLabel(TextLabels::MIGHT_BE_CONTENT);
            }
        }

        if ($this->expandToSameLevelText && $n != -1) {
            for ($i = $n; $i >= 0; $i--) {
                $tb = $blocks[$i];
                $tl = $tb->getLevel();
                if ($tl < $level) {
                    break;
                } else if ($tl == $level) {
                    if ($tb->getWordCount() >= $this->minWords) {
                        $tb->setIsContent(true);
                    }
                }
            }
            for ($i = $n, $l = count($blocks); $i < $l; $i++) {
                $tb = $blocks[$i];
                $tl = $tb->getLevel();
                if ($tl < $level) {
                    break;
                } else if ($tl == $level) {
                    if ($tb->getWordCount() >= $this->minWords) {
                        $tb->setIsContent(true);
                    }
                }
            }
        }

        return true;
    }
}