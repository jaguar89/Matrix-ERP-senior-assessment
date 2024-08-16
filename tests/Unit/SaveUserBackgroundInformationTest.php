<?php

namespace Tests\Unit;

use App\Events\UserSaved;
use App\Listeners\SaveUserBackgroundInformation;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Mockery;
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

        Event::assertDispatched(UserSaved::class, function ($event) use ($user) {
            return $event->user->is($user);
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
        $user->update(['firstname' => 'UpdatedName']);

        Event::assertDispatched(UserSaved::class, function ($event) use ($user) {
            return $event->user->is($user);
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

        $this->assertDatabaseHas('details', [
            'user_id' => $user->id,
            'key' => 'Full name',
            'value' => $user->fullName,
        ]);
        $this->assertDatabaseHas('details', [
            'user_id' => $user->id,
            'key' => 'Gender',
            'value' => $user->gender,
        ]);
    }

    /**
     * Test that the `SaveUserBackgroundInformation` listener correctly calls
     * `saveUserDetails` on the `UserService` when a `UserSaved` event is dispatched.
     */
    public function test_it_saves_user_details_when_user_saved_event_is_dispatched()
    {

        $user = User::factory()->create();
        $userServiceMock = Mockery::mock(UserService::class);

        $userServiceMock->shouldReceive('saveUserDetails')->once()->with($user);

        $listener = new SaveUserBackgroundInformation($userServiceMock);

        $listener->handle(new UserSaved($user));

    }
}
