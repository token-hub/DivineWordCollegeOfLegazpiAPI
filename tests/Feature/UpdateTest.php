<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authorized_user_can_view_all_the_updates()
    {
        /*
            Given we have an authorized and authenticated user that has permission to view updates
            And we have updates that the user can view
            When the user wants to see all the updates
            Then all the updates must be seen
        */
    }

    /** @test */
    public function authorized_user_can_view_a_specific_update()
    {
        /*
            Given we have an authorized and authenticated user that has permission to view updates
            And we have updates that a user can view
            When a user summited the specific update s/he wants to view
            Then that update must send back to the user
        */
    }

    /** @test */
    public function updates_has_required_fields()
    {
        /*
            Given we have an authorized and authenticated user
            When that user submitted an empty field that is required
            Then an error response must thrown
        */
    }

    /** @test */
    public function authorized_user_create_new_update()
    {
        /*
            Given we have an authorized and authenticated user that has permission to create new update
            When the user submitted all the required fields
            Then a new update must be created
            And an activty for creating a new update must be created too.
        */
    }

    /** @test */
    public function authorized_user_can_update_a_specific_update()
    {
        /*
            Given we have an authorized and authenticated user that has permission to update updates
            And we have updates that the user can update
            When the user submitted all the required fields
            Then that update must be updated
            And a proper response must be send back to the user
            And an activity for updating an update must be created too.
        */
    }

    /** @test */
    public function authorized_user_can_delete_updates()
    {
        /*
            Given we have an authorized and authenticated user that has permission to delete update
            And we have updates that the user can delete
            When the user submitted the update/s s/he wants to delete
            Then that update must be deleted
            And an activity for deleting an update must be created too.
        */
    }

    /** @test */
    public function unauthorized_user_must_not_access_any_updates_methods_or_pages()
    {
        /*
            Given we have an unauthorized and unauthenticated user
            When it try's to access and updates pages or do and updates methods,
            Then an error response must be thrown
        */
    }
}
