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
        Schema::create('campanhas_emails', function (Blueprint $table) {

            $table->integer('id', true)->unsigned();
            $table->string('uniqueid', 120)->unique()->notNullable();
            $table->integer('outbounds_fk')->unsigned();
            $table->integer('campanhas_fk')->unsigned();

            $table->timestamps();

            $table->unique(['campanhas_fk','outbouds_fk']);

            $table->foreign('campanhas_fk')->references('id')->on('campanhas');
            $table->foreign('outbounds_fk')->references('id')->on('outbounds');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campanhas_email');
    }
};
