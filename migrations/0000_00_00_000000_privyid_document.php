<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrivyidDocument extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('privyid_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('type');
            $table->text('owner');
            $table->string('document');
            $table->text('recipients');
            $table->text('token');
            $table->string('url',255);

            $table->text('document_response_json');
            $table->string('codification');
            $table->text('status_response_json');
            $table->text('status_recipients');
            $table->dateTime('last_status_updated');

            $table->unsignedInteger('privy_uploadable_id');
            $table->string('privy_uploadable_type');

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
        Schema::dropIfExists('privyid_documents');
    }
}
