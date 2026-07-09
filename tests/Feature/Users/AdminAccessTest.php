<?php

namespace Tests\Feature\Users;

use App\Enum\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_cannot_access_admin_users_index(): void
    {
        $member = User::factory()->create(['role' => Roles::Member]);

        $this->actingAs($member)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_staff_can_access_admin_users_index(): void
    {
        $staff = User::factory()->create(['role' => Roles::Staff]);

        $this->actingAs($staff)
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_admin_can_access_admin_users_index(): void
    {
        $admin = User::factory()->create(['role' => Roles::Admin]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_guest_redirected_to_login_from_admin_area(): void
    {
        $this->get(route('admin.users.index'))
            ->assertRedirect('/login');
    }
}
