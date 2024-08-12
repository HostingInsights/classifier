<?php

namespace Startwind\WebInsights\Classification\Classifier\Company;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Industry\Restaurant\RestaurantCompanyClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class LocalBusinessClassifier implements Classifier
{
    const TAG_PREFIX = 'company:type:local-business';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->getHtmlDocument()->containsAny([
            'Öffnungszeiten', 'opening times',
            'Anfahrt'
        ])) {
            return [self::TAG_PREFIX];
        }

        if (in_array(RestaurantCompanyClassifier::CLASSIFIER_PREFIX, $existingTags)) {
            return [self::TAG_PREFIX];
        }

        return [];
    }
}
