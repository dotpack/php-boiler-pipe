<?php

namespace Pforret\PhpArticleExtractor\Filters\Heuristics;

use Pforret\PhpArticleExtractor\Filters\IFilter;
use Pforret\PhpArticleExtractor\Formats\TextDocument;
use Pforret\PhpArticleExtractor\Naming\TextLabels;

// todo
final class DocumentTitleMatchClassifier implements IFilter
{
    private function clear(string $title): string
    {
        $title = strtolower($title);
        $title = str_replace('\u00a0', ' ', $title);
        $title = str_replace("'", '', $title);

        return $title;
    }

    private function getLongestPart(string $title, string $pattern): string
    {
        // todo
        return '';
    }

    private function getPotentialTitles(string $title): array
    {
        $potential = [];
        $potential[$title] = true;

        $t = $this->getLongestPart($title, '/\s*[\|»|-]\s*/');
        if ($t) {
            $potential[$t] = true;
        }
        $t = $this->getLongestPart($title, '/\s*[\|»|:]\s*/');
        if ($t) {
            $potential[$t] = true;
        }
        $t = $this->getLongestPart($title, '/\s*[\|»|:\(\)]\s*/');
        if ($t) {
            $potential[$t] = true;
        }
        $t = $this->getLongestPart($title, '/\s*[\|»|:\(\)\-]\s*/');
        if ($t) {
            $potential[$t] = true;
        }
        $t = $this->getLongestPart($title, '/\s*[\|»|,|:\(\)\-]\s*/');
        if ($t) {
            $potential[$t] = true;
        }
        $t = $this->getLongestPart($title, '/\s*[\|»|,|:\(\)\-\u00a0]\s*/');
        if ($t) {
            $potential[$t] = true;
        }

        // todo
        // addPotentialTitles(potentialTitles, title, "[ ]+[\\|][ ]+", 4);
        // addPotentialTitles(potentialTitles, title, "[ ]+[\\-][ ]+", 4);

        $t = preg_replace('/ - [^\-]+$/', '', $title, 1);
        $potential[$t] = true;
        $t = preg_replace('/^[^\-]+ - /', '', $title, 1);
        $potential[$t] = true;

        return $potential;
    }

    public function process(TextDocument $doc): bool
    {
        $title = $this->clear($doc->getTitle());
        $potentialTitles = $this->getPotentialTitles($title);
        if (! $potentialTitles) {
            return false;
        }

        $hasChanges = false;
        foreach ($doc->getTextBlocks() as $tb) {
            $text = $this->clear($tb->getText());
            if (isset($potentialTitles[$text])) {
                $tb->addLabel(TextLabels::TITLE);
                $hasChanges = true;
                break;
            }

            $text = trim(preg_replace('/[?!.\-:]+/', '', $text));
            if (isset($potentialTitles[$text])) {
                $tb->addLabel(TextLabels::TITLE);
                $hasChanges = true;
                break;
            }
        }

        return $hasChanges;
    }
}
