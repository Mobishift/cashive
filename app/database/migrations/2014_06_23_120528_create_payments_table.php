<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::create('payments', function($table)
	    {
	        $table->increments('id');
	        $table->string('ct_payment_id', 128)->default('')->index();
	        $table->integer('campaign_id');
	        $table->string('out_trade_no', 64)->default('')->index();
            $table->string('uid', 128)->default('')->index();
            $table->smallInteger('status')->default(0)->index();
            $table->string('payment_primary_type', 64)->default('')->index();
            $table->string('payment_sub_type', 64)->default('')->index();
            $table->double('amount', 15, 2);
            $table->double('user_fee_amount', 15, 2)->default(0);
            $table->double('admin_fee_amount', 15, 2)->default(0);
            $table->string('redirect_url', 200);
            $table->string('failure_url', 200);
            $table->string('payment_url', 200);
            $table->integer('quantity');
            $table->integer('reward_id')->index();
            $table->string('additional_info')->default('');
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
		Schema::drop('payments');
	}

}
