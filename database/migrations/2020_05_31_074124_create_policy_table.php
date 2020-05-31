<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePolicyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::create($tableNames['policy'], function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->nullable()->comment('名称');
            $table->string('desc')->nullable()->comment('描述');
            $table->text('statement')->nullable()->comment('授权语句');
            $table->string('state')->nullable()->default('DISABLE')->comment('状态{NORMAL:正常}{DISABLE:禁用}');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::dropIfExists($tableNames['policy']);
    }
}