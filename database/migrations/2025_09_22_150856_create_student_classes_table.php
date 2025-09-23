<?php
// create the student class table with column('id', 'user_id', 'class_completed', 'status'. 'cert')

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
        Schema::create('student_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->json('class_completed');
            $table->enum('status', ['completed', 'attending', 'stopped', 'started'])->default('started');
            $table->string('cert')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_classes');
    }
};
