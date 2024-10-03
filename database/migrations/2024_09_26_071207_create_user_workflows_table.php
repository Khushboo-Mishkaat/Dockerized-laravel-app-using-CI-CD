<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserWorkflowsTable extends Migration
{
    public function up()
    {
        Schema::create('user_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('user_filter', [
                'PreviouslySubscribed', 
                'Subscribed', 
                'NoSubscription', 
                'Guest', 
                'Inactive',
                'All'
            ]);
            $table->boolean('is_active')->default(true);
            $table->string('notification_title');
            $table->text('notification_text');
            $table->enum('recurring_duration', [
                'Daily', 
                'Weekly', 
                'Monthly'
            ]);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_workflows');
    }
}
