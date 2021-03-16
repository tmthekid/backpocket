<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no'); //->unique();
            $table->string('vendor_id');
            $table->string('order_no')->unique();
            $table->dateTime('transaction_date');
            $table->string('terminal_no')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('employee_no')->nullable();
            $table->string('sub_total')->nullable();
            $table->string('discount')->nullable();
            $table->string('total')->nullable();
            $table->string('tax_amount')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_ref')->nullable();
            $table->text('extra_info')->nullable();
            $table->text('message_id')->nullable()->comment("Parsed email message id");
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
        Schema::dropIfExists('transactions');
    }
}
