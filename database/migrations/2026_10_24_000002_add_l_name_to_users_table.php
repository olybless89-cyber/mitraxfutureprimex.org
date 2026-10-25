<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLNameToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('users', 'l_name')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('l_name')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('users', 'l_name')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('l_name');
        });
    }
}
