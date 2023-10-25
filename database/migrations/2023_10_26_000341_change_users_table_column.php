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
        // カラム名を変更
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('store', 'store_id');
        });

        // データ型をBIGINTに変更
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('store_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // カラム名を元に戻す
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('store_id', 'store');
        });

        // データ型を元に戻す（例としてINTに変更する）
        Schema::table('users', function (Blueprint $table) {
            $table->integer('store')->change();
        });
    }
};
