<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('homework_id');
            $table->text('description');
            $table->uuid('user_id');
            $table->timestamps();

            $table->primary('id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('homework_id')->references('id')->on('homeworks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('submissions');
    }
}
