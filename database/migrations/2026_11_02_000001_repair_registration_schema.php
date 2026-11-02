<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Registration depends on schema that the older migrations never defined.
 * Every step checks first, so this is safe on an imported database and on
 * every boot (docker/start.sh runs this file on its own).
 */
class RepairRegistrationSchema extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username', 191)->nullable()->unique()->after('name');
            });
        }

        if (! Schema::hasTable('crypto_accounts')) {
            Schema::create('crypto_accounts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('user_id')->nullable()->index();
                foreach (['btc', 'eth', 'ltc', 'xrp', 'link', 'bnb', 'aave', 'usdt', 'xlm', 'bch', 'ada'] as $coin) {
                    $table->string($coin)->nullable();
                }
                $table->timestamps();
            });
        }

        // The dashboard stores the word "received" here, so an integer column
        // crashes the first dashboard load after sign-up.
        $column = DB::selectOne(
            'SELECT DATA_TYPE AS type FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            ['users', 'signup_bonus']
        );

        if ($column && stripos($column->type, 'int') !== false) {
            DB::statement('ALTER TABLE users MODIFY signup_bonus VARCHAR(191) NULL');
        }
    }

    public function down()
    {
        // Repair only: nothing to undo.
    }
}
