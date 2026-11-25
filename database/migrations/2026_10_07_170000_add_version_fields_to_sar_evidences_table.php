<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sar_evidences', function (Blueprint $table) {
            $table->unsignedInteger('version')->default(1)->after('sar_criterion_id');
            $table->string('previous_file_path')->nullable()->after('file_path');
            $table->text('upload_reason')->nullable()->after('uploaded_by');
        });
    }

    public function down(): void
    {
        Schema::table('sar_evidences', function (Blueprint $table) {
            $table->dropColumn(['version', 'previous_file_path', 'upload_reason']);
        });
    }
};
