<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_must_have_required_fields()
    {
        /*
            Given we have an authorized and authenticated user
            When the user submitted an empty field that is required
            Then the creation must be canceled and an error response must be thrown
        */
    }

    /** @test */
    public function it_must_not_be_duplicated()
    {
        /*
            Given we have an authorized and authenticated user
            When the user submitted a data that is already exists
            Then the creation of the new role must be canceled
            And must return an error response
        */
    }

    /** @test */
    public function authorized_user_can_create_new_role()
    {
        /*
            Given we have an authorized and authenticated user that has permission to create a role
            When the user submitted all the required fields
            Then a new role must be created
            And It must return a proper response to the user
            And and a new activity for role creation must be created too
        */
    }

    /** @test */
    public function authorized_user_can_update_a_role()
    {
        /*
            Given we have an authorized and authenticated user that has permission to update a role
            And an existing role to update
            When the user submitted all the required fields
            Then the selected role must be updated
            And It must return a proper response to the user
            And a new activity for updating a role must be created too
        */
    }

    /** @test */
    public function authorized_user_can_delete_roles()
    {
        /*
            Given we have an authorized and authenticated user that has permission to delete a role
            And an exists role to delete
            When the user submitted a delete request
            Then that role/s must be deleted
            And It must return a proper response to the user
            And a new activity for deleting a role must be created too
        */
    }

    /** @test */
    public function unauthorized_user_must_not_access_any_roles_methods_or_pages()
    {
        /*
            Given we have an unauthorized and unauthenticated user
            When it try's to access and role pages or do and role methods,
            Then an error response must be thrown
        */
    }
}
