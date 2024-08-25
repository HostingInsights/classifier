<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Content\Social;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\Content\SocialMediaClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class SocialMediaCountClassifier implements Classifier
{
    public const TAG_MULTIPLE = 'social-media:multiple';
    public const TAG_EXTREME = 'social-media:extensive';

    public const TAG_PREFIX = 'social-media:count:';

    public function classify(HttpResponse $httpResponse, array $existingTags = []): array
    {
        $tags = [];

        $count = 0;

        foreach($existingTags as $existingTag) {
            if(str_starts_with($existingTag, SocialMediaClassifier::TAG)) {
                var_dump('hier');
                $count++;
            }
        }
        
        if($count > 1) {
            $tags[] = self::TAG_MULTIPLE;
        }

                
        if($count > 2) {
            $tags[] = self::TAG_EXTREME;
        }

        if($count > 0) {
            $tags[] = self::TAG_PREFIX . $count;
        }

        return $tags;
    }
}
