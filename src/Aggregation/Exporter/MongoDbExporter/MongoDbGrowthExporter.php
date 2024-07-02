<?php

namespace Startwind\WebInsights\Aggregation\Exporter\MongoDbExporter;

use MongoDB\Collection;
use Startwind\WebInsights\Aggregation\Exporter\FinishExporter;
use Startwind\WebInsights\Aggregation\UrlAwareAggregationResult;

class MongoDbGrowthExporter extends FinishExporter
{
    private string $uuid;
    private Collection $collection;

    private array $defaultOptions = [
        'outputDirectory' => '_results/report/default/',
    ];

    private string $outputDirectory;

    public function __construct(array $options = [])
    {
        // @todo param: with social
    }

    public function finish(int $numberOfProcessedWebsites): void
    {
        $urlList = [];

        foreach ($this->aggregationResults as $aggregationResult) {
            if ($aggregationResult instanceof UrlAwareAggregationResult) {
                $urls = $aggregationResult->getUrls();
                foreach ($urls['urls'] as $url) {
                    if (filter_var($url['url'], FILTER_VALIDATE_URL)) {
                        $urlList[] = $url['url'];
                    }
                }
            }
        }

        var_dump($urlList);
    }
}
