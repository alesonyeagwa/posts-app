<?php

namespace App\JsonApi\Posts;

use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'posts';

    //protected $defaultWith = ['comments'];

    /**
     * @param \App\Post $resource
     *      the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param \App\Post $resource
     *      the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'title' => $resource->title,
            'slug' => $resource->slug,
            'content' => $resource->content,
            'publishedAt' => $resource->published_at,
            'createdAt' => $resource->created_at,
            'updatedAt' => $resource->updated_at,
        ];
    }

    public function getRelationships($post, $isPrimary, array $includeRelationships)
    {
        return [
            'author' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
            ],
            'comments' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => true,
                self::DATA => function () use ($post) {
                    return $post->comments()->published()->orderBy('published_at', 'desc')->take(5)->get();
                },
                self::LINKS => [
                    'next' => $this->createLink(
                        "/posts/{$post->getRouteKey()}/comments?" . http_build_query([
                            'page' => ['number' => '2', 'size' => '5'],
                        ])
                    ),
                ],
            ]
        ];
    }

    public function getIncludePaths()
    {
        return ['comments'];
    }
}
