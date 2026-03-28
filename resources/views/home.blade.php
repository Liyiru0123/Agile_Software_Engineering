@extends('layouts.app')

@section('title', 'Dashboard - Academic English Learning')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    
    <!-- 页面标题 -->
    <div class="mb-8">
        <h1 class="text-4xl font-serif font-bold text-[#4A2C2A]">👋 Welcome, {{ auth()->user()->name }}</h1>
        <p class="text-[#6B3D2E] mt-2">Track your progress and plan your learning journey</p>
    </div>

    <!-- 统计数据卡片 -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border-2 border-[#6B3D2E] rounded-xl p-5 shadow-md text-center">
            <div class="text-3xl font-bold text-[#4A2C2A]">{{ $stats['total_submissions'] }}</div>
            <div class="text-sm text-[#6B3D2E] mt-1">Submissions</div>
        </div>
        <div class="bg-white border-2 border-[#6B3D2E] rounded-xl p-5 shadow-md text-center">
            <div class="text-3xl font-bold text-[#4A2C2A]">{{ floor($stats['total_time'] / 60) }}m</div>
            <div class="text-sm text-[#6B3D2E] mt-1">Time Spent</div>
        </div>
        <div class="bg-white border-2 border-[#6B3D2E] rounded-xl p-5 shadow-md text-center">
            <div class="text-3xl font-bold text-[#4A2C2A]">{{ $stats['completed_plans'] }}</div>
            <div class="text-sm text-[#6B3D2E] mt-1">Plans Done</div>
        </div>
        <div class="bg-white border-2 border-[#6B3D2E] rounded-xl p-5 shadow-md text-center">
            <div class="text-3xl font-bold text-[#4A2C2A]">{{ $stats['current_streak'] }}</div>
            <div class="text-sm text-[#6B3D2E] mt-1">Day Streak</div>
        </div>
    </div>

    <!-- 主内容区：左列（日历 + 计划）+ 右列（收藏 + 记录） -->
    <div class="grid lg:grid-cols-3 gap-6">
        
        <!-- ===== 左列：日历 + 今日计划 + 待办计划 + 过期计划 ===== -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- 📅 学习日历 -->
            <div class="bg-white border-2 border-[#6B3D2E] rounded-xl p-6 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-[#4A2C2A]">📅 Study Calendar</h2>
                    <div class="text-sm text-[#6B3D2E]">
                        {{ \Carbon\Carbon::parse($currentMonth)->format('F Y') }}
                    </div>
                </div>
                
                <!-- 日历表头 -->
                <div class="grid grid-cols-7 gap-1 mb-2">
                    @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                        <div class="text-center text-xs font-bold text-[#6B3D2E] py-2">{{ $day }}</div>
                    @endforeach
                </div>
                
                <!-- 日历日期 -->
                @php
                    $firstDay = \Carbon\Carbon::parse($currentMonth)->startOfMonth();
                    $lastDay = \Carbon\Carbon::parse($currentMonth)->endOfMonth();
                    $startPadding = $firstDay->dayOfWeek;
                    $daysInMonth = $lastDay->day;
                @endphp
                
                <div class="grid grid-cols-7 gap-1">
                    {{-- 填充空白 --}}
                    @for($i = 0; $i < $startPadding; $i++)
                        <div class="aspect-square"></div>
                    @endfor
                    
                    {{-- 日期格子 --}}
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $dateStr = $currentMonth . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                            $plan = $plans->get($dateStr);
                            $isToday = $dateStr === $today;
                            $isExpired = in_array($dateStr, $expiredPlans ?? []);
                            
                            // 状态样式判断
                            if ($isExpired) {
                                $bgClass = 'bg-red-100';
                                $borderClass = 'border-red-400';
                                $textClass = 'text-red-800';
                                $dotClass = 'bg-red-500';
                            } elseif ($plan && $plan->status === 'completed') {
                                $bgClass = 'bg-green-100';
                                $borderClass = 'border-green-400';
                                $textClass = 'text-green-800';
                                $dotClass = 'bg-green-500';
                            } elseif ($plan && $plan->status === 'pending') {
                                $bgClass = 'bg-yellow-100';
                                $borderClass = 'border-yellow-400';
                                $textClass = 'text-yellow-800';
                                $dotClass = 'bg-yellow-500';
                            } else {
                                $bgClass = 'bg-gray-50';
                                $borderClass = 'border-gray-200';
                                $textClass = 'text-gray-600';
                                $dotClass = '';
                            }
                        @endphp
                        <button 
                            onclick="openPlanModal('{{ $dateStr }}')"
                            class="aspect-square border-2 rounded-lg p-1 text-xs hover:shadow-md transition relative group {{ $isToday ? 'ring-2 ring-[#6B3D2E]' : '' }} {{ $bgClass }} {{ $borderClass }} {{ $textClass }}"
                            title="{{ $isExpired ? 'Overdue! Click to reschedule' : ($plan ? 'Click to edit plan' : 'Click to add plan') }}"
                        >
                            <span class="font-medium">{{ $day }}</span>
                            @if($dotClass)
                                <div class="absolute bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full {{ $dotClass }}"></div>
                            @endif
                            @if($isExpired)
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border border-white">!</span>
                            @endif
                        </button>
                    @endfor
                </div>
                
                <!-- 图例 -->
                <div class="flex items-center gap-4 mt-4 text-xs text-[#6B3D2E] flex-wrap">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-100 border border-green-400"></span> Completed</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-yellow-100 border border-yellow-400"></span> Pending</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-100 border border-red-400"></span> Overdue</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-gray-50 border border-gray-200"></span> No Plan</span>
                </div>
            </div>

            <!-- 🎯 今日计划 -->
            <div class="bg-white border-2 border-[#6B3D2E] rounded-xl p-6 shadow-md">
                <h2 class="text-xl font-bold text-[#4A2C2A] mb-4">🎯 Today's Plan</h2>
                
                @if($todayPlan)
                    <div class="flex items-start justify-between p-4 bg-[#FAF0E6] rounded-lg border border-[#6B3D2E]/30">
                        <div>
                            <h3 class="font-bold text-[#4A2C2A]">{{ $todayPlan->article->title ?? 'Article #' . $todayPlan->article_id }}</h3>
                            <p class="text-sm text-[#6B3D2E] mt-1">
                                Status: <span class="font-medium {{ $todayPlan->status === 'completed' ? 'text-green-600' : 'text-yellow-600' }}">
                                    {{ ucfirst($todayPlan->status) }}
                                </span>
                            </p>
                        </div>
                        @if($todayPlan->status === 'pending')
                            <button 
                                onclick="updatePlanStatus({{ $todayPlan->id }}, 'completed')"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium"
                            >
                                ✓ Mark Done
                            </button>
                        @endif
                    </div>
                @else
                    <div class="text-center py-6 text-[#6B3D2E]">
                        <p class="mb-3">No plan for today.</p>
                        <button onclick="openPlanModal('{{ $today }}')" class="px-4 py-2 bg-[#6B3D2E] text-[#F5E6D3] rounded-lg hover:bg-[#8B4D3A] transition text-sm">
                            + Add Plan
                        </button>
                    </div>
                @endif
            </div>

            <!-- 📋 待完成计划 -->
            <div class="bg-white border-2 border-[#6B3D2E] rounded-xl p-6 shadow-md">
                <h2 class="text-xl font-bold text-[#4A2C2A] mb-4">📋 Upcoming Plans</h2>
                
                @if($pendingPlans->count() > 0)
                    <div class="space-y-3">
                        @foreach($pendingPlans as $plan)
                            <div class="flex items-center justify-between p-3 bg-[#FAF0E6] rounded-lg border border-[#6B3D2E]/20">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-[#4A2C2A] truncate">{{ $plan->article->title ?? 'Article #' . $plan->article_id }}</p>
                                    <p class="text-xs text-[#6B3D2E]">{{ \Carbon\Carbon::parse($plan->plan_date)->format('M d, Y') }}</p>
                                </div>
                                <div class="flex items-center gap-1">
                                    {{-- ✅ 标记完成 --}}
                                    <button 
                                        onclick="updatePlanStatus({{ $plan->id }}, 'completed')"
                                        class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition" 
                                        title="Mark completed"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                    
                                    {{-- ✏️ 编辑 --}}
                                    <button onclick="openPlanModal('{{ $plan->plan_date }}', {{ $plan->article_id }})" 
                                            class="p-2 text-[#6B3D2E] hover:bg-gray-100 rounded-lg transition" 
                                            title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    
                                    {{-- ❌ 删除计划 --}}
                                    <button 
                                        onclick="deletePlan({{ $plan->id }})"
                                        class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition" 
                                        title="Delete plan"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-[#6B3D2E] py-4">No upcoming plans. Start planning!</p>
                @endif
            </div>

            <!-- ⚠️ 过期计划 -->
            @if(isset($overduePlans) && $overduePlans->count() > 0)
            <div class="bg-white border-2 border-red-400 rounded-xl p-6 shadow-md">
                <h2 class="text-xl font-bold text-red-700 mb-4">⚠️ Overdue Plans</h2>
                <p class="text-sm text-red-600 mb-4">You have {{ $overduePlans->count() }} overdue plan(s). Don't give up!</p>
                
                <div class="space-y-3">
                    @foreach($overduePlans as $plan)
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-red-800 truncate">{{ $plan->article->title ?? 'Article #' . $plan->article_id }}</p>
                                <p class="text-xs text-red-600">
                                    Was due: <span class="font-medium">{{ \Carbon\Carbon::parse($plan->plan_date)->format('M d, Y') }}</span>
                                </p>
                            </div>
                            <div class="flex items-center gap-1">
                                {{-- ✅ 标记完成 --}}
                                <button 
                                    onclick="updatePlanStatus({{ $plan->id }}, 'completed')"
                                    class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition" 
                                    title="Mark as completed"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                                
                                {{-- ❌ 跳过 --}}
                                <button 
                                    onclick="updatePlanStatus({{ $plan->id }}, 'skipped')"
                                    class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition" 
                                    title="Skip this plan"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                
                                {{-- 🔄 重新安排 --}}
                                <button onclick="openPlanModal('{{ $today }}', {{ $plan->article_id }})" 
                                        class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition" 
                                        title="Reschedule to today">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                                
                                {{-- 🗑️ 删除计划 --}}
                                <button 
                                    onclick="deletePlan({{ $plan->id }})"
                                    class="p-2 text-red-700 hover:bg-red-200 rounded-lg transition" 
                                    title="Delete plan permanently"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- ===== 右列：收藏文章 + 做题记录 ===== -->
        <div class="space-y-6">
            
            <!-- ❤️ 收藏的文章 -->
            <div class="bg-white border-2 border-[#6B3D2E] rounded-xl p-6 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-[#4A2C2A]">❤️ Favorites</h2>
                    <a href="{{ route('articles.index', ['favorites' => 1]) }}" class="text-sm text-[#6B3D2E] hover:text-[#8B4D3A] transition">
                        View All →
                    </a>
                </div>
                
                @if($favoritedArticles->count() > 0)
                    <div class="space-y-3">
                        @foreach($favoritedArticles as $article)
                            <div class="flex items-start justify-between p-3 bg-[#FAF0E6] rounded-lg border border-[#6B3D2E]/20 group">
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('articles.show', $article->id) }}" class="font-medium text-[#4A2C2A] hover:text-[#6B3D2E] transition block truncate">
                                        {{ $article->title }}
                                    </a>
                                    <p class="text-xs text-[#6B3D2E] mt-1">
                                        ⭐ Level {{ $article->difficulty }} • {{ number_format($article->word_count) }} words
                                    </p>
                                </div>
                                <button 
                                    onclick="toggleFavorite({{ $article->id }}, this)"
                                    class="p-2 text-red-500 hover:bg-red-100 rounded-lg transition opacity-0 group-hover:opacity-100"
                                    title="Remove from favorites"
                                >
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-[#6B3D2E] mb-3">No favorites yet</p>
                        <a href="{{ route('articles.index') }}" class="text-sm text-[#6B3D2E] hover:text-[#8B4D3A] transition">Browse articles →</a>
                    </div>
                @endif
            </div>

            <!-- 📊 最近做题记录 -->
            <div class="bg-white border-2 border-[#6B3D2E] rounded-xl p-6 shadow-md">
                <h2 class="text-xl font-bold text-[#4A2C2A] mb-4">📊 Recent Activity</h2>
                
                @if($recentSubmissions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-[#6B3D2E] border-b border-[#6B3D2E]/20">
                                    <th class="pb-2">Article</th>
                                    <th class="pb-2">Type</th>
                                    <th class="pb-2">Score</th>
                                    <th class="pb-2">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSubmissions as $sub)
                                    <tr class="border-b border-[#6B3D2E]/10 last:border-0">
                                        <td class="py-3 pr-2">
                                            <a href="{{ route('articles.show', $sub->exercise->article->id ?? $sub->article_id) }}" 
                                               class="text-[#4A2C2A] hover:text-[#6B3D2E] transition block truncate max-w-[150px]">
                                                {{ $sub->exercise->article->title ?? 'Article #' . ($sub->article_id ?? '?') }}
                                            </a>
                                        </td>
                                        <td class="py-3 text-[#6B3D2E]">{{ ucfirst($sub->exercise->type ?? 'quiz') }}</td>
                                        <td class="py-3 font-medium {{ ($sub->score ?? 0) >= 80 ? 'text-green-600' : (($sub->score ?? 0) >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $sub->score ? number_format($sub->score, 1) : '-' }}
                                        </td>
                                        <td class="py-3 text-[#6B3D2E]">
                                            {{ ($sub->time_spent ?? 0) >= 60 ? floor(($sub->time_spent ?? 0) / 60) . 'm' : ($sub->time_spent ?? 0) . 's' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6 text-[#6B3D2E]">
                        <p>No recent activity.</p>
                        <a href="{{ route('articles.index') }}" class="text-sm hover:text-[#8B4D3A] transition mt-2 inline-block">Start learning →</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 添加/编辑计划模态框 -->
<div id="planModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4 shadow-2xl border-2 border-[#6B3D2E]">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-[#4A2C2A]">📅 Set Study Plan</h3>
            <button onclick="closePlanModal()" class="text-[#6B3D2E] hover:text-red-500 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form id="planForm" class="space-y-4">
            @csrf
            <input type="hidden" id="planDate" name="plan_date">
            
            <div>
                <label class="block text-sm font-medium text-[#4A2C2A] mb-1">Select Article</label>
                <select name="article_id" id="articleSelect" class="w-full px-4 py-2 border-2 border-[#6B3D2E] rounded-lg bg-[#FAF0E6] text-[#4A2C2A] focus:outline-none focus:border-[#8B4D3A]" required>
                    <option value="">Choose an article...</option>
                    @foreach($articles as $article)
                        <option value="{{ $article->id }}">{{ $article->title }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closePlanModal()" class="px-4 py-2 text-[#6B3D2E] hover:bg-gray-100 rounded-lg transition">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-[#6B3D2E] text-[#F5E6D3] rounded-lg hover:bg-[#8B4D3A] transition font-medium">Save Plan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ✅ 更新计划状态 - AJAX 方式（无弹窗）
function updatePlanStatus(planId, status) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    
    if (!csrfToken) {
        alert('Error: CSRF token not found');
        return;
    }
    
    fetch(`/plans/${planId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-HTTP-Method-Override': 'PATCH',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(res => res.ok ? res.json() : Promise.reject())
    .then(() => {
        // 成功后静默刷新，不显示提示
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update plan');
    });
}

// ❌ 删除计划功能 - 无弹窗确认
function deletePlan(planId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    
    if (!csrfToken) {
        alert('Error: CSRF token not found');
        return;
    }
    
    fetch(`/plans/${planId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (res.ok) {
            return res.json();
        } else if (res.status === 403) {
            throw new Error('Unauthorized');
        }
        throw new Error('Failed to delete plan');
    })
    .then(() => {
        // 成功后静默刷新
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to delete plan');
    });
}

// 收藏功能 - 移除收藏
function toggleFavorite(articleId, button) {
    const meta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = meta?.getAttribute('content');
    
    if (!csrfToken) {
        alert('Error: CSRF token not found. Please refresh.');
        return;
    }
    
    if (!confirm('Remove this article from favorites?')) return;
    
    const originalHTML = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
    
    fetch(`/articles/${articleId}/toggle-favorite`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(res => res.ok ? res.json() : Promise.reject())
    .then(() => {
        button.closest('.group')?.remove();
        const favContainer = document.querySelector('.space-y-3');
        if (favContainer && favContainer.children.length === 0) {
            location.reload();
        }
    })
    .catch(() => {
        alert('Failed to remove favorite');
        button.disabled = false;
        button.innerHTML = originalHTML;
    });
}

// 日历模态框功能
function openPlanModal(dateStr, articleId = null) {
    document.getElementById('planDate').value = dateStr;
    document.getElementById('planModal').classList.remove('hidden');
    
    if (articleId) {
        document.getElementById('articleSelect').value = articleId;
    } else {
        document.getElementById('articleSelect').value = '';
    }
}

function closePlanModal() {
    document.getElementById('planModal').classList.add('hidden');
    document.getElementById('planForm').reset();
}

// 提交计划表单 - 无 alert 提示
document.getElementById('planForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    
    try {
        const response = await fetch('/plans', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        if (!response.ok) throw new Error('Failed to save plan');
        
        // 直接关闭并刷新，不显示 alert
        closePlanModal();
        location.reload();
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to save plan. Please try again.');
    }
});

// 点击模态框外部关闭
document.getElementById('planModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'planModal') closePlanModal();
});

// ESC 键关闭模态框
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closePlanModal();
});
</script>
@endpush