<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\Ranking;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\CountingAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\UrlAwareAggregationTrait;
use Startwind\WebInsights\Classification\ClassificationResult;

class MajesticRankAggregator extends CountingAggregator
{
    use UrlAwareAggregationTrait;

    private array $top20 = [];

    private int $minRank = 10000000000;

    private const TAG_PREFIX = 'majestic_rank:';

    public function aggregate(ClassificationResult $classificationResult): void
    {
        $tags = $classificationResult->getTagsStartingWithString(self::TAG_PREFIX);

        if (count($tags) > 0) {
            $rank = (int)str_replace(self::TAG_PREFIX, '', $tags[0]);

            if ($rank < $this->minRank || count($this->top20) < 21) {
                if (count($this->top20) > 19) {
                    $keys = array_keys($this->top20);
                    $this->minRank = (int)$keys[count($keys) - 1];
                    array_pop($this->top20);
                }

                $this->top20[$rank] = (string)$classificationResult->getUri();
                ksort($this->top20);

                // $this->minRank = (int)$this->top20[count($this->top20) - 1];
            }
        }
    }

    public function finish(): AggregationResult
    {
        return new AggregationResult(
            ['top20' => $this->top20],
            'The top 20 websites according to majestic rank',
            'majestic_rank',
            self::class
        );
    }
}
