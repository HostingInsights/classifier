<?php

namespace Startwind\WebInsights\Classification\Classifier\Industry\Restaurant;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class RestaurantCompanyClassifier implements Classifier
{
    public const CLASSIFIER_PREFIX = 'industry:restaurant';

    private array $keywords = [
        "restaurant",
    ];

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $title = $httpResponse->getHtmlDocument()->getTitle();
        $metaDescription = $httpResponse->getHtmlDocument()->getMetaDescription();

        $domain = (string)$httpResponse->getRequestUri();

        foreach ($this->keywords as $keyword) {
            $keyword = strtolower($keyword);

            if (str_contains(strtolower($title), $keyword)
                || str_contains(strtolower($metaDescription), $keyword)
                || str_contains(strtolower($domain), $keyword)) {
                return [self::CLASSIFIER_PREFIX];
            }
        }

        return [];

    }
}
