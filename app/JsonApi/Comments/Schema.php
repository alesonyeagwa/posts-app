<?php

namespace App\JsonApi\Comments;

use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'comments';

    /**
     * @param \App\Comment $resource
     *      the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param \App\Comment $resource
     *      the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'comment' => $resource->comment,
            'publishedAt' => $resource->published_at,
            'createdAt' => $resource->created_at,
            'updatedAt' => $resource->updated_at,
        ];
    }

    public function getRelationships($resource, $isPrimary, array $includeRelationships)
    {
        return [
            'commenter' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
            ],
            'post' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
            ]
        ];
    }
}
