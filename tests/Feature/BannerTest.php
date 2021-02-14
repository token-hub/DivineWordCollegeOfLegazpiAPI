<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authorized_user_can_view_all_the_banners()
    {
        /*
            Given we have an authorized and authenticated user
            When that user wants to view all the banner
            Then all the banners must appeared to screen
        */
    }

    /** @test */
    public function authorized_user_can_add_a_valid_banner_image()
    {
        /*
            Given we have an authorized and authenticated user
            When the user submitted a valid image banner
            Then that upload image must appeared on the list of the banners
            And a proper response mut be send back to the user
            And a new activity for adding a new banner image must be created too.
        */
    }

    /** @test */
    public function banner_image_must_meet_the_requirements()
    {
        /*
            Given we have an authorized and authenticated user
            When the user submitted an invalid image or tried to submit other type than image
            Then an error response must be thrown
        */
    }

    /** @test */
    public function authorized_user_can_delete_a_banner_image()
    {
        /*
            Given we have an authorized and authenticated user
            When the user wants to delete banner image/s
            Then that delete must not be seen anywhere
            And a proper response must be send back to the user
            And an activity for deleting a banner image must be created too.
        */
    }

    /** @test */
    public function authorized_user_can_reorder_banner_image()
    {
        /*
            Given we have an authorized and authenticated user
            When the user submitted a non-repeating order of banner images
            Then the order of the banner images must be updated
            And a proper response must be send back to the user
            And an activity must for updating the order of the banner image must be created too.
        */
    }

    /** @test */
    public function banner_must_have_a_default_background()
    {
        /*
            Given we have an authorized and authenticated user
            When the user deleted all the banner images
            Then a default banner image must appeared on the screen
        */
    }

    /** @test */
    public function unauthorized_user_must_not_access_any_banner_methods_or_pages()
    {
        /*
            Given we have an unauthorized and unauthenticated user
            When it try's to access and banner pages or do and banner methods,
            Then an error response must be thrown
        */
    }
}
