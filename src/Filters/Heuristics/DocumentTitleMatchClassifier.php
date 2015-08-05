<?php

namespace DotPack\PhpBoilerPipe\Filters\Heuristics;

use DotPack\PhpBoilerPipe\Filters\IFilter;
use DotPack\PhpBoilerPipe\TextDocument;
use DotPack\PhpBoilerPipe\TextLabels;

// todo
class DocumentTitleMatchClassifier implements IFilter
{
    protected function clear($title)
    {
        $title = strtolower($title);
        $title = str_replace('\u00a0', ' ', $title);
        $title = str_replace("'", '', $title);
        return $title;
    }

    protected function getLongestPart($title, $pattern)
    {
        // todo
        return '';
    }

    protected function getPotentialTitles($title)
    {
        $potential = [];
        $potential[$title] = true;

        $t = $this->getLongestPart($title, '/\s*[\|»|-]\s*/'); if ($t) $potential[$t] = true;
        $t = $this->getLongestPart($title, '/\s*[\|»|:]\s*/'); if ($t) $potential[$t] = true;
        $t = $this->getLongestPart($title, '/\s*[\|»|:\(\)]\s*/'); if ($t) $potential[$t] = true;
        $t = $this->getLongestPart($title, '/\s*[\|»|:\(\)\-]\s*/'); if ($t) $potential[$t] = true;
        $t = $this->getLongestPart($title, '/\s*[\|»|,|:\(\)\-]\s*/'); if ($t) $potential[$t] = true;
        $t = $this->getLongestPart($title, '/\s*[\|»|,|:\(\)\-\u00a0]\s*/'); if ($t) $potential[$t] = true;

        // todo
        // addPotentialTitles(potentialTitles, title, "[ ]+[\\|][ ]+", 4);
        // addPotentialTitles(potentialTitles, title, "[ ]+[\\-][ ]+", 4);

        $t = preg_replace('/ - [^\-]+$/', '', $title, 1); $potential[$t] = true;
        $t = preg_replace('/^[^\-]+ - /', '', $title, 1); $potential[$t] = true;

        return $potential;
    }

    public function process(TextDocument $doc)
    {
        $title = $this->clear($doc->getTitle());
        $potentialTitles = $this->getPotentialTitles($title);
        if (!$potentialTitles) return false;

        $change = false;
        foreach ($doc->getTextBlocks() as $tb) {
            $text = $this->clear($tb->getText());
            if (isset($potentialTitles[$text])) {
                $tb->addLabel(TextLabels::TITLE);
                $change = true;
                break;
            }

            $text = trim(preg_replace('/[\?\!\.\-\:]+/', '', $text));
            if (isset($potentialTitles[$text])) {
                $tb->addLabel(TextLabels::TITLE);
                $change = true;
                break;
            }
        }
        return $change;
    }
}
