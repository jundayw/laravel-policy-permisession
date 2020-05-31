<?php

use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        DB::table($tableNames['policy'])->insert([
            'title' => 'AdministratorAccess',
            'desc' => '全部权限',
            'statement' => '[{"Effect": "Allow","Action": ["Admin.*"],"Resource": "*","Condition":{"ip":"0.0.0.0/0"}}]',
            'state' => 'NORMAL',
        ]);
    }
}
