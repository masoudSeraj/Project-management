<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('task_dependencies');

        Schema::create('dependency_task', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->unsignedBigInteger('dependency_id');
            $table->foreign('dependency_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dependency_task');

        if (!Schema::hasTable('task_dependencies')) {
            Schema::create('task_dependencies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('task_id');
                $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
                $table->unsignedBigInteger('dependency_id');
                $table->foreign('dependency_id')->references('id')->on('tasks')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }
};
