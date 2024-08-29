<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagemapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imagemaps', function (Blueprint $table) {
            $table->id();
            $table->string('id_group');
            $table->string('coordinate');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('device_type')->nullable();
            $table->string('status');
            $table->string('meta')->nullable();
            $table->string('shape');
            $table->string('id_asset')->nullable();
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
        Schema::dropIfExists('imagemaps');
    }
}
