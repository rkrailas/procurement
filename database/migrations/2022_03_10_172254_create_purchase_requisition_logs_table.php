<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequisitionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pr_history_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_original');
            $table->string('obj_type');
            $table->string('line_no');
            $table->string('prno');
            $table->string('field');
            $table->string('old_value')->nullable();
            $table->string('new_value');
            $table->string('created_by')->nullable();;
            $table->string('changed_by')->nullable();;
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
        Schema::dropIfExists('pr_history_logs');
    }
}
