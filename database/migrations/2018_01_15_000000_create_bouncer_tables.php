<?php

use Silber\Bouncer\Database\Models;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateBouncerTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if(Schema::hasTable('roles')) {
            Schema::rename('roles', 'roles_archived');
        }

        Schema::create(Models::table('abilities'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150);
            $table->string('entity_type', 150)->nullable();
            $table->integer('entity_id')->unsigned()->nullable();
            $table->string('title')->nullable();
            $table->boolean('only_owned')->default(false);
            $table->integer('scope')->nullable()->index();
            $table->timestamps();
        });

        Schema::create(Models::table('roles'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150);
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->integer('level')->unsigned()->nullable();
            $table->integer('scope')->nullable()->index();
            $table->timestamps();
            $table->unique(['name', 'scope'], 'roles_name_scope_unique');
        });

        Schema::create(Models::table('assigned_roles'), function (Blueprint $table) {
            $table->integer('role_id')->unsigned()->index();
            $table->integer('entity_id')->unsigned();
            $table->string('entity_type', 150);
            $table->integer('scope')->nullable()->index();

            $table->index(
                ['entity_id', 'entity_type', 'scope'],
                'assigned_roles_entity_index'
            );

            $table->foreign('role_id')
                ->references('id')->on(Models::table('roles'))
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create(Models::table('permissions'), function (Blueprint $table) {
            $table->integer('ability_id')->unsigned()->index();
            $table->string('entity_type', 150);
            $table->integer('entity_id')->unsigned();
            $table->boolean('forbidden')->default(false);
            $table->integer('scope')->nullable()->index();

            $table->index(
                ['entity_id', 'entity_type', 'scope'],
                'permissions_entity_index'
            );

            $table->foreign('ability_id')
                ->references('id')->on(Models::table('abilities'))
                ->onUpdate('cascade')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Models::table('permissions'));
        Schema::dropIfExists(Models::table('assigned_roles'));
        Schema::dropIfExists(Models::table('roles'));
        Schema::dropIfExists(Models::table('abilities'));
        if(Schema::hasTable('roles_archived')) {
            Schema::rename('roles_archived', 'roles');
        }
    }
}
