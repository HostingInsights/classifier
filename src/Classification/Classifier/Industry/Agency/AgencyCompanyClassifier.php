<?php

namespace Startwind\WebInsights\Classification\Classifier\Industry\Agency;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class AgencyCompanyClassifier implements Classifier
{
    public const CLASSIFIER_PREFIX = 'industry:agency';

    private $keywords = [
        "agentur",
        "agency",
        "agencia",
        "agence",
        "agenzia",
        "agência",
        "Агентство",
        "机构 / 代理机构",
        "代理店",
        "대리점",
        "وكالة",
        "Ajans",
        "एजेंसी",
        "এজেন্সি",
        "agentschap",
        "Πρακτορείο",
        "Byrå",
        "agencja",
        "Ügynökség",
        "agenție"
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
