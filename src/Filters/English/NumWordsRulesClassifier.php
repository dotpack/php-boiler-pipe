<?php

namespace Pforret\PhpArticleExtractor\Filters\English;

use Pforret\PhpArticleExtractor\Filters\IFilter;
use Pforret\PhpArticleExtractor\Formats\TextBlock;
use Pforret\PhpArticleExtractor\Formats\TextDocument;

final class NumWordsRulesClassifier implements IFilter
{
    protected function classify(TextBlock $prev, TextBlock $curr, TextBlock $next)
    {
        $isContent = false;

        if ($curr->getLinkDensity() <= 0.333) {
            if ($prev->getLinkDensity() <= 0.555) {
                if ($curr->getWordCount() <= 16) {
                    if ($next->getWordCount() <= 15) {
                        if ($prev->getWordCount() > 4) {
                            $isContent = true;
                        }
                    } else {
                        $isContent = true;
                    }
                } else {
                    $isContent = true;
                }
            } else {
                if ($curr->getWordCount() <= 40) {
                    if ($next->getWordCount() > 17) {
                        $isContent = true;
                    }
                } else {
                    $isContent = true;
                }
            }
        }

        return $curr->setIsContent($isContent);
    }

    public function process(TextDocument $doc): bool
    {
        $curr = new TextBlock();
        $next = new TextBlock();

        $hasChanges = false;
        foreach ($doc->getTextBlocks() as $tb) {
            $prev = $curr;
            $curr = $next;
            $next = $tb;
            $hasChanges = $this->classify($prev, $curr, $next) || $hasChanges;
        }

        $prev = $curr;
        $curr = $next;
        $next = new TextBlock();
        $hasChanges = $this->classify($prev, $curr, $next) || $hasChanges;

        $prev = $curr;
        $curr = $next;
        $next = new TextBlock();

        return $this->classify($prev, $curr, $next) || $hasChanges;
    }
}
