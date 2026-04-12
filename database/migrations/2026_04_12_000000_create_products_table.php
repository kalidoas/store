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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', [
                'Téléphones',
                'TV',
                'Audio',
                'Informatique',
                'Électroménager',
                'Accessoires',
                'Autre',
            ]);
            $table->integer('quantity')->default(0);
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('transport_fees', 10, 2)->default(0);
            $table->decimal('other_fees', 10, 2)->default(0);
            $table->decimal('selling_price', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

