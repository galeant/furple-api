<?php

use App\Models\Breed;
use App\Models\Color;
use App\Models\Type;
use App\Models\User;
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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('name');
            $table->text('bio');
            $table->foreignIdFor(Type::class);
            $table->foreignIdFor(Breed::class);
            $table->foreignIdFor(Color::class);
            $table->integer('age_year');
            $table->integer('age_month');
            $table->unsignedInteger('gender');

            $table->integer('playfullness');
            $table->integer('active_level');
            $table->integer('friendliness');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
