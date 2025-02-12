<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\Industry\Hosting;

use Startwind\WebInsights\Aggregation\Aggregator\CountingAggregator;
use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Classification\Classifier\Industry\Hosting\HostingProductsClassifier;

class HostingProductsAggregator extends CountingAggregator
{
    protected int $limit = 300;

    public function aggregate(ClassificationResult $classificationResult): void
    {
        $products = $classificationResult->getTagsStartingWithString(HostingProductsClassifier::TAG_PREFIX, true, true);

        foreach ($products as $product) {
            $this->increaseCount($product);
        }
    }
}
