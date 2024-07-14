<?php

namespace Startwind\WebInsights\Classification\Classifier\Company;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\Content\PhoneNumberClassifier;
use Startwind\WebInsights\Classification\Classifier\Url\TopLevelDomainClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;
use Startwind\WebInsights\Response\HttpResponse;
use Startwind\WebInsights\Util\TagHelper;

class CompanyLocationClassifier implements Classifier
{
    const TAG_PREFIX = 'company:location:country:';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $tags = [];

        $tags = array_merge($tags, $this->classifyByPhoneNumber($existingTags));
        $tags = array_merge($tags, $this->classifyByDomain($existingTags));

        return $tags;
    }

    private function classifyByPhoneNumber(array $existingTags): array
    {
        $tags = [];

        $phoneNumbers = TagHelper::startsWith($existingTags, PhoneNumberClassifier::TAG);

        foreach ($phoneNumbers as $phoneNumber) {
            foreach (PhoneNumberClassifier::PHONE_COUNTRY as $prefix => $countryCode) {
                if (str_starts_with($phoneNumber, '+' . $prefix)) $tags[] = self::TAG_PREFIX . $countryCode;;
            }
        }

        return $tags;
    }

    private function classifyByDomain(array $existingTags): array
    {
        $tlds = TagHelper::startsWith($existingTags, TopLevelDomainClassifier::CLASSIFIER_PREFIX);

        foreach ($tlds as $tld) {
            if (array_key_exists($tld, TopLevelDomainClassifier::TLD_COUNTRY)) {
                return [self::TAG_PREFIX . TopLevelDomainClassifier::TLD_COUNTRY[$tld]];
            }
        }

        return [];
    }
}
