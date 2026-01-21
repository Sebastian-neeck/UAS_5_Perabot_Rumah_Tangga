<?php
// database/migrations/2026_01_20_remove_json_check_from_furniture.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // MySQL tidak support DROP CHECK CONSTRAINT langsung
        // Kita perlu alter table untuk modify column
        
        Schema::table('furniture', function (Blueprint $table) {
            // Hapus constraint dengan cara modify column
            DB::statement('ALTER TABLE furniture MODIFY COLUMN images JSON NULL');
        });
    }

    public function down()
    {
        // Optional: bisa ditambahkan constraint kembali
        Schema::table('furniture', function (Blueprint $table) {
            DB::statement('ALTER TABLE furniture MODIFY COLUMN images JSON CHECK (JSON_VALID(images))');
        });
    }
};