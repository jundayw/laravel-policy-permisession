<?php

namespace Jundayw\LaravelPolicyPermisession\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait PermissionTrait
{
    public function hasPermission($permission, $auth, ...$arguments)
    {
        if (session()->has('policies') == false || config('permission.cache.forget') == true) {
            session()->put('policies', $auth->getPermissions($permission, $arguments));
        }
        $policies = session()->get('policies');

        if (config('permission.debug')) {
            return true;
        }

        return $this->checkPermission($permission, $policies);
    }

    /**
     * 策略校验
     * @param $permission
     * @param $policies
     * @return bool
     */
    private function checkPermission($permission, $policies)
    {
        $permissions = [];
        
        // 根据 effect[allow/deny] 分组及转换数据格式
        foreach ($policies as $policy) {
            // 策略集合
            foreach (json_decode($policy['statement'], true) as $statement) {
                // 策略
                $statement                           = array_change_key_case($statement);
                $statement['effect']                 = strtolower($statement['effect']);
                $statement['action']                 = array_map('strtolower', Arr::wrap($statement['action']));
                $permissions[$statement['effect']][] = $statement;
            }
        }
        $permission           = strtolower($permission);
        $permissions['deny']  = $permissions['deny'] ?? [];
        $permissions['allow'] = $permissions['allow'] ?? [];
        // 拒绝逻辑处理：优先级高于允许
        foreach ($permissions['deny'] as $statement) {
            foreach ($statement['action'] as $action) {
                if (Str::is($action, $permission)) {
                    $condition = $this->checkCondition($statement);
                    return $condition == false;
                }
            }
        }
        // 允许逻辑处理
        foreach ($permissions['allow'] as $statement) {
            foreach ($statement['action'] as $action) {
                if (Str::is($action, $permission)) {
                    $condition = $this->checkCondition($statement);
                    return $condition == true;
                }
            }
        }
        // 默认处理
        return false;
    }

    /**
     * 策略条件校验
     * @param array $items
     * @return bool|mixed
     */
    protected function checkCondition($items = [])
    {
        if (!array_key_exists('condition', $items)) {
            return true;
        }
        foreach ($items['condition'] as $key => $condition) {
            $method = implode('', ['check', Str::studly($key), 'Condition']);
            if (method_exists($this, $method)) {
                return call_user_func([$this, $method], $condition);
            }
        }
        return true;
    }

    /**
     * 网络地址校验
     * 192.168.0.0
     * 192.168.0.0/16
     * @param $conditions
     * @return bool
     */
    protected function checkIpCondition($conditions)
    {
        // $conditions = '0.0.0.0/0';//全部地址
        foreach (Arr::wrap($conditions) as $condition) {
            $condition = implode('', [$condition, '/32']);
            $clientIp  = app('request')->getClientIp();
            if ($this->ipCIDRCheck($clientIp, $condition)) {
                return true;
            }
        }
        return false;
    }

    /**
     * check against CIDR
     * @param $IP
     * @param $CIDR
     * @return bool
     */
    private function ipCIDRCheck($IP, $CIDR)
    {
        list($net, $mask) = explode("/", $CIDR);

        $ip_net  = ip2long($net);
        $ip_mask = ~((1 << (32 - $mask)) - 1);

        $ip_ip = ip2long($IP);

        $ip_ip_net = $ip_ip & $ip_mask;

        return ($ip_ip_net == $ip_net);
    }
}