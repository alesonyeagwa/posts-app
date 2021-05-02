<?php

namespace Tests;

use App\Models\User;
use CloudCreativity\LaravelJsonApi\Testing\MakesJsonApiRequests;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, MakesJsonApiRequests, DatabaseTransactions;

    
    protected const USER_STATUS_UNAUTHENTICATED = 0;
    protected const USER_STATUS_UNAUTHORISED = 1;
    protected const USER_STATUS_AUTHORISED = 2;


    protected function actAsUnauthorised(){
        $this->actingAs(User::factory()->create());
    }

    protected function assertAuthFromResponse(TestResponse $response, $user_status, $authorisedStatusCode=200,$unAuthorisedStatusCode=403,$unAuthenticatedStatusCode=401){
        if($user_status == self::USER_STATUS_AUTHORISED){
            $response->assertStatus($authorisedStatusCode);
        }elseif($user_status == self::USER_STATUS_UNAUTHORISED){
            $response->assertStatus($unAuthorisedStatusCode);
        }else{
            $response->assertStatus($unAuthenticatedStatusCode);
        }
    }
}
