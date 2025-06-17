<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id('permission_id');
            $table->foreignId('role_id')->constrained('user_roles', 'role_id')->onDelete('cascade');
            $table->enum('description', ['Create', 'Retrieve', 'Update', 'Delete']);
            $table->timestamps();

            // A role can have a specific permission only once
            $table->unique(['role_id', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_permissions');
    }
}
