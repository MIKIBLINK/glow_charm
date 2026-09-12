<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'status',
        'permissions',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
            'permissions' => 'array',
        ];
    }

    /**
     * Use the username (name) column for authentication instead of email.
     */
    public function username(): string
    {
        return 'name';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Check whether this user may perform the given permission.
     * Admins are always allowed; inactive users are never allowed.
     */
    public function canDo(string $permission): bool
    {
        if (! $this->isAdmin()) {
            if (! $this->status) {
                return false;
            }

            return in_array($permission, $this->permissions ?? [], true);
        }

        return true;
    }

    /**
     * Default permission set given to a newly registered/created staff member
     * (read-only access to every module so they can at least browse).
     */
    public static function defaultStaffPermissions(): array
    {
        $keys = ['dashboard.view'];

        foreach (config('permissions.groups', []) as $perms) {
            foreach ($perms as $key => $label) {
                if (str_ends_with($key, '.view')) {
                    $keys[] = $key;
                }
            }
        }

        return array_values(array_unique($keys));
    }
}
