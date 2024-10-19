<?php

namespace Startwind\WebInsights\Classification\Classifier\Industry\Hosting;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class WordPressHostingClassifier implements Classifier
{
    public const CLASSIFIER_PREFIX = 'special:wordpress-hosting';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->getHtmlDocument()->containsAny([
            'WordPress Hosting'
        ])) {
            return [self::CLASSIFIER_PREFIX];
        }

        return [];
    }
}
