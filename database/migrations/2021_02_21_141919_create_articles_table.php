<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('identifier');
            $table->string('slug');
            $table->string('title');
            $table->string('link_to_source')->nullable();
            $table->text('content')->nullable();
            $table->text('summary')->nullable();
            $table->string('rights')->nullable();
            $table->timestamp('entry_timestamp');
            $table->jsonb('extra')->nullable();
            $table->boolean('has_been_read')->default(false);
            $table->uuid('subscription_uuid')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
