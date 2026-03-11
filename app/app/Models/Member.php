<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;

/**
 *
 *
 */
class Member extends User
{

    protected $table = 'users';

    protected $hidden = [
        'password',
        'remember_token',
        'staff_data',
    ];

    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'member_data' => 'array',
        ]);
    }

    public function getMemberData(): array
    {
        return $this->member_data ?? [];
    }

    public function setMemberData(array $data): void
    {
        $this->member_data = $data;
    }

    public function getMorphClass(): string
    {
        return User::class;
    }

    protected static function booted(): void
    {
        static::addGlobalScope('member_role', function (Builder $query) {
            $query->role('Member');
        });

        static::created(function (User $user) {
            if (!$user->hasRole('Member')) {
                $user->assignRole('Member');
            }
        });
    }

}
