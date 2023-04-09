<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('cellphone');
            $table->string('address');
            $table->string('postal_code');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('province_id');
            $table->foreign('province_id')->references('id')
                ->on('provinces')->onDelete('cascade');
            $table->foreignId('city_id');
            $table->foreign('city_id')->references('id')
                ->on('cities')->onDelete('cascade');
            $table->softDeletes();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
