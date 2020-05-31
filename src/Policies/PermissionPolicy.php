<?php

namespace Jundayw\LaravelPolicyPermisession\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Arr;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function __call($method, $arguments)
    {
        return call_user_func_array([Arr::first($arguments), 'hasPermission'], Arr::prepend($arguments, $method));
    }
}