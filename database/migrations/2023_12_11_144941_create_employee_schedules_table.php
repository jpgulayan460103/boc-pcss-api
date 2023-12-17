<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('schedule_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('schedule_shift_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->date('working_date')->nullable();
            $table->boolean('is_overtime')->default(0);
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
        Schema::dropIfExists('employee_schedules');
    }
}
