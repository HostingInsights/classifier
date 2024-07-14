<?php

namespace Startwind\WebInsights\Classification\Classifier\Industry\Agency;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class AgencyCompanyClassifier implements Classifier
{
    public const CLASSIFIER_PREFIX = 'industry:agency';

    private $keywords = [
        'agentur',
        'agency'
    ];

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $title = $httpResponse->getHtmlDocument()->getTitle();

        foreach ($this->keywords as $keyword) {
            if (str_contains(strtolower($title), $keyword)) {
                return [self::CLASSIFIER_PREFIX];
            }
        }

        return [];

    }
}
