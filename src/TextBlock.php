<?php

namespace DotPack\PhpBoilerPipe;

class TextBlock
{
    protected $level = 0;
    protected $tag = '';
    protected $text = '';
    protected $texts = [];

    protected $wordCount = 0;
    protected $linkCount = 0;
    protected $linkWordCount = 0;

    protected $labels = [];

    protected $isContent = false;
    protected $startOffset = 0;
    protected $endOffset = 0;

    public function __construct($level = 0, array $labels = [])
    {
        $this->level = $level;
        if ($labels) foreach($labels as $label) $this->labels[$label] = true;
    }

    /**
     * @param int $startOffset
     */
    public function setStartOffset($startOffset)
    {
        $this->startOffset = $startOffset;
    }

    /**
     * @param int $endOffset
     */
    public function setEndOffset($endOffset)
    {
        $this->endOffset = $endOffset;
    }

    /**
     * @return int
     */
    public function getStartOffset()
    {
        return $this->startOffset;
    }

    /**
     * @return int
     */
    public function getEndOffset()
    {
        return $this->endOffset;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return array
     */
    public function getTexts()
    {
        return $this->texts;
    }

    /**
     * @return int
     */
    public function getLinkWordCount()
    {
        return $this->linkWordCount;
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return $this->labels;
    }

    protected function calcWordCount($text)
    {
        $words = $text;
        $words = preg_replace('/\s[\.\-]+\s/u', ' ', $words);
        $words = preg_replace('/[^\.\-\p{L}\p{Nd}\p{Nl}\p{No}]+/u', ' ', $words);
        $words = preg_replace('/\s+/', ' ', $words);
        $words = trim($words, " \t\n\r\0\x0B.-");
        $words = explode(' ', $words);
        return count($words);
    }

    public function addText($text, $link = false)
    {
        if (!trim($text)) return;

        $this->text .= $text;
        $this->texts[] = $link ? '<a href="' . $link. '">' . $text . '</a>' : $text;

        $wordCount = $this->calcWordCount($text);
        $this->wordCount += $wordCount;
        if ($link) {
            $this->linkCount++;
            $this->linkWordCount += $wordCount;
        }
    }

    public function mergeNext(TextBlock $block)
    {
        $this->text .= "\n" . $block->getText();
        $this->texts = $this->texts + $this->getTexts();

        $this->wordCount += $block->getWordCount();
        $this->linkCount += $block->getLinkCount();
        $this->linkWordCount = $block->getLinkWordCount();

        $this->startOffset = min($this->startOffset, $block->getStartOffset());
        $this->endOffset = max($this->endOffset, $block->getEndOffset());

        $this->isContent = $this->isContent || $block->isContent();
        $this->labels = $this->labels + $block->getLabels();

        $this->level = min($this->level, $block->getLevel());
    }

    public function addLabel($label)
    {
        $this->labels[$label] = true;
    }

    public function hasLabel($label)
    {
        return isset($this->labels[$label]);
    }

    public function getWordCount()
    {
        return $this->wordCount;
    }

    protected function getWrappedLines()
    {
        return explode(PHP_EOL, wordwrap($this->text, 80, PHP_EOL));
    }

    public function getWrappedLineCount()
    {
        return count($this->getWrappedLines());
    }

    public function getLineCount()
    {
        $lineCount = count($this->getWrappedLines()) - 1;
        return $lineCount ? $lineCount : 1;
    }

    public function getLinkCount()
    {
        return $this->linkCount;
    }

    public function getTextDensity()
    {
        $wrappedLines = $this->getWrappedLines();
        $numWrappedLines = count($wrappedLines);
        $numWordsInWrappedLines = $this->getWordCount();
        if ($numWrappedLines > 1) {
            $numWordsInWrappedLines = $numWordsInWrappedLines - $this->calcWordCount($wrappedLines[$numWrappedLines - 1]);
        }
        return $numWordsInWrappedLines / $this->getLineCount();
    }

    public function getLinkDensity()
    {
        return $this->linkWordCount ? $this->linkWordCount / $this->wordCount : 0;
    }

    public function isEmpty()
    {
        return !$this->texts;
    }

    public function setIsContent($value)
    {
        $result = ($this->isContent != $value);
        $this->isContent = $value;
        return $result;
    }

    public function isContent()
    {
        return $this->isContent;
    }

    public function getText()
    {
        return $this->text;
    }

    public function __toString()
    {
        return
            "[" . $this->startOffset . "-" . $this->endOffset .
            ";tl=" . $this->getLevel() .
            ";nw=" . $this->getWordCount() .
            ";td=" . $this->getTextDensity() .
            ";nwl=" . count($this->getWrappedLines()) .
            ";ld=" . $this->getLinkDensity() . "]" .
            "\t" . ($this->isContent ? "CONTENT" : "boilerplate") . "," . json_encode($this->labels) .
            "\n" . $this->getText();
    }
}