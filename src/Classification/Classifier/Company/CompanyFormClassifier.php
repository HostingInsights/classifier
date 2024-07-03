<?php

namespace Startwind\WebInsights\Classification\Classifier\Company;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;
use Startwind\WebInsights\Response\HttpResponse;

class CompanyFormClassifier implements Classifier
{
    const TAG_PREFIX = 'company:form:';

    const FORMS = [
        'GmbH' => 'gmbh',
        'UG' => 'ug',
        'Gbr' => 'gbr',
        'se' => 'se',
    ];

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $footer = $httpResponse->getHtmlDocument()->getFooter();

        if ($footer) {
            $form = $this->getForm($footer);
            if ($form) return [self::TAG_PREFIX . $form];
        }

        $form = $this->getForm($httpResponse->getHtmlDocument());
        if ($form) return [self::TAG_PREFIX . $form];

        return [];
    }

    private function getForm(HtmlDocument $document): string|false
    {
        foreach (self::FORMS as $key => $form) {
            if ($document->contains($form, false, HtmlDocument::SOURCE_HTML, true)) {
                return $key;
            }
        }

        return false;
    }
}
