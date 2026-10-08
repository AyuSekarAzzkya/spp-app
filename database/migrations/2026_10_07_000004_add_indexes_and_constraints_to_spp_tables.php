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
        // 1. Table: students
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'deleted_at')) {
                $table->softDeletes();
            }
            $table->index(['status', 'class_id'], 'students_status_class_index');
            $table->index('status', 'students_status_index');
        });

        // 2. Table: spp_rates (1 rate per academic year)
        Schema::table('spp_rates', function (Blueprint $table) {
            $table->unique('academic_year_id', 'spp_rates_academic_year_unique');
        });

        // 3. Table: bills (Prevent duplicate bill + speed up queries)
        Schema::table('bills', function (Blueprint $table) {
            $table->unique(['student_id', 'month', 'year'], 'bills_student_month_year_unique');
            $table->index(['student_id', 'status'], 'bills_student_status_index');
            $table->index('status', 'bills_status_index');
            $table->index(['year', 'month'], 'bills_year_month_index');
            $table->index('due_date', 'bills_due_date_index');
        });

        // 4. Table: payments
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['student_id', 'status'], 'payments_student_status_index');
            $table->index('status', 'payments_status_index');
            $table->index('payment_date', 'payments_payment_date_index');
        });

        // 5. Table: payment_details (Prevent duplicate bill in 1 payment)
        Schema::table('payment_details', function (Blueprint $table) {
            $table->unique(['payment_id', 'bill_id'], 'payment_details_payment_bill_unique');
            $table->index('bill_id', 'payment_details_bill_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->dropUnique('payment_details_payment_bill_unique');
            $table->dropIndex('payment_details_bill_id_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_student_status_index');
            $table->dropIndex('payments_status_index');
            $table->dropIndex('payments_payment_date_index');
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->dropUnique('bills_student_month_year_unique');
            $table->dropIndex('bills_student_status_index');
            $table->dropIndex('bills_status_index');
            $table->dropIndex('bills_year_month_index');
            $table->dropIndex('bills_due_date_index');
        });

        Schema::table('spp_rates', function (Blueprint $table) {
            $table->dropUnique('spp_rates_academic_year_unique');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('students_status_class_index');
            $table->dropIndex('students_status_index');
            if (Schema::hasColumn('students', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
