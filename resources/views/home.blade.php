@extends('layouts.app')

@section('title', 'Dashboard - Academic English Learning')

@section('content')
<div class="min-h-screen bg-[#F5EFE6]">
    <div class="max-w-7xl mx-auto px-6 py-8">
        
        <!-- 统计卡片 - 复古风格 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- 总提交 -->
            <div class="bg-white border-2 border-[#8B4513] rounded-lg p-6 shadow-md">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-[#8B4513]/10 rounded">
                        <svg class="w-6 h-6 text-[#8B4513]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-[#5C3317]">{{ $stats['total_submissions'] }}</span>
                </div>
                <p class="text-gray-700 font-medium text-sm">Total Submissions</p>
            </div>
            
            <!-- 学习时长 -->
            <div class="bg-white border-2 border-[#8B4513] rounded-lg p-6 shadow-md">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-[#8B4513]/10 rounded">
                        <svg class="w-6 h-6 text-[#8B4513]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-[#5C3317]">{{ floor($stats['total_time'] / 60) }}<span class="text-lg text-gray-600">m</span></span>
                </div>
                <p class="text-gray-700 font-medium text-sm">Time Spent</p>
            </div>
            
            <!-- 完成计划 -->
            <div class="bg-white border-2 border-[#8B4513] rounded-lg p-6 shadow-md">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-[#8B4513]/10 rounded">
                        <svg class="w-6 h-6 text-[#8B4513]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-[#5C3317]">{{ $stats['completed_plans'] }}</span>
                </div>
                <p class="text-gray-700 font-medium text-sm">Plans Completed</p>
            </div>
            
            <!-- 连续天数 -->
            <div class="bg-white border-2 border-[#8B4513] rounded-lg p-6 shadow-md">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-[#8B4513]/10 rounded">
                        <svg class="w-6 h-6 text-[#8B4513]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-[#5C3317]">{{ $stats['current_streak'] }}</span>
                </div>
                <p class="text-gray-700 font-medium text-sm">Day Streak</p>
            </div>
        </div>
        
        <!-- 主要内容区 -->
        <div class="grid lg:grid-cols-3 gap-8 mb-8">
            
            <!-- 左侧：日历 -->
            <div class="lg:col-span-2 bg-white border-2 border-[#8B4513] rounded-lg shadow-md">
                <div class="p-4 border-b-2 border-[#8B4513]/20 bg-[#F5EFE6]">
                    <h3 class="text-xl font-serif font-bold text-[#5C3317]">Study Calendar</h3>
                </div>
                <div class="p-6">
                    <!-- 星期表头 -->
                    <div class="grid grid-cols-7 gap-2 mb-2">
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                            <div class="text-center text-xs font-bold text-[#8B4513] uppercase py-2">
                                {{ $day }}
                            </div>
                        @endforeach
                    </div>
                    <!-- 日历格子 -->
                    <div id="calendarDays" class="grid grid-cols-7 gap-2">
                        <!-- JS 动态生成 -->
                    </div>
                    <!-- 图例 -->
                    <div class="mt-4 flex items-center justify-center gap-6 text-xs">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-[#228B22] border border-[#8B4513]"></div>
                            <span class="text-gray-700">Completed</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-[#DAA520] border border-[#8B4513]"></div>
                            <span class="text-gray-700">Pending</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-gray-100 border border-gray-300"></div>
                            <span class="text-gray-700">No Plan</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 右侧：计划面板 -->
            <div class="space-y-6">
                
                <!-- 今天计划 -->
                <div class="bg-white border-2 border-[#8B4513] rounded-lg shadow-md">
                    <div class="p-4 border-b-2 border-[#8B4513]/20 bg-[#F5EFE6]">
                        <h3 class="text-lg font-serif font-bold text-[#5C3317]">Today's Plan</h3>
                    </div>
                    <div class="p-4">
                        @if($todayPlan)
                            <div class="bg-[#F5EFE6] rounded p-3 border border-[#8B4513]/30">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <p class="font-bold text-[#5C3317] text-sm">{{ $todayPlan->article->title ?? 'Article #' . $todayPlan->article_id }}</p>
                                        <p class="text-xs text-gray-600 mt-1">
                                            Status: 
                                            <span class="px-2 py-0.5 rounded text-xs font-bold 
                                                {{ $todayPlan->status === 'completed' ? 'bg-[#228B22] text-white' : 
                                                   ($todayPlan->status === 'skipped' ? 'bg-gray-400 text-white' : 'bg-[#DAA520] text-white') }}">
                                                {{ ucfirst($todayPlan->status) }}
                                            </span>
                                        </p>
                                    </div>
                                    @if($todayPlan->status === 'pending')
                                        <button onclick="updatePlanStatus({{ $todayPlan->id }}, 'completed')" 
                                                class="px-3 py-1 bg-[#228B22] text-white text-xs rounded hover:bg-[#2E8B57] transition">
                                            ✓
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm text-center py-4">No plan for today.</p>
                        @endif
                    </div>
                </div>
                
                <!-- 待完成计划 -->
                <div class="bg-white border-2 border-[#8B4513] rounded-lg shadow-md">
                    <div class="p-4 border-b-2 border-[#8B4513]/20 bg-[#F5EFE6]">
                        <h3 class="text-lg font-serif font-bold text-[#5C3317]">Upcoming Plans</h3>
                    </div>
                    <div class="p-4">
                        @if($pendingPlans->count() > 0)
                            <div class="space-y-2">
                                @foreach($pendingPlans as $plan)
                                    <div class="bg-[#F5EFE6] rounded p-2 border border-[#8B4513]/30">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <p class="font-semibold text-[#5C3317] text-xs">{{ $plan->article->title ?? 'Article #' . $plan->article_id }}</p>
                                                <p class="text-xs text-gray-600 mt-1">{{ \Carbon\Carbon::parse($plan->plan_date)->format('M d') }}</p>
                                            </div>
                                            <button onclick="updatePlanStatus({{ $plan->id }}, 'completed')" 
                                                    class="px-2 py-1 bg-[#228B22] text-white text-xs rounded hover:bg-[#2E8B57] transition">
                                                ✓
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm text-center py-4">No upcoming plans.</p>
                        @endif
                    </div>
                </div>
                
                <!-- 快速添加计划 -->
                <div class="bg-white border-2 border-[#8B4513] rounded-lg shadow-md">
                    <div class="p-4 border-b-2 border-[#8B4513]/20 bg-[#F5EFE6]">
                        <h3 class="text-lg font-serif font-bold text-[#5C3317]">Quick Plan</h3>
                    </div>
                    <div class="p-4">
                        <form id="quickPlanForm" class="space-y-3">
                            @csrf
                            <select name="article_id" class="w-full px-3 py-2 border-2 border-[#8B4513] rounded bg-white text-[#5C3317] focus:outline-none focus:border-[#DAA520] text-sm">
                                <option value="">Select an article...</option>
                                @foreach($articles as $article)
                                    <option value="{{ $article->id }}">{{ $article->title }}</option>
                                @endforeach
                            </select>
                            <input type="date" name="plan_date" id="quickPlanDate" 
                                   class="w-full px-3 py-2 border-2 border-[#8B4513] rounded bg-white text-[#5C3317] focus:outline-none focus:border-[#DAA520] text-sm"
                                   value="{{ now()->toDateString() }}">
                            <button type="submit" class="w-full py-2 bg-[#8B4513] text-[#F5EFE6] rounded font-medium hover:bg-[#A0522D] transition text-sm">
                                Add to Calendar
                            </button>
                        </form>
                    </div>
                </div>
                
            </div>
        </div>
        
        <!-- 最近活动 -->
        <div class="bg-white border-2 border-[#8B4513] rounded-lg shadow-md">
            <div class="p-4 border-b-2 border-[#8B4513]/20 bg-[#F5EFE6]">
                <h3 class="text-xl font-serif font-bold text-[#5C3317]">Recent Activity</h3>
            </div>
            <div class="p-4">
                @if($recentSubmissions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b-2 border-[#8B4513]/20">
                                    <th class="text-left py-2 text-[#8B4513] font-bold">Article</th>
                                    <th class="text-left py-2 text-[#8B4513] font-bold">Type</th>
                                    <th class="text-left py-2 text-[#8B4513] font-bold">Score</th>
                                    <th class="text-left py-2 text-[#8B4513] font-bold">Time</th>
                                    <th class="text-left py-2 text-[#8B4513] font-bold">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSubmissions as $sub)
                                    <tr class="border-b border-[#8B4513]/10 hover:bg-[#F5EFE6]">
                                        <td class="py-3">
                                            <span class="font-bold text-[#5C3317]">
                                                {{ $sub->exercise->article->title ?? 'Article #' . $sub->article_id }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <span class="px-2 py-1 rounded text-xs font-bold border 
                                                {{ $sub->exercise->type === 'reading' ? 'bg-blue-100 text-blue-800 border-blue-300' : 
                                                   ($sub->exercise->type === 'listening' ? 'bg-purple-100 text-purple-800 border-purple-300' : 
                                                   ($sub->exercise->type === 'speaking' ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-green-100 text-green-800 border-green-300')) }}">
                                                {{ ucfirst($sub->exercise->type) }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-[#5C3317] font-semibold">
                                            {{ $sub->score ? number_format($sub->score, 1) : '-' }}
                                        </td>
                                        <td class="py-3 text-gray-600">
                                            {{ $sub->time_spent >= 60 ? floor($sub->time_spent / 60) . 'm' : $sub->time_spent . 's' }}
                                        </td>
                                        <td class="py-3 text-gray-600">
                                            {{ \Carbon\Carbon::parse($sub->created_at)->format('M d, H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-sm text-center py-8">No recent activity. Start learning to see your progress!</p>
                @endif
            </div>
        </div>
        
    </div>
    
    <!-- 页脚 -->
    <footer class="mt-8 border-t-2 border-[#8B4513] bg-[#F5EFE6]">
        <div class="max-w-7xl mx-auto px-6 py-4 text-center text-sm text-[#5C3317]">
            <p>© 2026 Academic English Oral Training Studio. All rights reserved.</p>
        </div>
    </footer>
    
</div>

<!-- 模态框 -->
<div id="planModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-2xl p-6 w-full max-w-md mx-4 border-2 border-[#8B4513]">
        <h3 class="text-xl font-serif font-bold text-[#5C3317] mb-4">Set Study Plan</h3>
        <form id="planForm" class="space-y-4">
            @csrf
            <input type="hidden" name="plan_date" id="modalPlanDate">
            
            <div>
                <label class="block text-sm font-bold text-[#5C3317] mb-1">Select Article</label>
                <select name="article_id" class="w-full px-3 py-2 border-2 border-[#8B4513] rounded bg-white text-[#5C3317] focus:outline-none focus:border-[#DAA520]">
                    <option value="">Choose an article...</option>
                    @foreach($articles as $article)
                        <option value="{{ $article->id }}">{{ $article->title }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()" 
                        class="flex-1 py-2 border-2 border-[#8B4513] rounded text-[#5C3317] font-medium hover:bg-[#F5EFE6] transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 py-2 bg-[#8B4513] text-[#F5EFE6] rounded font-medium hover:bg-[#A0522D] transition">
                    Save Plan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const plans = @json($plans);
    const today = @json(now()->toDateString());
    const currentMonth = @json(now()->format('Y-m'));
    
    renderCalendar(currentMonth);
    
    document.getElementById('quickPlanForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        try {
            const response = await fetch("{{ route('plans.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                renderCalendar(currentMonth);
                location.reload();
            }
        } catch (error) {
            alert('Failed to save plan.');
        }
    });
    
    document.getElementById('planForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        try {
            const response = await fetch("{{ route('plans.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                renderCalendar(currentMonth);
                closeModal();
                location.reload();
            }
        } catch (error) {
            alert('Failed to save plan.');
        }
    });
});

function renderCalendar(ym) {
    const [year, month] = ym.split('-').map(Number);
    const firstDay = new Date(year, month - 1, 1);
    const lastDay = new Date(year, month, 0);
    const startDay = firstDay.getDay();
    const totalDays = lastDay.getDate();
    
    const container = document.getElementById('calendarDays');
    container.innerHTML = '';
    
    for (let i = 0; i < startDay; i++) {
        const dayEl = document.createElement('div');
        dayEl.className = 'aspect-square';
        container.appendChild(dayEl);
    }
    
    for (let day = 1; day <= totalDays; day++) {
        const dateStr = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayEl = document.createElement('div');
        dayEl.className = 'aspect-square rounded border-2 border-[#8B4513]/30 flex items-center justify-center cursor-pointer hover:bg-[#F5EFE6] transition text-sm font-semibold text-[#5C3317]';
        dayEl.textContent = day;
        
        const todayStr = @json(now()->toDateString());
        if (dateStr === todayStr) {
            dayEl.classList.add('bg-[#F5EFE6]', 'border-[#8B4513]', 'font-bold');
        }
        
        const plan = plans[dateStr];
        if (plan) {
            if (plan.status === 'completed') {
                dayEl.className = 'aspect-square rounded border-2 border-[#8B4513] flex items-center justify-center bg-[#228B22] text-white font-bold';
                dayEl.innerHTML = `${day}<span class="text-xs ml-0.5">✓</span>`;
            } else if (plan.status === 'pending') {
                dayEl.className = 'aspect-square rounded border-2 border-[#8B4513] flex items-center justify-center bg-[#DAA520] text-white font-bold';
                dayEl.innerHTML = `${day}<span class="text-xs ml-0.5">●</span>`;
            }
        }
        
        dayEl.addEventListener('click', () => openModal(dateStr));
        container.appendChild(dayEl);
    }
}

function openModal(dateStr) {
    document.getElementById('modalPlanDate').value = dateStr;
    document.getElementById('planModal').classList.remove('hidden');
    document.getElementById('planModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('planModal').classList.add('hidden');
    document.getElementById('planModal').classList.remove('flex');
}

async function updatePlanStatus(planId, status) {
    try {
        const response = await fetch(`/plans/${planId}`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status })
        });
        const result = await response.json();
        
        if (result.success) {
            location.reload();
        }
    } catch (error) {
        alert('Failed to update plan.');
    }
}

document.getElementById('planModal').addEventListener('click', (e) => {
    if (e.target.id === 'planModal') {
        closeModal();
    }
});
</script>
@endpush