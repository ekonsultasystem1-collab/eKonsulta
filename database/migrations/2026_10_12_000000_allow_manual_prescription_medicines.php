<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->dropForeign(['medicine_id']);
            $table->unsignedBigInteger('medicine_id')->nullable()->change();
            $table->string('medicine_name')->nullable()->after('medicine_id');
            $table->foreign('medicine_id')
                ->references('id')
                ->on('medicines')
                ->restrictOnDelete();
        });

        DB::table('prescription_items')
            ->whereNull('medicine_name')
            ->whereNotNull('medicine_id')
            ->orderBy('id')
            ->chunkById(100, function ($items): void {
                foreach ($items as $item) {
                    $medicineName = DB::table('medicines')
                        ->where('id', $item->medicine_id)
                        ->value('name');

                    if ($medicineName !== null) {
                        DB::table('prescription_items')
                            ->where('id', $item->id)
                            ->update(['medicine_name' => $medicineName]);
                    }
                }
            });
    }

    public function down(): void
    {
        if (DB::table('prescription_items')->whereNull('medicine_id')->exists()) {
            throw new RuntimeException(
                'Cannot reverse manual prescription medicines while prescriptions still reference them.'
            );
        }

        Schema::table('prescription_items', function (Blueprint $table) {
            $table->dropForeign(['medicine_id']);
            $table->dropColumn('medicine_name');
            $table->unsignedBigInteger('medicine_id')->nullable(false)->change();
            $table->foreign('medicine_id')
                ->references('id')
                ->on('medicines')
                ->restrictOnDelete();
        });
    }
};
