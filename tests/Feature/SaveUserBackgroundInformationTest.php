<?php

namespace Tests\Feature;

use App\Events\UserSaved;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SaveUserBackgroundInformationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that creating a user dispatches the `UserSaved` event with the correct user instance.
     *
     * @return void
     */
    public function test_create_user_dispatches_user_saved_event()
    {

        Event::fake([UserSaved::class]);
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('users.store'), [
            'prefixname' => 'Mr',
            'firstname' => 'John',
            'middlename' => 'Doe',
            'lastname' => 'Smith',
            'username' => 'johnsmith',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('users.index'));
        Event::assertDispatched(UserSaved::class, function ($event) {
            return $event->user->username === 'johnsmith';
        });
    }

    /**
     * Test that updating a user dispatches the `UserSaved` event with the correct user instance.
     *
     * @return void
     */
    public function test_update_user_dispatches_user_saved_event()
    {

        Event::fake([UserSaved::class]);
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->put(route('users.update' , $user->id), [
            'prefixname' => 'Mr',
            'firstname' => 'Jane',
            'middlename' => 'Doe',
            'lastname' => 'Smith',
            'username' => 'janesmith',
            'email' => 'jane@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertRedirect(route('users.index'));
        Event::assertDispatched(UserSaved::class, function ($event) {
            return $event->user->username === 'janesmith';
        });
    }

    /**
     * Test that user details are correctly saved in the database after creating a user.
     *
     *  @return void
     */
    public function test_user_details_are_saved_correctly_on_user_creation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $userData = [
            'prefixname' => 'Mr',
            'firstname' => 'John',
            'middlename' => 'Doe',
            'lastname' => 'Smith',
            'username' => 'johnsmith',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post(route('users.store'), $userData);

        $user2 = User::where('username', $userData['username'])->first();

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('details', [
            'user_id' => $user2->id,
            'key' => 'Full name',
            'value' => $user2->fullName,
        ]);
        $this->assertDatabaseHas('details', [
            'user_id' => $user2->id,
            'key' => 'Gender',
            'value' => $user2->gender,
        ]);
    }

}
