<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            Schema::table('buku', function (Blueprint $table) {
                $table->string('kategori_tmp', 50)->nullable()->after('kategori');
            });

            DB::statement('UPDATE buku SET kategori_tmp = kategori');

            Schema::table('buku', function (Blueprint $table) {
                $table->dropColumn('kategori');
            });

            Schema::table('buku', function (Blueprint $table) {
                $table->renameColumn('kategori_tmp', 'kategori');
            });
        } else {
            DB::statement('ALTER TABLE buku MODIFY kategori VARCHAR(50) NOT NULL');
        }

        Schema::table('buku', function (Blueprint $table) {
            $table->foreignId('kategori_id')->nullable()->after('kategori')
                ->constrained('kategori')->nullOnDelete();
        });

        $kategoriIds = DB::table('kategori')->pluck('id', 'nama_kategori');

        foreach ($kategoriIds as $nama => $id) {
            DB::table('buku')->where('kategori', $nama)->update(['kategori_id' => $id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kategori_id');
        });
    }
};
