<?php

namespace Tests\Feature;

use Illuminate\Foundation\Auth\RegistersUsers;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RegistersUsers;

    /** @test */
    public function authorized_user_can_view_all_users()
    {
        /*
            Given we have an authorized and authenticated user that has permission to view user account
            And we have users that the user can view
            When he visits the user page
            Then the user must see all the users
        */
    }

    /** @test */
    public function authorized_user_can_view_a_user()
    {
        /*
            Given we have an authorized and authenticated user that has permission to be a user account
            And we have users that the user can view
            When the user wants to view a specific user
            Then the user must see that user
        */
    }

    /** @test */
    public function authorized_user_can_delete_a_user()
    {
        /*
            Given we have an authorized and authenticated user that has permission to delete user account
            And we have users that the user can delete
            When that user wants to delete user account/s
            Then that user account/s must be deleted in the system
            And It must return a proper response to the user
            And a new activity log for user account deletion must be created too
        */
    }

    /** @test */
    public function authorized_user_can_activate_or_deactivate_a_user()
    {
        /*
            Given we have an authenticated and authorized user that has permission to activate/deactivate user account
            And we have users that the user can activate or deactivate
            When that authorized user, deactivates a user
            then that deactivated account must not avaible to login in the system,
            And a new activity log for user account deactivation must be created too
            When the authorized user, activated again that account,
            then the user that owns that account may again login in the system
            And a new activity log for user account activation must be created too
        */
    }

    /** @test */
    public function unauthorized_user_must_not_access_any_user_methods_or_pages()
    {
        /*
            Given we have an unauthorized and unauthenticated user
            When it try's to access and user pages or do and user methods,
            Then an error response must be thrown
        */
    }
}
