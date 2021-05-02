<?php

namespace App\JsonApi\Users;

use App\Models\Comment;
use App\Scopes\CommentScope;
use App\Scopes\PublishedResourceScope;
use CloudCreativity\LaravelJsonApi\Auth\AbstractAuthorizer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Authorizer extends AbstractAuthorizer
{

    //protected $guards = [];
    /**
     * Authorize a resource index request.
     *
     * @param string $type
     *      the domain record type.
     * @param Request $request
     *      the inbound request.
     * @return void
     * @throws AuthenticationException|AuthorizationException
     *      if the request is not authorized.
     */
    public function index($type, $request)
    {
        $this->can('viewAny', $type);
    }

    /**
     * Authorize a resource create request.
     *
     * @param string $type
     *      the domain record type.
     * @param Request $request
     *      the inbound request.
     * @return void
     * @throws AuthenticationException|AuthorizationException
     *      if the request is not authorized.
     */
    public function create($type, $request)
    {
        // TODO: Implement create() method.
        $this->can('create', $type);
    }

    /**
     * Authorize a resource read request.
     *
     * @param object $record
     *      the domain record.
     * @param Request $request
     *      the inbound request.
     * @return void
     * @throws AuthenticationException|AuthorizationException
     *      if the request is not authorized.
     */
    public function read($record, $request)
    {
        // TODO: Implement read() method.
        $this->can('view', $record);
    }

    /**
     * Authorize a resource update request.
     *
     * @param object $record
     *      the domain record.
     * @param Request $request
     *      the inbound request.
     * @return void
     * @throws AuthenticationException|AuthorizationException
     *      if the request is not authorized.
     */
    public function update($record, $request)
    {
        // TODO: Implement update() method.
        $this->can('update', $record);
    }

    /**
     * Authorize a resource read request.
     *
     * @param object $record
     *      the domain record.
     * @param Request $request
     *      the inbound request.
     * @return void
     * @throws AuthenticationException|AuthorizationException
     *      if the request is not authorized.
     */
    public function delete($record, $request)
    {
        // TODO: Implement delete() method.
        $this->can('delete', $record);
    }

    public function readRelationship($record, $field, $request)
    {
        try {
            $this->authenticate();
        } catch (\Throwable $th) {
        }
    }
    public function modifyRelationship($record, $field, $request)
    {
        return false;
    }
}
