<?php

use Pforret\PhpArticleExtractor\ArticleExtractor;
use PHPUnit\Framework\TestCase;

final class ArticleExtractorTest extends TestCase
{
    public function testWordpressHtml(): void
    {
        $html = file_get_contents(__DIR__.'/examples/blog_wordpress1.html');
        $extractor = new ArticleExtractor();
        $content = $extractor->getContent($html);
        $this->assertEquals('Before she heads back to a galaxy far, f', substr($content, 0, 40));

    }

    public function testWixHtml(): void
    {
        $html = file_get_contents(__DIR__.'/examples/blog_wix.html');
        $extractor = new ArticleExtractor();
        $content = $extractor->getContent($html);
        $this->assertEquals('Film Podcast: Wicked Little Letters Name', substr($content, 0, 40));

    }

    public function testDrupalHtml(): void
    {
        $html = file_get_contents(__DIR__.'/examples/blog_drupal.html');
        $extractor = new ArticleExtractor();
        $content = $extractor->getContent($html);
        //$this->assertEquals('4. Expand your reach with creators and ', substr($content, 0, 40));
        $this->assertStringContainsString('The post 4. Expand your reach with creators and influencers appeared first on', $content);

    }

    public function testJekyllHtml(): void
    {
        $html = file_get_contents(__DIR__.'/examples/blog_jekyll.html');
        $extractor = new ArticleExtractor();
        $content = $extractor->getContent($html);
        $this->assertEquals('I recently upgraded my Ubiquiti Wi-Fi in', substr($content, 0, 40));
        $this->assertStringContainsString('Cloud Key Gen 2', $content);

    }

    public function testMkdocsHtml(): void
    {
        $html = file_get_contents(__DIR__.'/examples/blog_mkdocs.html');
        $extractor = new ArticleExtractor();
        $content = $extractor->getContent($html);
        $this->assertEquals('This is part 2 in the “what’s new in', substr($content, 0, 40));
        $this->assertStringContainsString('override the terminal size', $content);

    }

    public function testBloggerHtml(): void
    {
        $html = file_get_contents(__DIR__.'/examples/blog_blogger.html');
        $extractor = new ArticleExtractor();
        $content = $extractor->getContent($html);
        $this->assertEquals('Guest post by David Kutcher Editor\'s', substr($content, 0, 40));
        $this->assertStringContainsString('override the terminal size', $content);

    }
}
