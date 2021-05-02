<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Scopes\PublishedResourceScope;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PostsController extends JsonApiController
{
    public function readingComments($record, $request){
        
        $response = Gate::inspect('update', $record);
        if($response->denied()){
            if($record->published_at == null){
                abort(404, "Resource posts with id {$record->getRouteKey()} does not exist.");
            }
        }

        Comment::addGlobalScope(function(Builder $builder) use ($record){
            $build = $builder->where('published_at', '!=', null);
            if(Auth::check()){
                $build->union(DB::table('comments')->where('post_id', $record->id)
                ->where(function($query){
                    $query->where('commenter_id', Auth::user()->id)
                            ->where('published_at', null);
                }));
            }
        });
    }
}
