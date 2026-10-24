<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualCardsTable extends Migration
{
    /**
     * Additive only. Stores NO full card number and NO CVV: just the last 4 digits and
     * expiry that the admin copies from the real card issuer, plus the approval status.
     */
    public function up()
    {
        Schema::create('virtual_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('status', 20)->default('pending'); // pending | approved | rejected
            $table->string('last4', 4)->nullable();
            $table->string('expiry', 5)->nullable();           // MM/YY
            $table->string('admin_note')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('virtual_cards');
    }
}
