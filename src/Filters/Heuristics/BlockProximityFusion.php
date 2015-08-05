<?php

namespace DotPack\PhpBoilerPipe\Filters\Heuristics;

use DotPack\PhpBoilerPipe\Filters\IFilter;
use DotPack\PhpBoilerPipe\TextDocument;

class BlockProximityFusion implements IFilter
{
    protected $maxBlocksDistance;
    protected $contentOnly;
    protected $sameTagLevelOnly;

    public function __construct($maxBlocksDistance, $contentOnly = false, $sameTagLevelOnly = false)
    {
        $this->maxBlocksDistance = $maxBlocksDistance;
        $this->contentOnly = $contentOnly;
        $this->sameTagLevelOnly = $sameTagLevelOnly;
    }

    public function process(TextDocument $doc)
    {
        $textBlocks = $doc->getTextBlocks();
        if (count($textBlocks) < 2) return false;

        $changes = false;

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
            if (!$tb->isContent()) continue;

            $diffBlocks = $tb->getStartOffset() - $prevBlock->getEndOffset() - 1;
            if ($diffBlocks <= $this->maxBlocksDistance) {
                $ok = true;
                if ($this->contentOnly) {
                    if (!$prevBlock->isContent() || !$tb->isContent()) {
                        $ok = false;
                    }
                }
                if ($ok && $this->sameTagLevelOnly && $prevBlock->getLevel() != $tb->getLevel()) {
                    $ok = false;
                }
                if ($ok) {
                    $prevBlock->mergeNext($tb);
                    $doc->removeTextBlock($tb);
                    $changes = true;
                } else {
                    $prevBlock = $tb;
                }
            } else {
                $prevBlock = $tb;
            }
        }

        return $changes;
    }
}
