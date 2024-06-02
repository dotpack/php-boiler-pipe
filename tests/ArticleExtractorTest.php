<?php

use Pforret\PhpArticleExtractor\ArticleExtractor;
use PHPUnit\Framework\TestCase;

final class ArticleExtractorTest extends TestCase
{
    public function testWordpress1Html(): void
    {
        $html = file_get_contents(__DIR__.'/examples/blog_wordpress1.html');
        $extractor = new ArticleExtractor();
        $content = $extractor->getContent($html);
        $this->assertEquals('Before she heads back to a galaxy far, f', substr($content, 0, 40));
        $this->assertEquals(1736, strlen($content));

    }

    public function testWordpress2Html(): void
    {
        $html = file_get_contents(__DIR__.'/examples/blog_wordpress2.html');
        $extractor = new ArticleExtractor();
        $content = $extractor->getContent($html);
        $this->assertEquals('NASA, Mission Partners to Update Media o', substr($content, 0, 40));
        $this->assertEquals(1383, strlen($content));

    }

    public function testWixHtml(): void
    {
        $html = file_get_contents(__DIR__.'/examples/blog_wix.html');
        $extractor = new ArticleExtractor();
        $content = $extractor->getContent($html);
        $this->assertEquals('Film Podcast: Wicked Little Letters Name', substr($content, 0, 40));
        $this->assertEquals(3294, strlen($content));

    }

    public function testDrupalHtml(): void
    {
        $html = file_get_contents(__DIR__.'/examples/blog_drupal2.html');
        $extractor = new ArticleExtractor();
        $content = $extractor->getContent($html);
        $this->assertEquals('Humanitarian aid in the Middle East Mess', substr($content, 0, 40));
        $this->assertEquals(564 , strlen($content));

    }

    public function testJekyllHtml(): void
    {
        $html = file_get_contents(__DIR__.'/examples/blog_jekyll.html');
        $extractor = new ArticleExtractor();
        $content = $extractor->getContent($html);
        $this->assertEquals('I recently upgraded my Ubiquiti Wi-Fi in', substr($content, 0, 40));
        $this->assertStringContainsString('Cloud Key Gen 2', $content);
        $this->assertEquals(4242 , strlen($content));

    }

    public function testMkdocsHtml(): void
    {
        $html = file_get_contents(__DIR__.'/examples/blog_mkdocs.html');
        $extractor = new ArticleExtractor();
        $content = $extractor->getContent($html);
        $this->assertEquals('This is part 2 in the “what’s new in', substr($content, 0, 40));
        $this->assertStringContainsString('override the terminal size', $content);
        $this->assertEquals(5002 , strlen($content));

    }

    public function testBloggerHtml(): void
    {
        $html = file_get_contents(__DIR__.'/examples/blog_blogger.html');
        $extractor = new ArticleExtractor();
        $content = $extractor->getContent($html);
        $this->assertEquals('I hope the examples above have opened yo', substr($content, 0, 40));
        $this->assertStringContainsString('Google Docs, AdSense', $content);
        $this->assertEquals(2607 , strlen($content));

    }
}
