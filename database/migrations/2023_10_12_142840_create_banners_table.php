<?php

use Lunar\Base\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @up
 * @down
 */
return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create($this->prefix . 'banners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banner_group_id')->constrained($this->prefix . 'banner_groups');
            $table->nestedSet();
            $table->string('name')->index();
            $table->string('link')->default(null)->index();
            $table->string('type')->default('static')->index();
            $table->json('attribute_data');
            $table->string('sort')->default('custom')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists($this->prefix . 'banners');
    }
};
