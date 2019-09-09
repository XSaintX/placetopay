<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStateToSaleHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_headers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->enum('state',['pending', 'approved', 'rejected']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_headers', function (Blueprint $table) {
            $table->dropColumn('state');
        });
    }
}
