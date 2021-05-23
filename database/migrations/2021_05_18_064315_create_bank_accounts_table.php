<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('user_id');
            $table->string('number')->unique();
            $table->string('currency')->default('EUR');
            $table->bigInteger('amount')->default(0);
            $table->boolean('type')->default(0);
            $table->bigInteger('deposit')->default(0);
            $table->bigInteger('withdraw')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
