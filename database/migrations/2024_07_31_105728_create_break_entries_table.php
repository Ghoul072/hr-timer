<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\TimeEntry;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('break_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TimeEntry::class);
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->string('start_location')->nullable();
            $table->string('end_location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('break_entries');
    }
};
