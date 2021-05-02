<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Scopes\PublishedResourceScope;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UsersController extends JsonApiController
{
    public function readingComments($record, $request){
        $response = Gate::inspect('update', $record);
        if($response->denied()){
            Comment::addGlobalScope(new PublishedResourceScope);
        }
    }

    public function readingPosts($record, $request){
        $response = Gate::inspect('update', $record);
        if($response->denied()){
            Post::addGlobalScope(new PublishedResourceScope);
        }
    }
}
