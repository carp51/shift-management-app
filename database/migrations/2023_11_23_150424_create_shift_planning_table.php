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
        Schema::create('plannings', function (Blueprint $table) {
            $table->id();
            $table->date('start_date')->comment('開始日');
            $table->date('end_date')->comment('終了日');
            $table->integer('shift_type')->comment('シフトの種類');
            $table->bigInteger('store_id')->comment('店舗ID');
            $table->integer('need_number')->comment('シフトに必要な人数');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Plannings');
    }
};
