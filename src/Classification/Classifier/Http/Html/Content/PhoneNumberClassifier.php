<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Content;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class PhoneNumberClassifier implements Classifier
{
    const TAG = 'html:content:phone:';

    public const PHONE_COUNTRY = [
        '49' => 'de',
        '43' => 'at',
        '41' => 'ch',
    ];

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $tags = [];

        $dom = $httpResponse->getHtmlDocument()->asDomDocument();

        $xpath = new \DOMXPath($dom);

        $query = '//a[starts-with(@href, "tel:")]';
        $entries = $xpath->query($query);

        foreach ($entries as $entry) {
            $href = $entry->getAttribute('href');
            $telNumber = substr($href, 4);

            $tags[] = self::TAG . $telNumber;
        }

        return $tags;
    }
}
