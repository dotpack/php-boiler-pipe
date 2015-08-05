<?php

namespace DotPack\PhpBoilerPipe\Filters\English;

use DotPack\PhpBoilerPipe\Filters\IFilter;
use DotPack\PhpBoilerPipe\TextDocument;
use DotPack\PhpBoilerPipe\TextLabels;

class TerminatingBlocksFinder implements IFilter
{
    protected function startWithNumber($haystack, array $needles = [])
    {
        // todo
        return false;
    }

    protected function startWith($haystack, $needle)
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }

    protected function contains($haystack, $needle)
    {
        return strpos($haystack, $needle) > -1;
    }

    protected function equals($haystack, $needle)
    {
        return $haystack == $needle;
    }

    public function process(TextDocument $doc)
    {
        $change = false;
        foreach ($doc->getTextBlocks() as $tb) {
            $result = false;
            $wordCount = $tb->getWordCount();
            if ($wordCount < 15) {
                $text = strtolower(trim($tb->getText()));
                $length = mb_strlen($text);
                if ($length > 7) {
                    $result
                        = ("thanks for your comments - this feedback is now closed" === $text)
                        || $this->startWith($text, "comments")
                        || $this->startWith($text, "© reuters")
                        || $this->startWith($text, "please rate this")
                        || $this->startWith($text, "post a comment")
                        || $this->startWithNumber($text, [" comments", " users responded in"])
                        || $this->contains($text, "what you think...")
                        || $this->contains($text, "add your comment")
                        || $this->contains($text, "add comment")
                        || $this->contains($text, "reader views")
                        || $this->contains($text, "have your say")
                        || $this->contains($text, "reader comments")
                        || $this->contains($text, "rätta artikeln")
                    ;
                } else if (1 == $tb->getLinkDensity()) {
                    $result = ("comment" == $text);
                }
            }
            if ($result) $tb->addLabel(TextLabels::INDICATES_END_OF_TEXT);
            $change = $change || $result;
        }
        return $change;
    }
}

