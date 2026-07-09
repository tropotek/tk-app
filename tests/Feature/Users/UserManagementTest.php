<?php

namespace Tests\Feature\Users;

use App\Enum\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private const COMPONENT = 'pages::users.edit';

    public function test_staff_can_create_member_user(): void
    {
        $staff = User::factory()->create(['role' => Roles::Staff]);

        Livewire::actingAs($staff)
            ->test(self::COMPONENT, ['user' => null])
            ->set('form.name', 'New Member')
            ->set('form.email', 'new-member@example.com')
            ->set('form.role', Roles::Member)
            ->call('save')
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'new-member@example.com',
            'role' => Roles::Member->value,
        ]);
    }

    public function test_staff_cannot_create_admin_user(): void
    {
        $staff = User::factory()->create(['role' => Roles::Staff]);

        Livewire::actingAs($staff)
            ->test(self::COMPONENT, ['user' => null])
            ->set('form.name', 'Sneaky Admin')
            ->set('form.email', 'sneaky-admin@example.com')
            ->set('form.role', Roles::Admin)
            ->call('save')
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'sneaky-admin@example.com']);
    }

    public function test_staff_cannot_edit_existing_admin_user(): void
    {
        $staff = User::factory()->create(['role' => Roles::Staff]);
        $admin = User::factory()->create(['role' => Roles::Admin]);

        Livewire::actingAs($staff)
            ->test(self::COMPONENT, ['user' => $admin])
            ->assertForbidden();
    }

    public function test_admin_can_create_admin_user(): void
    {
        $admin = User::factory()->create(['role' => Roles::Admin]);

        Livewire::actingAs($admin)
            ->test(self::COMPONENT, ['user' => null])
            ->set('form.name', 'New Admin')
            ->set('form.email', 'new-admin@example.com')
            ->set('form.role', Roles::Admin)
            ->call('save')
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'new-admin@example.com',
            'role' => Roles::Admin->value,
        ]);
    }

    public function test_admin_can_edit_admin_user(): void
    {
        $admin = User::factory()->create(['role' => Roles::Admin]);
        $otherAdmin = User::factory()->create(['role' => Roles::Admin]);

        Livewire::actingAs($admin)
            ->test(self::COMPONENT, ['user' => $otherAdmin])
            ->set('form.name', 'Updated Name')
            ->call('save')
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $otherAdmin->id,
            'name' => 'Updated Name',
        ]);
    }
}
