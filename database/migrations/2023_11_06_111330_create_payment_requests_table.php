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
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('initiator_email');
            $table->string('request_reference');
            $table->enum('trx_type',['deposite','withdraw']);
            $table->string('client_mobile');
            $table->string('network');
            $table->string('amount');
            $table->string('posted_reference')->nullable();
            $table->enum('status',['New','Posted','Pending','Completed','Success','Fail', 'Recived', 'Verified', 'Processed'])->default('New');
            $table->bigInteger('user_id')->unsigned()->index()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};
