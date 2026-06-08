<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // CMS-driven bridge landers. `content` holds structured, typed fields per
        // section (fixed slot counts) so marketing can edit copy/links/images via the
        // admin without ever touching the layout. `template` picks the render template.
        Schema::create('landers', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();        // public /lp/{slug}
            $table->string('name');                  // admin label
            $table->string('template')->default('research-confidence'); // blade template key
            $table->string('outbound_slug')->nullable();   // CTA OutboundLink slug (/go)
            $table->boolean('is_active')->default(true);
            $table->boolean('noindex')->default(true);
            $table->json('content')->nullable();     // structured editable content
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landers');
    }
};
