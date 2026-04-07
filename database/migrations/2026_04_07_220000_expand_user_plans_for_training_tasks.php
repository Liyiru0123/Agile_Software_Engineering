<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_plans') || Schema::hasColumn('user_plans', 'plan_kind')) {
            return;
        }

        Schema::create('user_plans_tmp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('article_id')->nullable()->constrained()->nullOnDelete();
            $table->date('plan_date');
            $table->enum('plan_kind', ['article', 'skill', 'custom'])->default('article');
            $table->string('title')->nullable();
            $table->enum('skill_type', ['listening', 'speaking'])->nullable();
            $table->unsignedTinyInteger('target_count')->nullable();
            $table->enum('status', ['pending', 'completed', 'skipped'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->index(['user_id', 'plan_date', 'status']);
        });

        $plans = DB::table('user_plans')->orderBy('id')->get();

        foreach ($plans as $plan) {
            DB::table('user_plans_tmp')->insert([
                'id' => $plan->id,
                'user_id' => $plan->user_id,
                'article_id' => $plan->article_id,
                'plan_date' => $plan->plan_date,
                'plan_kind' => 'article',
                'title' => null,
                'skill_type' => null,
                'target_count' => null,
                'status' => $plan->status,
                'completed_at' => $plan->completed_at,
            ]);
        }

        Schema::drop('user_plans');
        Schema::rename('user_plans_tmp', 'user_plans');
    }

    public function down(): void
    {
        if (! Schema::hasTable('user_plans') || ! Schema::hasColumn('user_plans', 'plan_kind')) {
            return;
        }

        Schema::create('user_plans_legacy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->date('plan_date');
            $table->enum('status', ['pending', 'completed', 'skipped'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->index(['user_id', 'plan_date', 'status']);
        });

        $plans = DB::table('user_plans')
            ->where('plan_kind', 'article')
            ->whereNotNull('article_id')
            ->orderBy('id')
            ->get();

        foreach ($plans as $plan) {
            DB::table('user_plans_legacy')->insert([
                'id' => $plan->id,
                'user_id' => $plan->user_id,
                'article_id' => $plan->article_id,
                'plan_date' => $plan->plan_date,
                'status' => $plan->status,
                'completed_at' => $plan->completed_at,
            ]);
        }

        Schema::drop('user_plans');
        Schema::rename('user_plans_legacy', 'user_plans');
    }
};
