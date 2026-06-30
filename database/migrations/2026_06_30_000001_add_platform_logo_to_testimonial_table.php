<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlatformLogoToTestimonialTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('testimonial') || Schema::hasColumn('testimonial', 'platform_logo')) {
            return;
        }

        Schema::table('testimonial', function (Blueprint $table) {
            // Platform logo for optional review source branding.
            $table->string('platform_logo', 500)->nullable()->after('image');
        });
    }

    public function down()
    {
        if (!Schema::hasTable('testimonial') || !Schema::hasColumn('testimonial', 'platform_logo')) {
            return;
        }

        Schema::table('testimonial', function (Blueprint $table) {
            $table->dropColumn('platform_logo');
        });
    }
}
