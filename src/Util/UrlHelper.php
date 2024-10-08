<?php

namespace Startwind\WebInsights\Util;

use Psr\Http\Message\UriInterface;

abstract class UrlHelper
{
    static public function getDomain(UriInterface $uri): string
    {
        $host_with_subdomain = $uri->getHost();
        $array = explode(".", $host_with_subdomain);

        /*if (in_array($array[1], ['com', 'co', 'net', 'edu', 'gov', 'org'])) {
            if (count($array) > 2) {
                return $array[count($array) - 2] . '.' . $array[count($array) - 1];
            }
        }*/

        return (array_key_exists(count($array) - 2, $array) ? $array[count($array) - 2] : "") . "." . $array[count($array) - 1];
    }

    static public function normalizeUrl(string $string): string
    {
        $url = self::removeGetParameters($string);

        if (substr_count($url, '/') === 3) {
            $url = rtrim($url, '/');
        }

        return $url;
    }

    static public function removeGetParameters(string $string): string
    {
        $parsedUrl = parse_url($string);

        $urlWithoutQuery = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

        if (isset($parsedUrl['path'])) {
            $urlWithoutQuery .= $parsedUrl['path'];
        }

        return $urlWithoutQuery;
    }

    static public function rootUri(UriInterface $uri): UriInterface
    {
        return $uri->withPath('')->withQuery('');
    }
}
