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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId("buyer_id")->constrained()->onDelete("cascade");
            $table->foreignId("seller_id")->constrained()->onDelete("cascade");
            $table->foreignId("gig_id")->constrained()->onDelete("cascade");
            $table->integer("quantity")->default(1);
            $table->decimal("total_price",8,2);
            $table->string("status")->default("pending");
            $table->string("payment_status")->default("pending");
            $table->text("modification_request")->nullable();
            $table->string("transaction_reference")->unique();
            $table->timestamp("order_date")->useCurrent();
            $table->timestamp("delivery_date")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
