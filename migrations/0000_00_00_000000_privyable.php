<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Privyable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('privyids', function (Blueprint $table) {
            $table->increments('id');
            $table->string('privyId',20);
            $table->string('code');
            $table->text('token');
            $table->text('refresh_token');
            $table->text('user_token');
            $table->text('identity_response_json');
            $table->datetime('code_expired')->nullable();
            $table->datetime('token_expired');

            $table->unsignedInteger('privyable_id');
            $table->string('privyable_type');

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
        Schema::dropIfExists('privyids');
    }
}
