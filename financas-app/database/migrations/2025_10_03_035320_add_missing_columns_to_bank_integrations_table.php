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
        Schema::table('bank_integrations', function (Blueprint $table) {
            // Adiciona colunas que estão no modelo mas não na migration original
            $table->string('sync_frequency')->default('daily')->after('last_sync_at');
            $table->boolean('auto_sync')->default(true)->after('sync_frequency');
            $table->integer('error_count')->default(0)->after('auto_sync');
            $table->text('last_error')->nullable()->after('error_count');
            $table->json('configuration')->nullable()->after('last_error');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_integrations', function (Blueprint $table) {
            $table->dropColumn([
                'sync_frequency',
                'auto_sync', 
                'error_count',
                'last_error',
                'configuration'
            ]);
        });
    }
};
