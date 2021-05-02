<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
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

    public function test_can_list_comments_for_a_post_public(){
        $post = Post::factory()->create();
        //another user
        $user = User::factory()->create();

        $comments = $post->comments ?: Comment::factory()->count(3)
                                    ->for($post)
                                    ->for($user, 'commenter')
                                    ->create();
        $this->jsonApi()
                ->expects('comments')
                ->get(route('api:v1:posts.relationships.comments', $post->getRouteKey()))
                ->assertStatus(200);
    }

    public function test_cannot_list_unpublished_comments_for_a_post_protected(){
        $post = Post::factory()->create();

        $comments = Comment::factory()->count(3)
                                ->for($post)
                                ->create(['published_at' => null]);

        $this->jsonApi()
                ->expects('comments')
                ->get(route('api:v1:posts.relationships.comments', $post->getRouteKey()))
                ->assertJsonPath('data', array());
    }

    public function test_can_list_user_comments_public(){
        $user = User::factory()->create();
        $comments = Comment::factory()->count(3)
                                    ->for($user, 'commenter')
                                    ->create();

        $this->jsonApi()
                ->expects('comments')
                ->get(route('api:v1:users.relationships.comments', $user->getRouteKey()))
                ->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_list_user_unpublished_comments(){
        $this->viewUnpublishedUserComments(self::USER_STATUS_UNAUTHENTICATED);
    }

    public function test_can_unauthorized_user_list_users_unpublished_comment(){
        $this->viewUnpublishedUserComments(self::USER_STATUS_UNAUTHORISED);
    }

    public function test_can_authorized_user_list_comments_protected(){
        $this->viewUnpublishedUserComments(self::USER_STATUS_AUTHORISED);
    }

    public function test_can_create_comment(){
        $this->createComment(self::USER_STATUS_AUTHORISED);
    }    

    public function test_unauthenticated_user_cannot_create_comment(){
        $this->createComment(self::USER_STATUS_UNAUTHENTICATED);
    } 
    
    public function test_can_update_comment(){
        $this->updateComment(self::USER_STATUS_AUTHORISED);
    }

    public function test_unauthenticated_user_cannot_update_comment(){
        $this->updateComment(self::USER_STATUS_UNAUTHENTICATED);
    }

    public function test_unauthorised_user_cannot_update_comment(){
        $this->updateComment(self::USER_STATUS_UNAUTHORISED);
    }

    public function test_can_delete_comment(){
        $this->deleteComment(self::USER_STATUS_AUTHORISED);
    }
    public function test_unauthenticated_user_cannot_delete_comment(){
        $this->deleteComment(self::USER_STATUS_UNAUTHENTICATED);
    }
    public function test_unauthorised_user_cannot_delete_comment(){
        $this->deleteComment(self::USER_STATUS_UNAUTHORISED);
    }

    private function createComment($user_status){
        $comment = Comment::factory()->make();
        $data = [
            'type' => 'comments',
            'attributes' => [
                'post_id' => $comment->post->id,
                'comment' => $comment->comment,
                'isPublished' => "1",
            ]
        ];

        if($user_status !== self::USER_STATUS_UNAUTHENTICATED){
            $this->actingAs($comment->commenter);
        }
        
        $response = $this
                    ->jsonApi()
                    ->expects('comments')
                    ->withData($data)
                    ->post(route('api:v1:comments.create'));
        
        $this->assertAuthFromResponse($response, $user_status, 201, 201);
    }

    private function updateComment($user_status){
        $comment = Comment::factory()->create(['published_at' => \Carbon\Carbon::now()]);

        $data = [
            'type' => 'comments',
            'id' => (string) $comment->getRouteKey(),
            'attributes' => [
                'comment' => "A new comment",
            ]
        ];

        if($user_status == self::USER_STATUS_AUTHORISED){
            $this->actingAs($comment->commenter);
        }elseif($user_status == self::USER_STATUS_UNAUTHORISED){
            $this->actAsUnauthorised();
        }

        $response = $this->jsonApi()
                ->expects('comments')
                ->withData($data)
                ->patch(route('api:v1:comments.update', $comment->getRouteKey()));

        $this->assertAuthFromResponse($response, $user_status);
    }

    private function deleteComment($user_status){
        $comment = Comment::factory()->create(['published_at' => \Carbon\Carbon::now()]);

        if($user_status == self::USER_STATUS_AUTHORISED){
            $this->actingAs($comment->commenter);
        }elseif($user_status == self::USER_STATUS_UNAUTHORISED){
            $this->actAsUnauthorised();
        }

        $response = $this->jsonApi()
                ->expects('comments')
                ->delete(route('api:v1:comments.delete', $comment->getRouteKey()));
                
        $this->assertAuthFromResponse($response, $user_status, 204);
    }

    private function viewUnpublishedUserComments($user_status){
        $user = User::factory()->create();

        $post = Post::factory()
                        ->for($user, 'author')
                        ->create();
        $comments = Comment::factory()->count(3)
                                    ->for($post)
                                    ->for($user, 'commenter')
                                    ->create(['published_at' => null]);

        if($user_status == self::USER_STATUS_AUTHORISED){
            $this->actingAs($user);
        }elseif($user_status == self::USER_STATUS_UNAUTHORISED){
            $this->actAsUnauthorised();
        }

        $response = $this->jsonApi()
                ->expects('comments')
                ->get(route('api:v1:users.relationships.comments', $user->getRouteKey()));
        
        $response->assertStatus(200);

        if($user_status !== self::USER_STATUS_AUTHORISED){
            $response->assertJsonPath('data', array());
        }
        
    }

}
