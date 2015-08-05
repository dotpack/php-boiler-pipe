<?php

namespace DotPack\PhpBoilerPipe;

class TextDocument
{
    protected $title = '';
    protected $offset = 0;

    /**
     * @var TextBlock[]
     */
    protected $textBlocks;

    public function __construct(array $textBlocks = [])
    {
        $this->textBlocks = $textBlocks;
    }

    public function setTitle($title)
    {
        $this->title = trim($title);
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getTextBlocks()
    {
        return $this->textBlocks;
    }

    public function addTextBlock(TextBlock $block)
    {
        if ($block->isEmpty()) return;
        $block->setStartOffset($this->offset);
        $block->setEndOffset($this->offset);
        $this->textBlocks[] = $block;
        $this->offset++;
    }

    public function removeTextBlock(TextBlock $block)
    {
        $index = array_search($block, $this->textBlocks);
        array_splice($this->textBlocks, $index, 1);
    }

    public function getContent()
    {
        return $this->getText(true, false);
    }

    public function getText($includeContent, $includeNonContent)
    {
        $result = '';
        foreach ($this->textBlocks as $block) {
            if ($block->isContent() && !$includeContent) continue;
            if (!$block->isContent() && !$includeNonContent) continue;
            $result .= $block->getText() . "\n";
        }
        return $result;
    }

    public function __toString()
    {
        $result = $this->getTitle() . "\n";
        foreach ($this->textBlocks as $block) {
            $result .= $block . "\n";
        }
        return $result;
    }
}