<?php

namespace DotPack\PhpBoilerPipe\Filters\English;

use DotPack\PhpBoilerPipe\Filters\IFilter;
use DotPack\PhpBoilerPipe\TextDocument;
use DotPack\PhpBoilerPipe\TextBlock;

class NumWordsRulesClassifier implements IFilter
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

    public function process(TextDocument $doc)
    {
        $curr = new TextBlock();
        $next = new TextBlock();

        $change = false;
        foreach ($doc->getTextBlocks() as $tb) {
            $prev = $curr;
            $curr = $next;
            $next = $tb;
            $change = $this->classify($prev, $curr, $next) || $change;
        }

        $prev = $curr;
        $curr = $next;
        $next = new TextBlock();
        $change = $this->classify($prev, $curr, $next) || $change;

        $prev = $curr;
        $curr = $next;
        $next = new TextBlock();
        $change = $this->classify($prev, $curr, $next) || $change;

        return $change;
    }
}
