<?php

namespace Startwind\WebInsights\Util;

abstract class TagHelper
{
    static public function startsWith(array $tags, string $prefix, $removePrefix = true): array
    {
        $tagsContaining = [];

        foreach ($tags as $tag) {
            if (str_starts_with($tag, $prefix)) {
                if ($removePrefix) {
                    $tagsContaining[] = str_replace($prefix, '', $tag);
                } else {
                    $tagsContaining[] = $tag;
                }
            }
        }

        return $tagsContaining;
    }

    static public function normalize(string $string): string
    {
        $string = strtolower($string);
        $string = str_replace(' ', '_', $string);
        $string = str_replace(',', '_', $string);
        $string = str_replace('.', '_', $string);

        $string = str_replace('___', '_', $string);
        $string = str_replace('__', '_', $string);

        $string = trim($string);
        $string = trim($string, '_');

        return $string;
    }
}
