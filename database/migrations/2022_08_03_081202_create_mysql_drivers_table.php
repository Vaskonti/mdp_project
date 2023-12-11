<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mysql')->create('drivers', static function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->text('description');
            $table->string('egn');
            $table->string('image');
            $table->string('email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists('drivers');
    }

};
