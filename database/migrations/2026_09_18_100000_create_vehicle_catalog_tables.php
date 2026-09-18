<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Vehicle model catalogue (new-auto detail page, catalogue grid, compare).
 *
 * One `vehicle_models` row per marketed model (TVS King Deluxe); fuel/engine
 * variants, specs, images, city prices, scores and content hang off it.
 * `auto_posts` is NOT touched — it stays the used-auto listing table.
 *
 * Nothing here alters an existing table, so the migration is safe on live.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auto_brand_id')->index();          // auto_brands.id
            $table->unsignedBigInteger('auto_model_id')->nullable()->index(); // auto_models.id (master link)
            $table->string('name');                                          // TVS King Deluxe
            $table->string('slug', 120)->unique();                           // tvs-king-deluxe
            $table->string('model_slug', 80);                                // king-deluxe  (URL: /new-autos/tvs/king-deluxe)
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('badge', 40)->nullable();                         // Most Popular / EV / New
            $table->string('segment', 30)->default('passenger');             // passenger | cargo
            $table->tinyInteger('seating')->default(3);
            $table->json('use_case')->nullable();                            // ["commercial","high-mileage"]
            $table->string('image')->nullable();                             // hero image
            $table->string('maintenance_level', 20)->default('Low');         // compare table
            $table->string('best_for', 60)->nullable();                      // compare table

            // Cached rating (recomputed from approved reviews)
            $table->decimal('rating_avg', 2, 1)->default(0);
            $table->integer('rating_count')->default(0);

            // AutoBazaar score
            $table->decimal('score_overall', 3, 1)->default(0);
            $table->string('score_rank_note', 80)->nullable();
            $table->string('score_summary')->nullable();

            // Warranty & service card
            $table->tinyInteger('warranty_years')->nullable();
            $table->integer('warranty_km')->nullable();
            $table->tinyInteger('engine_warranty_years')->nullable();
            $table->integer('engine_warranty_km')->nullable();
            $table->integer('service_interval_km')->nullable();
            $table->tinyInteger('service_interval_months')->nullable();
            $table->tinyInteger('free_services')->nullable();
            $table->integer('free_services_km')->nullable();

            // Showroom card (authorized_sellers.id)
            $table->unsignedBigInteger('dealer_id')->nullable()->index();

            $table->integer('popularity')->default(0);
            $table->tinyInteger('is_popular')->default(0);
            $table->integer('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            $table->integer('status_id')->default(1);                        // 1 live, 0 draft
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->string('ip_address')->nullable();
        });

        Schema::create('vehicle_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_model_id')->index();
            $table->string('name', 60);                                      // Petrol / CNG / Electric
            $table->string('fuel_key', 20)->default('petrol');               // petrol|cng|lpg|diesel|electric
            $table->unsignedBigInteger('fuel_type_id')->nullable();          // auto_fuel_types.id (live fuel price)
            $table->string('icon', 20)->default('fuel');
            $table->string('engine_cc', 40)->nullable();
            $table->string('power', 40)->nullable();
            $table->decimal('mileage', 6, 1)->nullable();
            $table->string('mileage_unit', 20)->default('km/litre');
            $table->integer('payload_kg')->nullable();
            $table->string('transmission', 40)->nullable();
            $table->decimal('ex_showroom_price', 10, 2)->default(0);
            $table->tinyInteger('is_default')->default(0);
            $table->integer('sort_order')->default(0);
            $table->integer('status_id')->default(1);
            $table->timestamps();
        });

        Schema::create('vehicle_specifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_model_id')->index();
            $table->string('spec_group', 40)->default('General');            // Engine / Dimensions / Capacity …
            $table->string('label', 80);
            $table->string('value');
            $table->tinyInteger('is_key')->default(0);                        // shown in "Key Specifications"
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('vehicle_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_model_id')->index();
            $table->string('image');
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('status_id')->default(1);
            $table->timestamps();
        });

        // On-road price per location. ex_showroom is stored per row so a
        // district can differ; on_road = ex_showroom + rto + insurance + registration + other + accessories.
        Schema::create('vehicle_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_model_id')->index();
            $table->unsignedBigInteger('vehicle_variant_id')->nullable()->index(); // null = applies to default variant
            $table->string('location', 80)->default('Chennai');               // district / city name
            $table->string('state', 60)->default('Tamil Nadu');
            $table->decimal('ex_showroom', 10, 2)->default(0);
            $table->decimal('rto', 10, 2)->default(0);
            $table->decimal('insurance', 10, 2)->default(0);
            $table->decimal('registration', 10, 2)->default(0);
            $table->decimal('other', 10, 2)->default(0);
            $table->decimal('accessories', 10, 2)->default(0);
            $table->tinyInteger('is_default')->default(0);
            $table->integer('status_id')->default(1);
            $table->timestamps();
        });

        Schema::create('vehicle_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_model_id')->index();
            $table->string('label', 60);                                     // Mileage, Comfort …
            $table->decimal('score', 3, 1)->default(0);                       // out of 10
            $table->string('tone', 20)->default('brand');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Feature strip + "Why choose this auto" benefit points
        Schema::create('vehicle_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_model_id')->index();
            $table->string('icon', 30)->default('check');
            $table->string('label', 80);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('vehicle_suitability', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_model_id')->index();
            $table->string('type', 20)->default('suitable');                 // suitable | not_recommended
            $table->string('label', 80);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Offers auto-hide outside valid_from..valid_to
        Schema::create('vehicle_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_model_id')->index();
            $table->string('title');
            $table->decimal('value_amount', 10, 2)->nullable();               // ₹ worth, summed for "Offers worth"
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('status_id')->default(1);
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_model_id')->index();
            $table->string('type', 30)->default('brochure');                 // brochure | price_list | specification | other
            $table->string('title');
            $table->string('file');
            $table->integer('sort_order')->default(0);
            $table->integer('status_id')->default(1);
            $table->timestamps();
        });

        // Availability & delivery card
        Schema::create('vehicle_stock', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_model_id')->index();
            $table->unsignedBigInteger('vehicle_variant_id')->nullable()->index();
            $table->unsignedBigInteger('dealer_id')->nullable()->index();     // authorized_sellers.id
            $table->string('colour', 40)->nullable();
            $table->integer('qty')->default(0);
            $table->tinyInteger('delivery_days_min')->default(1);
            $table->tinyInteger('delivery_days_max')->default(3);
            $table->integer('status_id')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'vehicle_stock', 'vehicle_documents', 'vehicle_offers', 'vehicle_suitability', 'vehicle_features',
            'vehicle_scores', 'vehicle_prices', 'vehicle_images', 'vehicle_specifications', 'vehicle_variants',
            'vehicle_models',
        ] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
