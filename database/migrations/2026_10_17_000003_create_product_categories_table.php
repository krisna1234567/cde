<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 80)->unique();
            $table->timestamps();
        });

        $names = collect(['ACC', 'Battery', 'Converter', 'Driver', 'Inverter', 'Inverter Hybrid', 'Inverter Off Grid', 'Inverter On Grid', 'LED', 'Solar Module', 'Other'])
            ->merge(DB::table('services')->whereNotNull('product_category')->pluck('product_category'))->unique();
        foreach ($names as $name) {
            DB::table('product_categories')->insert(['name' => $name, 'created_at' => now(), 'updated_at' => now()]);
        }

        Schema::table('services', function (Blueprint $table): void {
            $table->foreign('product_category')->references('name')->on('product_categories')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->dropForeign(['product_category']);
        });
        Schema::dropIfExists('product_categories');
    }
};
