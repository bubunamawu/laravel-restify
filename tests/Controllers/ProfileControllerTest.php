<?php

namespace Binaryk\LaravelRestify\Tests\Controllers;

use Binaryk\LaravelRestify\Tests\Fixtures\User\User;
use Binaryk\LaravelRestify\Tests\IntegrationTest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileControllerTest extends IntegrationTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->authenticate(factory(User::class)->create([
            'name' => 'Eduard Lupacescu',
            'email' => 'eduard.lupacescu@binarcode.com',
        ]));
    }

    public function test_profile_returns_authenticated_user()
    {
        $response = $this->getJson('/restify-api/profile')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ]);

        $response->assertJsonFragment([
            'email' => $this->authenticatedAs->email,
        ]);
    }

    public function test_profile_update()
    {
        $response = $this->putJson('restify-api/profile', [
            'email' => 'contact@binarschool.com',
            'name' => 'Eduard',
        ])
            ->assertStatus(200);

        $response->assertJsonFragment([
            'email' => 'contact@binarschool.com',
            'name' => 'Eduard',
        ]);
    }

    public function test_profile_update_unique_email()
    {
        factory(User::class)->create([
            'email' => 'existing@gmail.com',
        ]);

        $this->putJson('restify-api/profile', [
            'email' => 'existing@gmail.com',
            'name' => 'Eduard',
        ])->assertStatus(400);
    }

    public function test_profile_upload_avatar()
    {
        $file = UploadedFile::fake()->image($this->getTestJpg())->size(100);

        $this->postJson('restify-api/profile/avatar', [
            'avatar' => $file
        ])
            ->assertStatus(200);
    }
}