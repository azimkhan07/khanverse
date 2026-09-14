<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->string('aadhaar_number')->nullable()->after('available_for_work');
            $table->string('aadhaar_document')->nullable()->after('aadhaar_number');
            $table->string('pan_number')->nullable()->after('aadhaar_document');
            $table->string('pan_document')->nullable()->after('pan_number');
            $table->enum('kyc_status', ['pending', 'submitted', 'verified', 'rejected'])->default('pending')->after('pan_document');
            $table->string('kyc_rejection_reason')->nullable()->after('kyc_status');
        });

        Schema::table('buyers', function (Blueprint $table) {
            $table->string('verification_document')->nullable()->after('profile_image');
            $table->enum('verification_status', ['pending', 'submitted', 'verified', 'rejected'])->default('pending')->after('verification_document');
            $table->string('verification_rejection_reason')->nullable()->after('verification_status');
            $table->boolean('is_consultancy')->default(false)->after('verification_rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn(['aadhaar_number', 'aadhaar_document', 'pan_number', 'pan_document', 'kyc_status', 'kyc_rejection_reason']);
        });

        Schema::table('buyers', function (Blueprint $table) {
            $table->dropColumn(['verification_document', 'verification_status', 'verification_rejection_reason', 'is_consultancy']);
        });
    }
};