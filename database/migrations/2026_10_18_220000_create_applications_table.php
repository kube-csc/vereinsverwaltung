<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('funding_pot_id')->nullable()->constrained('funding_pots')->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->text('bank_account_snapshot')->nullable();
            $table->json('form_snapshot')->nullable();
            $table->json('answers');
            $table->json('reviewer_answers')->nullable();
            $table->string('bescheid_pdf_path')->nullable();
            $table->string('status', 20)->default('entwurf');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_note')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->text('decision_reason')->nullable();
            $table->decimal('approved_amount', 12, 2)->nullable();
            $table->decimal('final_amount', 12, 2)->nullable();
            $table->timestamp('payout_exported_at')->nullable();
            $table->foreignId('payout_exported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('deletion_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index(
                ['funding_pot_id', 'user_id', 'submitted_at'],
                'applications_pot_user_submission_limit_index'
            );
            $table->index(
                ['funding_pot_id', 'club_id', 'submitted_at'],
                'applications_pot_club_submission_limit_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
