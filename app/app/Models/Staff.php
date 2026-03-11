<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 *
 *
 */
class Staff extends User
{

    protected $table = 'users';

    protected $hidden = [
        'password',
        'remember_token',
        'member_data',
    ];

    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'staff_data' => 'array',
        ]);
    }

    public function getStaffData(): array
    {
        return $this->staff_data ?? [];
    }

    public function setStaffData(array $data): void
    {
        $this->staff_data = $data;
    }

    public function getMorphClass(): string
    {
        return User::class;
    }

    protected static function booted(): void
    {
        static::addGlobalScope('staff_role', function (Builder $query) {
            $query->role('Staff');
        });

        static::created(function (User $user) {
            if (!$user->hasRole('Staff')) {
                $user->assignRole('Staff');
            }
        });
    }


}
