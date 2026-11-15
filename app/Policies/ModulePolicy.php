<?php

namespace App\Policies;

use App\Models\User;
use App\Support\Modules;

abstract class ModulePolicy
{
    protected string $module = 'core';

    protected string $permission = 'settings';

    protected bool $locked = false;

    public function before(User $user, string $ability): ?bool
    {
        if ($this->locked && in_array($ability, ['create', 'update', 'delete', 'deleteAny', 'restore', 'forceDelete'], true)) {
            return false;
        }

        if (config('denimforge.demo_readonly') && in_array($ability, ['create', 'update', 'delete', 'deleteAny'], true) && $this->permission !== 'inquiries') {
            return false;
        }

        if ($this->module !== 'core' && ! Modules::enabled($this->module)) {
            return false;
        }

        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can($this->permission.'.view');
    }

    public function view(User $user): bool
    {
        return $user->can($this->permission.'.view');
    }

    public function create(User $user): bool
    {
        return $user->can($this->permission.'.create');
    }

    public function update(User $user): bool
    {
        return $user->can($this->permission.'.update');
    }

    public function delete(User $user): bool
    {
        return $user->can($this->permission.'.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can($this->permission.'.delete');
    }
}
