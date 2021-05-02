<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_can_list_posts_public(){
        $this->jsonApi()
                ->expects('posts')
                ->get(route('api:v1:posts.index'))
                ->assertStatus(200);
    }

    public function test_can_list_posts_protected(){
        $user = User::factory()->create();

        $posts = Post::factory()->count(3)
                                ->for($user, 'author')
                                ->create(["published_at" => null]);

        $this->actingAs($user);

        $this->jsonApi()
                ->expects('posts')
                ->get(route('api:v1:users.relationships.posts', $user->getRouteKey()))
                ->assertStatus(200);
    }

    public function test_can_create_post(){
        $this->createPost(self::USER_STATUS_AUTHORISED);
    }

    public function test_unauthenticated_user_cannot_create_post(){
        $this->createPost(self::USER_STATUS_UNAUTHENTICATED);
    }

    public function test_can_view_a_published_post(){
        $post = Post::factory()->create(['published_at' => \Carbon\Carbon::now()]);

        $this->jsonApi()
            ->expects('posts')
            ->get(route('api:v1:posts.read', $post->getRouteKey()))
            ->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_view_an_unpublished_post(){
        $this->viewUnpublishedPost(self::USER_STATUS_UNAUTHENTICATED);
    }

    public function test_unauthorized_user_view_cannot_an_unpublished_post(){
        $this->viewUnpublishedPost(self::USER_STATUS_UNAUTHORISED);
    }

    public function test_can_authorized_user_view_of_an_unpublished_post(){
        $this->viewUnpublishedPost(self::USER_STATUS_AUTHORISED);
    }

    public function test_can_update_post(){
        $this->updatePost(self::USER_STATUS_AUTHORISED);
    }

    public function test_unauthenticated_user_cannot_update_post(){
        $this->updatePost(self::USER_STATUS_UNAUTHENTICATED);
    }
    public function test_unauthorised_user_cannot_update_post(){
        $this->updatePost(self::USER_STATUS_UNAUTHORISED);
    }

    public function test_can_delete_post(){
        $this->deletePost(self::USER_STATUS_AUTHORISED);
    }
    public function test_unauthenticated_user_cannot_delete_post(){
        $this->deletePost(self::USER_STATUS_UNAUTHENTICATED);
    }
    public function test_unauthorised_user_cannot_delete_post(){
        $this->deletePost(self::USER_STATUS_UNAUTHORISED);
    }


    private function createPost($user_status){
        $post = Post::factory()->make();

        $data = [
            'type' => 'posts',
            'attributes' => [
                'title' => $post->title,
                'slug' => $post->slug,
                'content' => $post->content,
                'publishedAt' => $post->published_at->toAtomString(),
            ]
        ];

        if($user_status !== self::USER_STATUS_UNAUTHENTICATED){
            $this->actingAs($post->author);
        }
        
        $response = $this
                    ->jsonApi()
                    ->expects('posts')
                    ->withData($data)
                    ->post(route('api:v1:posts.create'));

        
        $this->assertAuthFromResponse($response, $user_status, 201, 201);
    }



    private function updatePost($user_status){
        $post = \App\Models\Post::first() ?: Post::factory()->create(['published_at' => \Carbon\Carbon::now()]);

        $data = [
            'type' => 'posts',
            'id' => (string) $post->getRouteKey(),
            'attributes' => [
                'title' => 'A new title',
            ],
        ];

        if($user_status == self::USER_STATUS_AUTHORISED){
            $this->actingAs($post->author);
        }elseif($user_status == self::USER_STATUS_UNAUTHORISED){
            $this->actAsUnauthorised();
        }

        $response = $this->jsonApi()
                        ->expects('posts')
                        ->withData($data)
                        ->patch(route('api:v1:posts.update', $post->getRouteKey()));
                

        $this->assertAuthFromResponse($response, $user_status);
    }

    private function deletePost($user_status){
        $post = \App\Models\Post::first() ?: Post::factory()->create(['published_at' => \Carbon\Carbon::now()]);

        if($user_status == self::USER_STATUS_AUTHORISED){
            $this->actingAs($post->author);
        }elseif($user_status == self::USER_STATUS_UNAUTHORISED){
            $this->actAsUnauthorised();
        }

        $response = $this->jsonApi()
                ->expects('posts')
                ->delete(route('api:v1:posts.delete', $post->getRouteKey()));

        $this->assertAuthFromResponse($response, $user_status, 204);
    }

    private function viewUnpublishedPost($user_status){
        $post = Post::factory()->create(['published_at' => null]);

        if($user_status == self::USER_STATUS_AUTHORISED){
            $this->actingAs($post->author);
        }elseif($user_status == self::USER_STATUS_UNAUTHORISED){
            $this->actAsUnauthorised();
        }

        $response = $this->jsonApi()
            ->expects('posts')
            ->get(route('api:v1:posts.read', $post->getRouteKey()));
        
        $this->assertAuthFromResponse($response, $user_status, 200, 404, 404);
    }

}
