<?php

namespace Startwind\WebInsights\Classification\Classifier\Url;

use Psr\Http\Message\UriInterface;
use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\UrlClassifier;

class TopLevelDomainClassifier extends UrlClassifier
{
    public const CLASSIFIER_PREFIX = 'tld' . Classifier::TAG_SEPARATOR;

    public const TLD_COUNTRY = [
        'de' => 'de',
        'at' => 'at',
        'ch' => 'ch',
    ];

    protected function doClassification(UriInterface $uri, array $existingTags): array
    {
        $urlParts = pathinfo($uri->getHost());
        return [self::CLASSIFIER_PREFIX . $urlParts['extension']];
    }
}
