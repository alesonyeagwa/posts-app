<?php

namespace App\JsonApi\Users;

use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'users';

    /**
     * @param \App\User $resource
     *      the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param \App\User $resource
     *      the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'username' => $resource->username,
            'createdAt' => $resource->created_at,
            'updatedAt' => $resource->updated_at,
        ];
    }

    public function getRelationships($resource, $isPrimary, array $includeRelationships)
    {
        return [
            'posts' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
            ],
            'comments' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
            ]
        ];
    }
}
