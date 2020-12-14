<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'mail_jobs',
            function (Blueprint $table) {
                $table->id();
                $table->string('from');
                $table->string('to');
                $table->string('subject')->nullable();
                $table->text('content')->nullable();
                $table->string('state');
                $table->string('sender_third_party_provider_name')->nullable();

                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mails');
    }
}
