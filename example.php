<?php

require __DIR__ . '/vendor/autoload.php';

$path = "http://news.investors.com/073115-764464-electronic-arts-ea-stock-falls-on-weak-guidance.htm?ven=yahoocp&src=aurlled&ven=yahoo";
$data = file_get_contents($path);

$ae = new DotPack\PhpBoilerPipe\ArticleExtractor();
echo $ae->getContent($data) . "\n";