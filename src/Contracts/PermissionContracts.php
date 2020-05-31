<?php

namespace Jundayw\LaravelPolicyPermisession\Contracts;

interface PermissionContracts
{
    /**
     * @param $permission
     * @param $arguments
     * @return mixed
     */
    public function getPermissions($permission, $arguments);

    /**
     * @param $permission
     * @param $auth
     * @param mixed ...$arguments
     * @return mixed
     */
    public function hasPermission($permission, $auth, ...$arguments);
}