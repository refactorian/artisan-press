<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The media_library table acts as a singleton owner for standalone media uploads
     * (images not attached to a specific model). Spatie MediaLibrary requires every
     * media record to have a polymorphic owner, so we use this table as that owner.
     */
    public function up(): void
    {
        Schema::create('media_library', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Global Media Library');
            $table->timestamps();
        });

        // Seed the singleton row so it always exists.
        DB::table('media_library')->insert([
            'id' => 1,
            'name' => 'Global Media Library',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_library');
    }
};
