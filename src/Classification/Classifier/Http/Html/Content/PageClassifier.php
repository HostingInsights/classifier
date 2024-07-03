<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Content;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Helper\XPathHelper;
use Startwind\WebInsights\Response\HttpResponse;

class PageClassifier implements Classifier
{
    public const TAG_INTERNAL_LINK = 'html:content:pages:';
    public const TAG_BLOG = self::TAG_INTERNAL_LINK . 'blog';
    public const TAG_IMPRINT = self::TAG_INTERNAL_LINK . 'imprint';

    public const PATTERN_IMPRINT = [
        "//a[contains(@href, '/impressum')]",
        "//a[contains(@href, '/imprint')]"
    ];

    protected string $tagPrefix = self::TAG_INTERNAL_LINK;

    protected array $xPaths = [
        'blog' => ["//a[contains(@href, '/blog')]", "//a[text()='blog']", "//a[contains(@href, '.blog')]"],
        'forum' => ["//a[contains(@href, '/forum')]"],
        'imprint' => self::PATTERN_IMPRINT,
        'contact' => ["//a[contains(@href, '/contact')]", "//a[contains(@href, '/kontakt')]"],
        'price' => ["//a[contains(@href, '/price')]", "//a[contains(@href, '/pricing')]"],
        'about' => ["//a[contains(@href, '/ueber-uns')]", "//a[contains(@href, '/about')]"],
        'login' => ["//a[contains(@href, '/login')]", "//a[contains(@href, 'login.')]"],
    ];

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        return XPathHelper::exists(
            $httpResponse->getHtmlDocument(),
            $this->xPaths,
            $this->tagPrefix
        );
    }
}
