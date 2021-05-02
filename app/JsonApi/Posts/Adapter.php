<?php

namespace App\JsonApi\Posts;

use App\Models\Post;
use CloudCreativity\LaravelJsonApi\Eloquent\AbstractAdapter;
use CloudCreativity\LaravelJsonApi\Pagination\StandardStrategy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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
    
    protected $defaultSort = ['-publishedAt', '-createdAt'];

    /**
     * Adapter constructor.
     *
     * @param StandardStrategy $paging
     */
    public function __construct(StandardStrategy $paging)
    {
        parent::__construct(new \App\Models\Post(), $paging);
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

    protected function author()
    {
        return $this->hasOne();
    }
    
    protected function comments()
    {
        return $this->hasMany();
    }


    /**
     * @param Post $post
     * @param ResourceObject $data
     */
    protected function creating(Post $post, ResourceObject $data): void
    {
        $this->cuPost($post, $data);
    }

    /**
     * @param Post $post
     * @param ResourceObject $data
     */
    protected function updating(Post $post, ResourceObject $data): void
    {
        $this->cuPost($post, $data);
    }

    /**
     * Actions when creating or updating a post.
     *
     * @param Post $post
     * @param ResourceObject $data
     */
    private function cuPost(Post $post, ResourceObject $data): void
    {
        $attributes = $data->getAttributes();
        if(!empty($attributes->get('isPublished'))){
            $post->published_at = $attributes->get('isPublished') == '1' ? \Carbon\Carbon::now() : null;
        }

        if($post->id == null){
            $post->slug = $this->generatePostSlug($post->title);
            $post->author()->associate(Auth::user());
        }else{
            //Don't change the slug if the title is the same
            if(!empty($attributes->get('title'))){
                $post->slug =  $attributes->get('title') == $post->title ? $post->slug : $this->generatePostSlug($post->title);
            }
        }
    }

    private function generatePostSlug($title){
        $slug = Str::slug($title);

        $check = Post::where('slug', $slug)->first();
        if ($check) {
            $randStr = Str::random(10);
            return $this->generatePostSlug("{$title} {$randStr}");
        }

        return $slug;
    }

}
