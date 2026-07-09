<?php

namespace Tests\Feature\Demo;

use App\Enum\Roles;
use App\Models\User;
use Demo\Models\Idea;
use Demo\Notifications\IdeaPublished;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class DemoModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_example_pages_render_for_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => Roles::Member]);
        $this->actingAs($user);

        $this->get('/examples/examples')->assertOk()->assertSee('Livewire Examples');
        $this->get('/examples/formThree')->assertOk()->assertSee('Side Cols Template');
        $this->get('/examples/formFieldset')->assertOk()->assertSee('form.ui.fieldset');
        $this->get('/examples/tableArray')->assertOk();
        $this->get('/examples/ideas/create')->assertOk();
    }

    public function test_livewire_example_pages_render(): void
    {
        $user = User::factory()->create(['role' => Roles::Member]);

        Livewire::actingAs($user)->test('demo::examples.bootstrap')->assertOk();
        Livewire::actingAs($user)->test('demo::examples.ideas')->assertOk();
        Livewire::actingAs($user)->test('demo::examples.tables.table-array-live')->assertOk();
        Livewire::actingAs($user)->test('demo::examples.tables.test')->assertOk();
    }

    public function test_idea_crud_and_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create(['role' => Roles::Member]);
        $this->actingAs($user);

        $this->post('/examples/ideas', [
            'title' => 'Test Idea',
            'status' => 'pending',
            'description' => 'A test idea description',
        ])->assertRedirect('/examples/ideas');

        $this->assertDatabaseHas('ideas', [
            'title' => 'Test Idea',
            'user_id' => $user->id,
        ]);

        $idea = Idea::first();

        Notification::assertSentTo($user, IdeaPublished::class);

        $this->get("/examples/ideas/{$idea->id}/edit")->assertOk();

        $this->delete("/examples/ideas/{$idea->id}")->assertRedirect('/examples/ideas');

        $this->assertDatabaseMissing('ideas', ['id' => $idea->id]);
    }
}
