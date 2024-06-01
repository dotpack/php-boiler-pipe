<?php

namespace Pforret\PhpArticleExtractor\Formats;

use Pforret\PhpArticleExtractor\Naming\TextLabels;

class HtmlContent
{
    private ?TextDocument $textDocument = null;

    private ?TextBlock $textBlock = null;

    private bool $isBody = false;

    private bool $isTitle = false;

    private bool $isAnchor = false;

    private string $title = '';

    private int $index = 0;

    private int $level = 0;

    private $block = null;

    private $tag = '';

    private array $labels = [
        'li' => [TextLabels::LI],
        'h1' => [TextLabels::HEADING, TextLabels::H1],
        'h2' => [TextLabels::HEADING, TextLabels::H2],
        'h3' => [TextLabels::HEADING, TextLabels::H3],
    ];

    protected function node(\DOMNode $element, int $level = 0, bool $isAnchor = false)
    {
        $tag = null;
        if ($element->nodeType == XML_ELEMENT_NODE) {
            $tag = strtolower($element->tagName);
            if ($tag == 'body') {
                $this->isBody = true;
            }
            $this->isTitle = ($tag == 'title');
        }

        if ($this->isBody) {
            if ($element->nodeType == XML_ELEMENT_NODE) {
                if ($tag == 'a') {
                    $href = $element->attributes->getNamedItem('href');
                    $isAnchor = $href ? $href->nodeValue : false;
                } else {
                    if ($this->textBlock) {
                        $this->textDocument->addTextBlock($this->textBlock);
                    }
                    $labels = $this->labels[$tag] ?? [];
                    $this->textBlock = new TextBlock($level, $labels);
                }
            } elseif ($element->nodeType == XML_TEXT_NODE) {
                $this->textBlock->addText($element->data, $isAnchor);
            }
        } elseif ($this->isTitle) {
            if ($element->nodeType == XML_TEXT_NODE) {
                $this->title .= $element->data;
            }
        }

        if ($element->childNodes) {
            foreach ($element->childNodes as $node) {
                $this->node($node, $level + 1, $isAnchor);
            }
        }
    }

    public function __construct(string $html)
    {
        $html = preg_replace('/<(span)(.*?)>/', '', $html);
        $html = preg_replace('/<\/(span)>/', '', $html);

        $this->textDocument = new TextDocument();

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        $this->node($dom->documentElement);

        if ($this->textBlock) {
            $this->textDocument->addTextBlock($this->textBlock);
        }
        $this->textDocument->setTitle($this->title);
    }

    final public function getTextDocument(): TextDocument
    {
        return $this->textDocument;
    }
}
