<?php

namespace Pforret\PhpArticleExtractor;

use Pforret\PhpArticleExtractor\Filters\English\IgnoreBlocksAfterContentFilter;
use Pforret\PhpArticleExtractor\Filters\English\NumWordsRulesClassifier;
use Pforret\PhpArticleExtractor\Filters\English\TerminatingBlocksFinder;
use Pforret\PhpArticleExtractor\Filters\Heuristics\BlockProximityFusion;
use Pforret\PhpArticleExtractor\Filters\Heuristics\DocumentTitleMatchClassifier;
use Pforret\PhpArticleExtractor\Filters\Heuristics\ExpandTitleToContentFilter;
use Pforret\PhpArticleExtractor\Filters\Heuristics\KeepLargestBlockFilter;
use Pforret\PhpArticleExtractor\Filters\Heuristics\LargeBlockSameTagLevelToContentFilter;
use Pforret\PhpArticleExtractor\Filters\Heuristics\ListAtEndFilter;
use Pforret\PhpArticleExtractor\Filters\Heuristics\TrailingHeadlineToBoilerplateFilter;
use Pforret\PhpArticleExtractor\Filters\Simple\BoilerplateBlockFilter;
use Pforret\PhpArticleExtractor\Formats\ArticleContents;
use Pforret\PhpArticleExtractor\Formats\HtmlContent;
use Pforret\PhpArticleExtractor\Formats\TextDocument;
use Pforret\PhpArticleExtractor\Naming\TextLabels;

final class ArticleExtractor
{
    private function process(TextDocument $doc): bool
    {
        return (new TerminatingBlocksFinder())->process($doc)
        | (new DocumentTitleMatchClassifier)->process($doc)
        | (new NumWordsRulesClassifier)->process($doc)
        | (new IgnoreBlocksAfterContentFilter(60))->process($doc)
        | (new TrailingHeadlineToBoilerplateFilter)->process($doc)
        | (new BlockProximityFusion(1))->process($doc)
        | (new BoilerplateBlockFilter(TextLabels::TITLE))->process($doc)
        | (new BlockProximityFusion(1, true, true))->process($doc)
        | (new KeepLargestBlockFilter(true, 150))->process($doc)
        | (new ExpandTitleToContentFilter)->process($doc)
        | (new LargeBlockSameTagLevelToContentFilter)->process($doc)
        | (new ListAtEndFilter)->process($doc);
    }

    public function getContent(string $html): string
    {
        $content = new HtmlContent($html);
        $document = $content->getTextDocument();

        $this->process($document);

        return $document->getContent();
    }

    public function getArticle(string $html): ArticleContents
    {
        $content = new HtmlContent($html);
        $document = $content->getTextDocument();

        $this->process($document);

        $article = new ArticleContents();
        $article->title = $document->getTitle();
        $article->content = $document->getContent();
        $article->images = $content->getImages();

        return $article;
    }
}
