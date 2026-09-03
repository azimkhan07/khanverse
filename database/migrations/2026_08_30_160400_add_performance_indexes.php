<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Performance migration — adds composite/single indexes on the hot query
 * and join columns that were created without indexes (n+1 / slow-query guard).
 *
 * Every index below maps to a real query path in the codebase:
 *  - orders.buyer_id / seller_id / service_id / status -> buyer/seller/admin order lists + filters
 *  - projects.buyer_id / seller_id / status -> role-based project lists + filters
 *  - services.seller_id / category_id / status -> catalog + seller services
 *  - wallet_transactions.wallet_id -> wallet ledger (per-wallet fetch)
 *  - project_hostings.project_id / project_deliveries.project_id -> delivery handover
 *  - notifications.user_id -> per-user notification inbox
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index('service_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index('delivery_method');
        });

        Schema::table('project_hostings', function (Blueprint $table) {
            $table->index('project_id');
            $table->index('order_id');
            $table->index('buyer_id');
        });

        Schema::table('project_deliveries', function (Blueprint $table) {
            $table->index('project_id');
            $table->index('order_id');
            $table->index('seller_id');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->index(['seller_id', 'status']);
            $table->index('category_id');
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->index('wallet_id');
            $table->index('reference_type');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['buyer_id', 'status']);
            $table->dropIndex(['seller_id', 'status']);
            $table->dropIndex(['service_id']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['buyer_id', 'status']);
            $table->dropIndex(['seller_id', 'status']);
            $table->dropIndex(['delivery_method']);
        });

        Schema::table('project_hostings', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->dropIndex(['order_id']);
            $table->dropIndex(['buyer_id']);
        });

        Schema::table('project_deliveries', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->dropIndex(['order_id']);
            $table->dropIndex(['seller_id']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['seller_id', 'status']);
            $table->dropIndex(['category_id']);
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex(['wallet_id']);
            $table->dropIndex(['reference_type']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_read']);
        });
    }
};