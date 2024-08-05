<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('basic_reference', function (Blueprint $table) {
            $table->decimal('payment', 10, 2)->nullable()->after('projManager');
        });
    }

    public function down()
    {
        Schema::table('basic_reference', function (Blueprint $table) {
            $table->dropColumn('payment');
        });
    }
};
