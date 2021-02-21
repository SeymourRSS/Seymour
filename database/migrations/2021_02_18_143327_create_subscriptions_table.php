<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('identifier');
            $table->string('slug');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('checksum');
            $table->string('link_to_feed');
            $table->string('link_to_source')->nullable();
            $table->string('license')->nullable();
            $table->string('rights')->nullable();
            $table->timestamp('feed_timestamp');
            $table->string('variant')->nullable();
            $table->foreignId('user_id')->nullable()->index();
            $table->jsonb('extra')->nullable();
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
        Schema::dropIfExists('subscriptions');
    }
}
