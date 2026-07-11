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
        Schema::table('project_attachments', function (Blueprint $table) {

            $table->enum('uploaded_by', [
                'admin',
                'seller',
                'buyer',
            ]);

            $table->string('file_name');

            $table->string('file_path');

            $table->unsignedBigInteger('file_size')->default(0);

            $table->string('mime_type')->nullable();

            $table->index('user_id');

            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_attachments', function (Blueprint $table) {
            //
        });
    }
};
