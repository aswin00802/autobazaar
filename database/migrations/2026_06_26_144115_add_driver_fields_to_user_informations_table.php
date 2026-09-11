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
        Schema::table('user_informations', function (Blueprint $table) {
            $table->date('dob')->nullable()->after('profile');
            $table->enum('gender', [
                'male',
                'female',
                'other'
            ])->nullable()->after('dob');
            $table->string('blood_group', 5)->nullable()->after('gender');
            $table->text('address')->nullable()->after('blood_group');

           $table->unsignedTinyInteger('seating_capacity')->nullable()->after('millage');

            $table->string('driving_license_no')->nullable()->unique()->after('seating_capacity');
            $table->string('driving_license_image')->nullable()->after('driving_license_no');
            $table->date('driving_license_expiry')->nullable()->after('driving_license_image');

            $table->string('rc_book_no')->nullable()->after('driving_license_expiry');
            $table->string('rc_book_image')->nullable()->after('rc_book_no');

            $table->date('insurance_expiry')->nullable()->after('rc_book_image');
            $table->string('insurance_image')->nullable()->after('insurance_expiry');

            $table->string('permit_no')->nullable()->after('insurance_image');
            $table->date('permit_expiry')->nullable()->after('permit_no');
            $table->date('fitness_expiry')->nullable()->after('permit_expiry');

            $table->string('police_verification')->nullable()->after('fitness_expiry');

            $table->string('aadhaar_no', 20)->nullable()->unique()->after('police_verification');
            $table->string('aadhaar_copy')->nullable()->after('aadhaar_no');

            $table->index('gender');
            $table->index('blood_group');

            // Vehicle Reports
            $table->index('brand_id');
            $table->index('model_id');
            $table->index('vehicle_no');

            // Expiry Reports
            $table->index('driving_license_expiry');
            $table->index('insurance_expiry');
            $table->index('permit_expiry');
            $table->index('fitness_expiry');

            // Composite Indexes
            $table->index(['brand_id', 'model_id']);
            $table->index(['insurance_expiry', 'permit_expiry']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_informations', function (Blueprint $table) {
            $table->dropIndex(['brand_id', 'model_id']);
            $table->dropIndex(['insurance_expiry', 'permit_expiry']);

            // Drop Normal Indexes
            $table->dropIndex(['gender']);
            $table->dropIndex(['blood_group']);
            $table->dropIndex(['brand_id']);
            $table->dropIndex(['model_id']);
            $table->dropIndex(['fuel_id']);
            $table->dropIndex(['vehicle_no']);
            $table->dropIndex(['driving_license_expiry']);
            $table->dropIndex(['insurance_expiry']);
            $table->dropIndex(['permit_expiry']);
            $table->dropIndex(['fitness_expiry']);

            // Drop Columns
            $table->dropColumn([
                'dob',
                'gender',
                'blood_group',
                'address',
                'seating_capacity',
                'driving_license_no',
                'driving_license_image',
                'driving_license_expiry',
                'rc_book_no',
                'rc_book_image',
                'insurance_expiry',
                'insurance_image',
                'permit_no',
                'permit_expiry',
                'fitness_expiry',
                'police_verification',
                'aadhaar_no',
                'aadhaar_copy',
            ]);
        });
    }
};
