<?php

namespace Startwind\WebInsights\Classification;

use Psr\Http\Message\UriInterface;
use Startwind\WebInsights\Util\TagHelper;

class ClassificationResult
{
    private UriInterface $uri;
    private array $tags = [];

    public function __construct(UriInterface $uri)
    {
        $this->uri = $uri;
    }

    public function addTag(string $tag, bool $normalize = true): void
    {
        if ($normalize) {
            $this->tags[] = TagHelper::normalize($tag);
        } else {
            $this->tags[] = $tag;
        }
    }

    public function addTags(array $tags, bool $normalize = true): void
    {
        foreach ($tags as $tag) {
            if (is_array($tag)) continue;
            $this->addTag($tag, $normalize);
        }
    }

    public function getTags(): array
    {
        sort($this->tags);
        return array_unique($this->tags);
    }

    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->tags);
    }

    public function getTagsStartingWithString(string $startingString, bool $removeStartingString = false, bool $dissolveUnderscore = false): array
    {
        $tags = [];

        foreach ($this->tags as $tag) {
            if (str_starts_with($tag, $startingString)) {
                if ($removeStartingString) {
                    $newTag = str_replace($startingString, '', $tag);
                } else {
                    $newTag = $tag;
                }

                if ($dissolveUnderscore) {
                    $newTag = str_replace('_', ' ', $newTag);
                }

                if (trim($newTag) == "") continue;

                $tags[] = $newTag;
            }
        }

        return $tags;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }
}
