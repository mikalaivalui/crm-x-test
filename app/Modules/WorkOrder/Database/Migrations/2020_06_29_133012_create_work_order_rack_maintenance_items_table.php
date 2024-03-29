<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkOrderRackMaintenanceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('work_order_rack_maintenance_items')) {
            Schema::create('work_order_rack_maintenance_items', function (Blueprint $table) {
                $table->increments('id');
            });
        }

        $columns = Schema::getColumnListing('work_order_rack_maintenance_items');

        Schema::table('work_order_rack_maintenance_items', function (Blueprint $table) use ($columns) {
            if (!in_array('id', $columns)) {
                $table->increments('id');
            }

            if (!in_array('work_order_id', $columns)) {
                $table->integer('work_order_id')->unsigned();
            }

            if (!in_array('link_person_wo_id', $columns)) {
                $table->integer('link_person_wo_id')->unsigned();
            }

            if (!in_array('name', $columns)) {
                $table->string('name');
            }

            if (!in_array('start_at', $columns)) {
                $table->dateTime('start_at');
            }

            if (!in_array('stop_at', $columns)) {
                $table->dateTime('stop_at')->nullable();
            }

            if (!in_array('notification_sent', $columns)) {
                $table->boolean('notification_sent')->default(false);
            }

            //$table->timestamps();
        });
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
    }
}
