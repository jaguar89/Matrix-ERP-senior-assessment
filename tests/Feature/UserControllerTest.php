<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

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
     * Test if the index method returns a list of users.
     *
     * @return void
     */
    public function test_index_returns_user_list()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $users = User::factory()->count(20)->create();

        $response = $this->get(route('users.index'));


        $response->assertStatus(200);
        $response->assertViewIs('pages.users.index');
        $response->assertViewHas('users');
    }

    /**
     * Test if the trashed method returns a list of soft-deleted users.
     *
     * @return void
     */
    public function test_trashed_returns_soft_deleted_users()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $users = User::factory()->count(20)->create();

        $this->userService->destroy($users->take(20)->pluck('id')->toArray());

        $response = $this->get(route('users.trashed'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.users.trashed');
        $response->assertViewHas('users');
    }

    /**
     * Test if the create method returns the user creation form.
     *
     * @return void
     */
    public function test_create_returns_create_form()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('users.create'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.users.create');
    }

    /**
     * Test if a user can be stored in the database.
     *
     * @return void
     */
    public function test_store_creates_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Storage::fake('public');
        $photo = UploadedFile::fake()->image('profile.jpg');

        $response = $this->post(route('users.store'), [
            'prefixname' => 'Mr',
            'firstname' => 'John',
            'middlename' => 'Doe',
            'lastname' => 'Smith',
            'username' => 'johnsmith',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'photo' => $photo
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'johnsmith',
            'email' => 'john@example.com'
        ]);
        Storage::disk('public')->assertExists('users/' . $photo->hashName());
    }

    /**
     * Test if a user can be updated in the database.
     *
     * @return void
     */
    public function test_update_updates_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Storage::fake('public');
        $photo = UploadedFile::fake()->image('profile.jpg');

        $response = $this->put(route('users.update' , $user->id), [
            'prefixname' => 'Mr',
            'firstname' => 'Jane',
            'middlename' => 'Doe',
            'lastname' => 'Smith',
            'username' => 'janesmith',
            'email' => 'jane@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'photo' => $photo
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'janesmith',
            'email' => 'jane@example.com'
        ]);
        $this->assertDatabaseMissing('users', [
            'username' => 'johnsmith',
            'email' => 'john@example.com'
        ]);
        Storage::disk('public')->assertExists('users/' . $photo->hashName());

    }

    /**
     * Test if a user can be deleted from the database.
     *
     * @return void
     */
    public function test_destroy_deletes_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->delete(route('users.destroy', $user->id));

        $response->assertRedirect(route('users.index'));
        $this->assertSoftDeleted('users', [
            'id' => $user->id
        ]);
    }

    /**
     * Test if a user can be restored.
     *
     * @return void
     */
    public function test_restore_restores_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $user2 = User::factory()->create();
        $this->userService->destroy($user2->id);

        $response = $this->withHeader('Referer', route('users.trashed'))
            ->patch(route('users.restore', $user2->id));

        $response->assertRedirect(route('users.trashed'));
        $this->assertNotSoftDeleted('users', [
            'id' => $user2->id
        ]);
    }

    /**
     * Test if a user can be permanently deleted.
     *
     * @return void
     */
    public function test_delete_permanently_deletes_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $user2 = User::factory()->create();
        $this->userService->destroy($user2->id);

        $response = $this->withHeader('Referer', route('users.trashed'))
            ->delete(route('users.delete', $user2->id));

        $response->assertRedirect(route('users.trashed'));
        $this->assertDatabaseMissing('users', ['id' => $user2->id]);

    }
}
