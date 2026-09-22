<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Customer-generated content and leads for the vehicle detail page.
 *
 * Reviews need admin approval before they show. Enquiries hold every lead
 * type the page produces (Enquire / Quotation / Test drive / Loan) in one
 * table so the admin Leads screen has a single pipeline.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Skips a table that is already there: the live database was built
        // from a dump, so many tables exist without this ever having run.
        Schema::hasTable('vehicle_reviews') || Schema::create('vehicle_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_model_id')->index();
            $table->integer('user_id')->nullable()->index();
            $table->string('name', 80);
            $table->string('city', 80)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->tinyInteger('rating')->default(5);                       // 1..5
            $table->string('title')->nullable();
            $table->text('body');
            $table->tinyInteger('is_verified')->default(0);                  // verified buyer
            $table->string('review_status', 20)->default('pending');        // pending | approved | rejected
            $table->integer('status_id')->default(1);
            $table->timestamps();
            $table->string('ip_address')->nullable();
        });

        Schema::hasTable('vehicle_enquiries') || Schema::create('vehicle_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('enquiry_no', 30)->unique();                      // VE-0001
            $table->unsignedBigInteger('vehicle_model_id')->nullable()->index();
            $table->unsignedBigInteger('vehicle_variant_id')->nullable();
            $table->integer('user_id')->nullable()->index();
            $table->string('name', 80);
            $table->string('mobile', 20)->index();
            $table->string('email', 120)->nullable();
            $table->string('state', 60)->nullable();
            $table->string('district', 80)->nullable();
            $table->string('city', 80)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('source', 20)->default('enquiry');               // enquiry | quotation | test_drive | loan | call | whatsapp
            $table->dateTime('preferred_at')->nullable();                    // test-drive date
            $table->string('time_slot', 30)->nullable();
            $table->string('buying_timeframe', 40)->nullable();
            $table->json('buying_options')->nullable();
            $table->decimal('loan_amount', 10, 2)->nullable();
            $table->text('message')->nullable();
            $table->string('lead_status', 20)->default('new');              // new | contacted | test_drive | documents | loan | booked | delivered | closed
            $table->integer('assigned_to')->nullable();
            $table->tinyInteger('otp_verified')->default(0);
            $table->string('page_url')->nullable();
            $table->text('admin_note')->nullable();
            $table->integer('status_id')->default(1);
            $table->timestamps();
            $table->string('ip_address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_enquiries');
        Schema::dropIfExists('vehicle_reviews');
    }
};
