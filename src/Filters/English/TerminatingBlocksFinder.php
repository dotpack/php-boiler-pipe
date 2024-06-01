<?php

namespace Pforret\PhpArticleExtractor\Filters\English;

use Pforret\PhpArticleExtractor\Filters\IFilter;
use Pforret\PhpArticleExtractor\Formats\TextDocument;
use Pforret\PhpArticleExtractor\Naming\TextLabels;

final class TerminatingBlocksFinder implements IFilter
{
    public static function startWithNumber(string $haystack, string $needle): bool
    {
        return preg_match("/^\d+$needle/", $haystack);
    }

    public function process(TextDocument $doc): bool
    {
        $hasChanges = false;
        foreach ($doc->getTextBlocks() as $tb) {
            $result = false;
            $wordCount = $tb->getWordCount();
            if ($wordCount < 15) {
                $text = strtolower(trim($tb->getText()));
                $length = mb_strlen($text);
                if ($length > 7) {
                    $result
                        = ($text === 'thanks for your comments - this feedback is now closed')
                        || str_starts_with($text, 'comments')
                        || str_starts_with($text, '© reuters')
                        || str_starts_with($text, 'please rate this')
                        || str_starts_with($text, 'post a comment')
                        || self::startWithNumber($text, ' comments')
                        || self::startWithNumber($text, ' users responded in')
                        || str_contains($text, 'what you think...')
                        || str_contains($text, 'add your comment')
                        || str_contains($text, 'add comment')
                        || str_contains($text, 'reader views')
                        || str_contains($text, 'have your say')
                        || str_contains($text, 'reader comments')
                        || str_contains($text, 'rätta artikeln');
                } elseif ($tb->getLinkDensity() == 1) {
                    $result = ($text == 'comment');
                }
            }
            if ($result) {
                $tb->addLabel(TextLabels::INDICATES_END_OF_TEXT);
            }
            $hasChanges = $hasChanges || $result;
        }

        return $hasChanges;
    }
}
