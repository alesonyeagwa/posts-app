<?php

namespace App\JsonApi\Comments;

use App\Models\Comment;
use App\Models\Post;
use App\Scopes\CommentScope;
use CloudCreativity\LaravelJsonApi\Eloquent\AbstractAdapter;
use CloudCreativity\LaravelJsonApi\Pagination\StandardStrategy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use CloudCreativity\LaravelJsonApi\Document\ResourceObject;

class Adapter extends AbstractAdapter
{

    /**
     * Mapping of JSON API attribute field names to model keys.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Mapping of JSON API filter names to model scopes.
     *
     * @var array
     */
    protected $filterScopes = [];

    
    protected $defaultPagination = ['size' => 5];

    protected $defaultSort = ['-createdAt', '-publishedAt'];

    /**
     * Adapter constructor.
     *
     * @param StandardStrategy $paging
     */
    public function __construct(StandardStrategy $paging)
    {
        parent::__construct(new \App\Models\Comment(), $paging);
        //$this->addScopes($scope);
    }

    /**
     * @param Builder $query
     * @param Collection $filters
     * @return void
     */
    protected function filter($query, Collection $filters)
    {
        //$query->where('published_at', '!=', null);
        //$this->filterWithScopes($query, $filters);
    }

    protected function fillAttributes($record, Collection $attributes)
    {
        
    }

    protected function commenter()
    {
        return $this->hasOne();
    }
    
    protected function post()
    {
        return $this->hasOne();
    }

    protected function creating(Comment $comment, ResourceObject $data): void
    {
        $comment->comment = $data->getAttributes()['comment'];
        $this->cuComment($comment, $data);
    }

    protected function updating(Comment $comment, ResourceObject $data): void
    {
        $this->cuComment($comment, $data);
    }

    /**
     * Actions when creating or updating a comment.
     *
     * @param Post $post
     * @param ResourceObject $data
     */
    private function cuComment(Comment $comment, ResourceObject $data): void
    {
        $attributes = $data->getAttributes();
        $isPublished = $attributes->get('isPublished');
        if(!empty($isPublished)){
            $comment->published_at = $isPublished == '1' ? \Carbon\Carbon::now() : null;
        }
        
        if($comment->id == null){
            $post = Post::find($attributes->get("post_id"));
            $comment->commenter()->associate(Auth::user());
            $comment->post()->associate($post);
        }
    }
}
