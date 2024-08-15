<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The UserService instance.
     *
     * @var \App\Services\UserService
     */
    protected $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = app(UserService::class);
    }


    /**
     * Test that the UserService can return a paginated list of users.
     *
     * @return void
     */
    public function test_it_can_return_a_paginated_list_of_users()
    {
        // Arrangements
        User::factory()->count(30)->create();

        // Actions
        $paginatedUsers = $this->userService->list();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $paginatedUsers);
        $this->assertCount(10, $paginatedUsers->items());
        $this->assertEquals(30, $paginatedUsers->total());
    }


    /**
     * Test storing a user with valid attributes.
     *
     * @return void
     */
    public function test_it_can_store_a_user_to_database()
    {
        // Arrangements
        Storage::fake('public');
        $photo = UploadedFile::fake()->image('profile.jpg');
        $attributes = [
            'prefixname' => 'Mr',
            'firstname' => 'John',
            'middlename' => 'Doe',
            'lastname' => 'Smith',
            'username' => 'johnsmith',
            'email' => 'john@example.com',
            'password' => 'password',
//            'password_confirmation' => 'password',
            'photo' => $photo,
        ];


        // Actions
        $user = $this->userService->store($attributes);

        // Assertions
        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'username' => 'johnsmith',
            'email' => 'john@example.com'
        ]);
        Storage::disk('public')->assertExists('users/' . $photo->hashName());
    }

    /**
     * Test that an existing user can be found and returned.
     *
     * @return void
     */
    public function test_it_can_find_and_return_an_existing_user()
    {
        // Arrangements
        $user = User::factory()->create();
        $userId = $user->id;


        // Actions
        $foundUser = $this->userService->find($userId);

        // Assertions
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($userId, $foundUser->id);

    }

    /**
     * Test that an existing user can be successfully updated with new attributes.
     *
     * @return void
     */
    public function test_it_can_update_an_existing_user()
    {
        // Arrangements
        $user = User::factory()->create();
        $userId = $user->id;

        Storage::fake('public');
        $photo = UploadedFile::fake()->image('profile.jpg');
        $attributes = [
            'prefixname' => 'Mr',
            'firstname' => 'John',
            'middlename' => 'Doe',
            'lastname' => 'Smith',
            'username' => 'johnsmith',
            'email' => 'john@example.com',
//            'password' => 'password',
//            'password_confirmation' => 'password',
            'photo' => $photo,
        ];

        // Actions
        $result = $this->userService->update($userId, $attributes);

        // Assertions
        $this->assertTrue($result);
        $this->assertDatabaseHas('users', [
            'username' => 'johnsmith',
            'email' => 'john@example.com'
        ]);
        Storage::disk('public')->assertExists('users/' . $photo->hashName());
    }


    /**
     * Test that an existing user can be soft deleted.
     *
     * @return void
     */
    public function test_it_can_soft_delete_an_existing_user()
    {
        // Arrangements
        $user = User::factory()->create();
        $userId = $user->id;

        // Actions
        $this->userService->destroy($userId);

        // Assertions
        $this->assertSoftDeleted('users', [
            'id' => $userId,
        ]);
    }


    /**
     * Test if the service can return a paginated list of soft-deleted (trashed) users.
     *
     * @return void
     */
    public function test_it_can_return_a_paginated_list_of_trashed_users()
    {
        // Arrangements
        $users = User::factory()->count(30)->create();

        // Actions
        $this->userService->destroy($users->take(30)->pluck('id')->toArray());
        $paginatedTrashedUsers = $this->userService->listTrashed();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $paginatedTrashedUsers);
        $this->assertCount(10, $paginatedTrashedUsers->items());
        $this->assertEquals(30, $paginatedTrashedUsers->total());
        $this->assertTrue($paginatedTrashedUsers->contains($users->first()));
    }

    /**
     * Test if the service can restore a soft-deleted user.
     *
     * @return void
     */
    public function test_it_can_restore_a_soft_deleted_user()
    {
        // Arrangements
        $users = User::factory()->count(30)->create();

        // Actions
        // Soft delete some users
        $this->userService->destroy($users->take(20)->pluck('id')->toArray());
        $this->assertSoftDeleted('users', ['id' => 5,]);

        // Restore some users
        $this->userService->restore($users->take(10)->pluck('id')->toArray());
        $this->assertSoftDeleted('users', ['id' => 15,]);

        // Assertions
        // first 10 users have been restored
        foreach ($users->take(10) as $user) {
            $this->assertFalse(User::withTrashed()->find($user->id)->trashed());
        }

        // next 10 users are still soft-deleted
        foreach ($users->slice(10, 10) as $user) {
            $this->assertTrue(User::withTrashed()->find($user->id)->trashed());
        }
    }


    /**
     * Test if the service can permanently delete a soft-deleted user.
     *
     * @return void
     */
    public function test_it_can_permanently_delete_a_soft_deleted_user()
    {
        // Arrangements
        $users = User::factory()->count(30)->create();

        // Actions
        // Soft delete some users
        $this->userService->destroy($users->take(15)->pluck('id')->toArray());

        // Permanently delete the soft-deleted users
        $this->userService->delete($users->take(10)->pluck('id')->toArray());

        // Assertions
        // Verify that the permanently deleted users are no longer in the database
        foreach ($users->take(10) as $user) {
            $this->assertDatabaseMissing('users', ['id' => $user->id]);
        }

        // Verify that the remaining 5 of the originally soft-deleted users are still soft-deleted
        foreach ($users->skip(10)->take(5) as $user) {
            $this->assertTrue(User::withTrashed()->find($user->id)->trashed());
        }
    }

    /**
     * Test if the service can upload a photo.
     *
     * @return void
     */
    public function it_can_upload_photo()
    {
        // Arrangements
        Storage::fake('public');
        $photo = UploadedFile::fake()->image('profile.jpg');


        // Actions
        $uploadedPath = $this->userService->upload($photo);

        // Assertions
        Storage::disk('public')->assertExists('users/' . $photo->hashName());
        $this->assertEquals('users/' . $photo->hashName() , $uploadedPath);
    }
}
