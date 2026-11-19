<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_bantuans', function (Blueprint $table) {
            if (!Schema::hasColumn('pengajuan_bantuans', 'jumlah_alsintan_eksisting')) {
                $table->unsignedInteger('jumlah_alsintan_eksisting')->default(0)->after('jumlah_diminta');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_bantuans', function (Blueprint $table) {
            if (Schema::hasColumn('pengajuan_bantuans', 'jumlah_alsintan_eksisting')) {
                $table->dropColumn('jumlah_alsintan_eksisting');
            }
        });
    }
};

