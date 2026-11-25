<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Extend academic_intakes for Table 5.1.1
        Schema::table('academic_intakes', function (Blueprint $table) {
            if (!Schema::hasColumn('academic_intakes', 'admitted_first_year')) {
                $table->integer('admitted_first_year')->default(0)->after('ug_4th_year');
            }
            if (!Schema::hasColumn('academic_intakes', 'admitted_lateral_entry')) {
                $table->integer('admitted_lateral_entry')->default(0)->after('admitted_first_year');
            }
            if (!Schema::hasColumn('academic_intakes', 'admitted_leftover_seats')) {
                $table->integer('admitted_leftover_seats')->default(0)->after('admitted_lateral_entry');
            }
            if (!Schema::hasColumn('academic_intakes', 'sanctioned_intake')) {
                $table->integer('sanctioned_intake')->nullable()->after('academic_year_id');
            }
        });

        // 2. Extend faculty_academic_years for Table 5.1.2
        Schema::table('faculty_academic_years', function (Blueprint $table) {
            if (!Schema::hasColumn('faculty_academic_years', 'is_allied_department')) {
                $table->boolean('is_allied_department')->default(false)->after('association_type');
            }
            if (!Schema::hasColumn('faculty_academic_years', 'first_year_teaching_load_percentage')) {
                $table->decimal('first_year_teaching_load_percentage', 5, 2)->default(0.00)->after('is_allied_department');
            }
        });

        // 3. Create visiting_faculties table for 5.4
        if (!Schema::hasTable('visiting_faculties')) {
            Schema::create('visiting_faculties', function (Blueprint $table) {
                $table->id();
                $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
                $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
                $table->string('name');
                $table->string('designation');
                $table->string('organization');
                $table->string('course_name');
                $table->string('course_code')->nullable();
                $table->string('contribution_type')->default('Teaching / Project Guidance');
                $table->decimal('interaction_hours', 8, 2)->default(0.00);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('visiting_faculties');

        Schema::table('faculty_academic_years', function (Blueprint $table) {
            if (Schema::hasColumn('faculty_academic_years', 'first_year_teaching_load_percentage')) {
                $table->dropColumn('first_year_teaching_load_percentage');
            }
            if (Schema::hasColumn('faculty_academic_years', 'is_allied_department')) {
                $table->dropColumn('is_allied_department');
            }
        });

        Schema::table('academic_intakes', function (Blueprint $table) {
            if (Schema::hasColumn('academic_intakes', 'sanctioned_intake')) {
                $table->dropColumn('sanctioned_intake');
            }
            if (Schema::hasColumn('academic_intakes', 'admitted_leftover_seats')) {
                $table->dropColumn('admitted_leftover_seats');
            }
            if (Schema::hasColumn('academic_intakes', 'admitted_lateral_entry')) {
                $table->dropColumn('admitted_lateral_entry');
            }
            if (Schema::hasColumn('academic_intakes', 'admitted_first_year')) {
                $table->dropColumn('admitted_first_year');
            }
        });
    }
};
