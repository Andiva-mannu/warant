<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('warranties', function (Blueprint $table) {
            $table->enum('status', ['active', 'claimed', 'expired'])
                  ->nullable()
                  ->after('expiry_date');
        });
    }

    public function down()
    {
        Schema::table('warranties', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};