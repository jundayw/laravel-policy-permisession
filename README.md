# 安装方法
命令行下, 执行 composer 命令安装:
````
composer require jundayw/laravel-policy-permisession
````

# 使用方法
authentication package that is simple and enjoyable to use.

## 导出配置
```
php artisan vendor:publish --tag=permission-config
```

## 导出数据库迁移文件
```
php artisan vendor:publish --tag=permission-migrations
```

## 数据库迁移
```
php artisan migrate --path=/database/migrations/2020_05_31_074124_create_policy_table.php
```

## 导出数据库填充文件
```
php artisan vendor:publish --tag=permission-seeders
```

## 数据库填充
```
php artisan db:seed --class=PermissionTableSeeder
```

## 用户模型
```
use Jundayw\LaravelPolicyPermisession\Contracts\PermissionContracts;
use Jundayw\LaravelPolicyPermisession\Traits\PermissionTrait;

class User extends Authenticatable implements PermissionContracts
{
    use PermissionTrait;
    
    public function getPermissions($permission, $arguments)
    {
        //return Policy::all();
    }
```

## 自定义中间件
请先调用Auth中间件，然后在调用自定义中间件
```
namespace App\Http\Middleware;

use Closure;

class Permisession
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guards)
    {
        //$request->user()->can($request->route()->..., $guards)
        return $next($request);
    }
}
```

## 授权语句
```
[
    {
        "Effect": "Allow",
        "Action": ["Admin.*"],
        "Resource": "*",
        "Condition":{
            "ip":"0.0.0.0/0"
        }
    },{
        "Effect": "Deny",
        "Action": ["Admin.role.*"],
        "Resource": "*"
    }
]
```
```
$request->user('admin')->can('admin.manager.delete')
// true
$request->user('admin')->can('admin.role.delete')
// false
```