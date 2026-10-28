<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('membership_categories', 'benefits')) {
            Schema::table('membership_categories', function (Blueprint $table) {
                $table->text('benefits')->nullable()->after('name');
            });
        }

        $categoryBenefits = [
            'Student' => 'Discounted member rates, affordable wash packages, and access to standard member promotions. A valid student ID may be requested.',
            'Regular' => 'Standard access to membership services, member-only pricing, and regular wash promotions.',
            'Senior Citizen' => 'Priority assistance, discounted member rates, and access to comfortable waiting-area service. A valid senior citizen ID may be requested.',
            'Corporate' => 'Consolidated billing, vehicle record tracking, priority scheduling, and membership support for company or fleet vehicles.',
        ];

        foreach ($categoryBenefits as $name => $benefits) {
            DB::table('membership_categories')->where('name', $name)->update([
                'benefits' => $benefits,
                'updated_at' => now(),
            ]);
        }

        $levels = [
            'Basic' => [
                'fee' => 299.00,
                'validity_days' => 30,
                'benefits' => '2 standard car washes per month and member-only pricing on additional washes.',
            ],
            'Silver' => [
                'fee' => 499.00,
                'validity_days' => 30,
                'benefits' => '4 standard car washes per month, 1 interior vacuum, and access to the priority lane.',
            ],
            'Gold' => [
                'fee' => 799.00,
                'validity_days' => 30,
                'benefits' => '8 standard car washes per month, 2 interior vacuums, priority lane access, and 10% off add-on services.',
            ],
            'Platinum' => [
                'fee' => 1199.00,
                'validity_days' => 30,
                'benefits' => '12 premium car washes per month, 4 interior vacuums, priority lane access, and 15% off add-on services.',
            ],
        ];

        foreach ($levels as $name => $details) {
            DB::table('membership_levels')->where('name', $name)->update([
                ...$details,
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('membership_levels')->update([
            'fee' => null,
            'validity_days' => null,
            'benefits' => null,
            'updated_at' => now(),
        ]);

        if (Schema::hasColumn('membership_categories', 'benefits')) {
            Schema::table('membership_categories', function (Blueprint $table) {
                $table->dropColumn('benefits');
            });
        }
    }
};
