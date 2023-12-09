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
        Schema::connection('mongodb')->dropIfExists('cars');
        Schema::connection('mongodb')->create('cars', function (Blueprint $collection) {
            $collection->index('registrationPlate');
            $collection->addColumn('date','entered');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cars_collection');
    }
};
