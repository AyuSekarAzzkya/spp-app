<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_proofs', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_proofs', 'version')) {
                $table->unsignedInteger('version')->default(1)->after('note');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_proofs', function (Blueprint $table) {
            if (Schema::hasColumn('payment_proofs', 'version')) {
                $table->dropColumn('version');
            }
        });
    }
};
