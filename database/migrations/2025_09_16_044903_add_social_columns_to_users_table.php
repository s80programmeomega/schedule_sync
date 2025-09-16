<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider_id')->nullable();
            $table->string('provider')->nullable();
            $table->text('provider_token')->nullable();
            $table->text('provider_refresh_token')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'provider_id',
                'provider_name',
                'provider_token',
                'provider_refresh_token'
            ]);
        });
    }
};
