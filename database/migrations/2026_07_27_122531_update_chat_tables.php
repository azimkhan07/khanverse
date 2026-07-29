<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->after('order_id');
            $table->timestamp('last_message_at')->nullable()->after('project_id');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->string('message_type')->default('text')->after('attachment');
            $table->timestamp('seen_at')->nullable()->after('message_type');
            $table->dropColumn('is_seen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->boolean('is_seen')->default(0)->after('attachment');
            $table->dropColumn(['message_type','seen_at',]);
        });

        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropColumn(['project_id', 'last_message_at']);
        });
    }
};
