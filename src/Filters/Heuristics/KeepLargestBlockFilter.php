<?php

namespace Pforret\PhpArticleExtractor\Filters\Heuristics;

use Pforret\PhpArticleExtractor\Filters\IFilter;
use Pforret\PhpArticleExtractor\Formats\TextDocument;
use Pforret\PhpArticleExtractor\Naming\TextLabels;

final class KeepLargestBlockFilter implements IFilter
{
    private string $labelToKeep;

    private int $minWords;

    private bool $expandToSameLevelText;

    public function __construct(bool $expandToSameLevelText, int $minWords)
    {
        $this->expandToSameLevelText = $expandToSameLevelText;
        $this->minWords = $minWords;
    }

    public function process(TextDocument $doc): bool
    {
        $blocks = $doc->getTextBlocks();
        if (count($blocks) < 2) {
            return false;
        }

        $maxNumWords = -1;
        $largestBlock = null;

        $level = -1;
        $i = 0;
        $n = -1;
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
                } elseif ($tl == $level) {
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
                } elseif ($tl == $level) {
                    if ($tb->getWordCount() >= $this->minWords) {
                        $tb->setIsContent(true);
                    }
                }
            }
        }

        return true;
    }
}
