<?php

namespace Startwind\WebInsights\Classification\Classifier\Industry\Agency;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class FreelancerCompanyClassifier implements Classifier
{
    public const CLASSIFIER_PREFIX = 'industry:freelancer';

    private array $keywords = [
        "freelancer"
    ];

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $title = $httpResponse->getHtmlDocument()->getTitle();
        $metaDescription = $httpResponse->getHtmlDocument()->getMetaDescription();

        foreach ($this->keywords as $keyword) {
            $keyword = strtolower($keyword);

            if (str_contains(strtolower($title), $keyword)
                || str_contains(strtolower($metaDescription), $keyword)) {
                return [self::CLASSIFIER_PREFIX];
            }
        }

        return [];

    }
}
