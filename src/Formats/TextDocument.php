<?php

namespace Pforret\PhpArticleExtractor\Formats;

final class TextDocument
{
    // TextDocument is an array of TextBlocks
    private string $title = '';

    private int $offset = 0;

    /**
     * @var TextBlock[]
     */
    private array $textBlocks;

    public function __construct(array $textBlocks = [])
    {
        $this->textBlocks = $textBlocks;
    }

    public function setTitle(string $title): self
    {
        $this->title = trim($title);

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTextBlocks(): array
    {
        return $this->textBlocks;
    }

    public function addTextBlock(TextBlock $block): self
    {
        if (! $block->isEmpty()) {
            $block->setStartOffset($this->offset);
            $block->setEndOffset($this->offset);
            $this->textBlocks[] = $block;
            $this->offset++;
        }

        return $this;
    }

    public function removeTextBlock(TextBlock $block): self
    {
        $index = array_search($block, $this->textBlocks);
        array_splice($this->textBlocks, $index, 1);

        return $this;
    }

    public function getContent(): string
    {
        return $this->getText(true, false);
    }

    public function getText(bool $includeContent, bool $includeNonContent): string
    {
        $result = '';
        foreach ($this->textBlocks as $block) {
            if ($block->isContent() && ! $includeContent) {
                continue;
            }
            if (! $block->isContent() && ! $includeNonContent) {
                continue;
            }
            $result .= $block->getText();
        }

        return $result;
    }

    public function __toString(): string
    {
        $result = trim($this->getTitle())."\n";
        foreach ($this->textBlocks as $block) {
            $result .= trim($block).' ';
        }

        return $result;
    }
}
