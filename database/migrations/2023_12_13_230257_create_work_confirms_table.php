<?php

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
        Schema::create('work_confirms', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_id')->comment('店舗ID');
            $table->date('month')->comment('確定月');
            $table->boolean('confirm_status')->comment('確定しているかどうか');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_confirms');
    }
};
