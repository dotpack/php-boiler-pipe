<?php

namespace Pforret\PhpArticleExtractor\Filters\Heuristics;

use Pforret\PhpArticleExtractor\Filters\IFilter;
use Pforret\PhpArticleExtractor\Formats\TextDocument;

final class BlockProximityFusion implements IFilter
{
    private int $maxBlocksDistance;

    private bool $contentOnly;

    private bool $sameTagLevelOnly;

    public function __construct(int $maxBlocksDistance, bool $contentOnly = false, bool $sameTagLevelOnly = false)
    {
        $this->maxBlocksDistance = $maxBlocksDistance;
        $this->contentOnly = $contentOnly;
        $this->sameTagLevelOnly = $sameTagLevelOnly;
    }

    public function process(TextDocument $doc): bool
    {
        $textBlocks = $doc->getTextBlocks();
        if (count($textBlocks) < 2) {
            return false;
        }

        $hasChanges = false;

        $offset = 0;
        if ($this->contentOnly) {
            $prevBlock = null;
            foreach ($textBlocks as $tb) {
                $offset++;
                if ($tb->isContent()) {
                    $prevBlock = $tb;
                    break;
                }
            }
        } else {
            $prevBlock = $textBlocks[0];
            $offset = 1;
        }

        for ($i = $offset, $l = count($textBlocks); $i < $l; $i++) {
            $tb = $textBlocks[$i];
            if (! $tb->isContent()) {
                continue;
            }

            $diffBlocks = $tb->getStartOffset() - $prevBlock->getEndOffset() - 1;
            if ($diffBlocks <= $this->maxBlocksDistance) {
                $ok = true;
                if ($this->contentOnly) {
                    if (! $prevBlock->isContent() || ! $tb->isContent()) {
                        $ok = false;
                    }
                }
                if ($ok && $this->sameTagLevelOnly && $prevBlock->getLevel() != $tb->getLevel()) {
                    $ok = false;
                }
                if ($ok) {
                    $prevBlock->mergeNext($tb);
                    $doc->removeTextBlock($tb);
                    $hasChanges = true;
                } else {
                    $prevBlock = $tb;
                }
            } else {
                $prevBlock = $tb;
            }
        }

        return $hasChanges;
    }
}
