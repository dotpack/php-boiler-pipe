# PhpBoilerPipe

## Project Archived

This project is no longer maintained. Please refer to [pforret/php-article-extractor](https://github.com/pforret/php-article-extractor) for further updates and continued development.

Thank you for your support!

---

Boilerplate Removal and Fulltext Extraction from HTML pages.

Partial implementation of [https://github.com/kohlschutter/boilerpipe](https://github.com/kohlschutter/boilerpipe) in PHP. Requires PHP >= 5.4.

## Example

``` php
# html
$path = "http://example.com/some-article.html";
$data = file_get_contents($path);

# code
$ae = new DotPack\PhpBoilerPipe\ArticleExtractor();
echo $ae->getContent($data) . "\n";
```
