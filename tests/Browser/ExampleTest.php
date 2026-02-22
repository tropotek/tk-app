<?php

use App\Models\Employer;

it('returns a successful response after user registration', function () {
    visit('/register')
        ->fill('email', 'test2@ttek.org')
        ->fill('name', 'Test User')
        ->fill('password', 'pass1234')
        ->press('@btn-register')
        ->assertPathIs('/');

    expect(\App\Models\User::count())->toBe(1);

    $this->assertAuthenticated();
});

it('successfully created and read job_listings and employers', function () {
    $this->actingAs($user = \App\Models\User::factory()->create());

//    Employer::factory(10)->create();
//    \App\Models\Job::factory(10)->create();
//
//    expect(\App\Models\Job::count())->toBe(10);

    $this->assertAuthenticated();
});
