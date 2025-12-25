<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('filament_exceptions_table', function (Blueprint $table) {
            $table->id();

            // Exception details
            $table->string('type', 255);
            $table->string('code')->default('0');
            $table->longText('message');
            $table->string('file', 255);
            $table->unsignedInteger('line');
            $table->json('trace');

            // Request details
            $table->string('method', 10);
            $table->string('path', 2048);
            $table->string('ip', 45)->nullable();

            // Request data (all nullable since not always present)
            $table->json('headers')->nullable();
            $table->json('cookies')->nullable();
            $table->json('body')->nullable();
            $table->json('query')->nullable();

            // Route context (for Laravel's exception renderer components)
            $table->json('route_context')->nullable();
            $table->json('route_parameters')->nullable();

            // Markdown for copy functionality
            $table->longText('markdown')->nullable();

            $table->timestamps();

            // Index for common queries
            $table->index('created_at');
            $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('filament_exceptions_table');
    }
};
