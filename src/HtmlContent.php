<?php

namespace DotPack\PhpBoilerPipe;

class HtmlContent
{
    /**
     * @var TextDocument
     */
    protected $textDocument = null;

    /**
     * @var TextBlock
     */
    protected $textBlock = null;

    protected $isBody = false;
    protected $isTitle = false;
    protected $isAnchor = false;

    protected $title = '';
    protected $index = 0;
    protected $level = 0;
    protected $block = null;
    protected $tag = '';

    protected $labels = [
        'li' => [TextLabels::LI],
        'h1' => [TextLabels::HEADING, TextLabels::H1],
        'h2' => [TextLabels::HEADING, TextLabels::H2],
        'h3' => [TextLabels::HEADING, TextLabels::H3],
    ];

    protected function node(\DOMNode $element, $level = 0, $isAnchor = false)
    {
        $tag = null;
        if ($element->nodeType == XML_ELEMENT_NODE) {
            $tag = strtolower($element->tagName);
            if ($tag == 'body') $this->isBody = true;
            $this->isTitle = ('title' == $tag);
        }

        if ($this->isBody) {
            if ($element->nodeType == XML_ELEMENT_NODE) {
                if ('a' == $tag) {
                    $href = $element->attributes->getNamedItem('href');
                    $isAnchor = $href ? $href->nodeValue : false;
                } else {
                    if ($this->textBlock) $this->textDocument->addTextBlock($this->textBlock);
                    $labels = isset($this->labels[$tag]) ? $this->labels[$tag] : [];
                    $this->textBlock = new TextBlock($level, $labels);
                }
            } else if ($element->nodeType == XML_TEXT_NODE) {
                $this->textBlock->addText($element->data, $isAnchor);
            }
        } else if ($this->isTitle) {
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

    public function __construct($html)
    {
        $html = preg_replace('/<(span)(.*?)>/', '', $html);
        $html = preg_replace('/<\/(span)>/', '', $html);

        $this->textDocument = new TextDocument();

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        $this->node($dom->documentElement);

        if ($this->textBlock) $this->textDocument->addTextBlock($this->textBlock);
        $this->textDocument->setTitle($this->title);
    }

    /**
     * @return TextDocument
     */
    public function getTextDocument()
    {
        return $this->textDocument;
    }
}