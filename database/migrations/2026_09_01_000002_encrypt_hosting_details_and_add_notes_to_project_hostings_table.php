<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class EncryptHostingDetailsAndAddNotesToProjectHostingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Sensitive hosting fields are stored encrypted (ciphertext expands the
     * original value), so widen them to TEXT. Only host + username stay
     * plaintext so the seller can identify a project before unlocking the key.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE project_hostings MODIFY provider TEXT NULL');
        DB::statement('ALTER TABLE project_hostings MODIFY domain TEXT NULL');
        DB::statement('ALTER TABLE project_hostings MODIFY port TEXT NULL');
        DB::statement('ALTER TABLE project_hostings MODIFY protocol TEXT NULL');
        DB::statement('ALTER TABLE project_hostings ADD notes TEXT NULL AFTER username');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Ciphertext no longer guaranteed to fit in the original sized columns,
        // so leave the widened columns in place to avoid truncating data.
    }
}