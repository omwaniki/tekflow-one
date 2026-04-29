<x-app-layout>

<div class="max-w-7xl mx-auto px-8 pt-10 pb-12">
    <div class="mb-6 text-sm text-gray-500 flex items-center gap-2">
        <a href="{{ route('audits.index') }}">Audits</a>
        <span>/</span>
        <span>{{ $audit->name }}</span>
        <span>/</span>
        <span class="text-gray-800 font-medium">Dashboard</span>
    </div>

    @php
        $isAdmin = auth()->user()->can('edit audits');
        $progress = $total > 0 ? round(($verified / $total) * 100) : 0;
    @endphp

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-3xl font-semibold text-gray-800">
                {{ $audit->name }} — Dashboard
            </h1>

            <p class="text-gray-500 mt-1">
                {{ $isAdmin ? 'Audit results and discrepancies' : 'Your audit progress and tasks' }}
            </p>
        </div>

        <!-- Campus Filter (ADMIN ONLY) -->
        @if($isAdmin)
        <form method="GET" class="flex items-center gap-2 text-sm">

            <span class="text-gray-400 text-xs uppercase tracking-wide">
                Filter
            </span>

            <select name="campus_id"
                onchange="this.form.submit()"
                class="border border-gray-300 rounded-lg px-3 py-2 bg-white shadow-sm focus:ring-2 focus:ring-blue-500">

                <option value="">All Campuses</option>

                @foreach($campuses as $campus)
                    <option value="{{ $campus->id }}"
                        {{ $campusId == $campus->id ? 'selected' : '' }}>
                        {{ $campus->name }}
                    </option>
                @endforeach

            </select>

        </form>
        @endif

    </div>

    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="flex justify-between text-sm text-gray-500 mb-2">
            <span>Progress</span>
            <span>{{ $verified }} / {{ $total }} ({{ $progress }}%)</span>
        </div>

        <div class="w-full bg-gray-200 rounded-full h-2">
            @php
                $progressColor = match(true) {
                    $progress === 100 => 'bg-green-600',
                    $progress > 0 => 'bg-amber-500',
                    default => 'bg-red-500',
                };
            @endphp

            <div class="{{ $progressColor }} h-2 rounded-full transition-all duration-700"
                style="width: {{ $progress }}%">
            </div>
        </div>
    </div>

    <!-- ========================= -->
    <!-- 👨‍💼 ADMIN VIEW -->
    <!-- ========================= -->
    @if($isAdmin)

        <!-- Summary Cards -->
        <div class="grid grid-cols-4 gap-4 mb-8">

            <div class="bg-white p-4 rounded-xl shadow">
                <p class="text-gray-500 text-sm">Total Assets</p>
                <p class="text-2xl font-semibold">{{ $total }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow">
                <p class="text-gray-500 text-sm">Verified</p>
                <p class="text-2xl font-semibold text-blue-600">{{ $verified }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow">
                <p class="text-gray-500 text-sm">Missing</p>
                <p class="text-2xl font-semibold text-red-600">{{ $missing }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow">
                <p class="text-gray-500 text-sm">Issues</p>
                <p class="text-2xl font-semibold text-yellow-600">{{ $discrepancies->count() }}</p>
            </div>

        </div>

        <!-- 🔥 NEW: Campus Progress -->
        <div class="mb-10">
            <div class="flex gap-2 mb-4">

                <a href="{{ route('audits.dashboard', $audit->id) }}"
                class="px-3 py-1 rounded-full text-sm border
                {{ !$statusFilter ? 'bg-gray-800 text-white' : 'bg-white text-gray-700' }}">
                    All
                </a>

                <a href="{{ route('audits.dashboard', [$audit->id, 'status' => 'completed']) }}"
                class="px-3 py-1 rounded-full text-sm border
                {{ $statusFilter === 'completed' ? 'bg-green-600 text-white' : 'bg-white text-gray-700' }}">
                    🟢 Completed
                </a>

                <a href="{{ route('audits.dashboard', [$audit->id, 'status' => 'in_progress']) }}"
                class="px-3 py-1 rounded-full text-sm border
                {{ $statusFilter === 'in_progress' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-700' }}">
                    🟡 In Progress
                </a>

                <a href="{{ route('audits.dashboard', [$audit->id, 'status' => 'not_started']) }}"
                class="px-3 py-1 rounded-full text-sm border
                {{ $statusFilter === 'not_started' ? 'bg-red-600 text-white' : 'bg-white text-gray-700' }}">
                    🔴 Not Started
                </a>

            </div>

            <h2 class="text-xl font-semibold mb-4 text-gray-800">
                Campus Progress
            </h2>

            <div class="bg-white rounded-xl shadow p-4 space-y-4">

                @foreach($campusProgress as $campus)

                    @php
                        $percent = $campus['percent'];
                    @endphp

                    <a href="{{ route('audits.campus', [$audit->id, $campus['id']]) }}"
                    class="block rounded-lg p-3 transition-all duration-200 cursor-pointer
                        hover:bg-gray-50 hover:shadow-md hover:-translate-y-0.5">

                        <div>
                            <div class="flex justify-between items-center text-sm mb-1">

                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-700">
                                    {{ $campus['name'] }}
                                </span>
                            </div>

                            @php
                                $statusStyles = match($campus['status']) {
                                    'completed' => 'bg-green-100 text-green-700',
                                    'in_progress' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-red-100 text-red-700',
                                };

                                $statusLabel = ucfirst(str_replace('_', ' ', $campus['status']));
                            @endphp

                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusStyles }} shadow-sm">
                                {{ $statusLabel }}
                                • {{ $campus['verified'] }}/{{ $campus['total'] }}
                                • {{ $percent }}%
                            </span>

                        </div>

                            <div class="text-xs text-gray-500 mb-1">
                                {{ $campus['percent'] }}%
                            </div>

                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="
                                    h-2 rounded-full transition-all duration-700 ease-out
                                    @if($campus['status'] === 'completed') bg-green-500
                                    @elseif($campus['status'] === 'in_progress') bg-amber-500
                                    @else bg-red-500
                                    @endif
                                "
                                style="width: 0%"
                                data-width="{{ $campus['percent'] == 0 ? 2 : $campus['percent'] }}">
                                </div>
                            </div>
                        </div>

                    </a>

                @endforeach

            </div>
        </div>

        <!-- Missing Devices -->
        <div class="mb-10">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Missing Devices</h2>

            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-4">Device</th>
                            <th class="p-4">Serial</th>
                            <th class="p-4">Campus</th>
                            <th class="p-4">Expected Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records->where('found', 0) as $r)
                            <tr class="border-t">
                                <td class="p-4">
                                    {{ $r->asset->brand ?? $r->asset->device_type ?? 'Unknown' }}
                                </td>
                                <td class="p-4">{{ $r->asset->serial_number }}</td>
                                <td class="p-4">{{ $r->campus->name ?? '-' }}</td>
                                <td class="p-4">{{ $r->expected_status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-gray-500">
                                    No missing devices 🎉
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!--Additional actions-->
        <div class="flex justify-between items-center mb-3">

            <h2 class="text-xl font-semibold text-gray-800">
                Status Changes
            </h2>

            @if($discrepancies->count())
            <button onclick="approveAll()"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                Approve All
            </button>
            @endif

        </div>

        <!-- Status Discrepancies -->
         
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-4">Device</th>
                        <th class="p-4">Serial</th>
                        <th class="p-4">Current</th>
                        <th class="p-4">Suggested</th>
                        <th class="p-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($discrepancies as $item)
                        <tr class="border-t discrepancy-row" data-id="{{ $item['record']->id }}">

                            <td class="p-4">
                                {{ $item['device'] }}
                            </td>

                            <td class="p-4">
                                {{ $item['serial'] }}
                            </td>

                            <td class="p-4 text-gray-600">
                                {{ $item['current'] }}
                            </td>

                            <td class="p-4">
                                <span class="
                                    px-2 py-1 rounded-full text-xs font-medium
                                    {{ $item['suggested'] === 'active'
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-red-100 text-red-700' }}
                                ">
                                    {{ $item['suggested'] }}
                                </span>
                            </td>

                            <td class="p-4 text-right">
                                <form method="POST" action="{{ route('audit.records.approve', $item['record']->id) }}">
                                    @csrf
                                    <button type="submit"
                                        class="bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700">
                                        Approve
                                    </button>
                                </form>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500">
                                No discrepancies 👍
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        

    <!-- ========================= -->
    <!-- 🧑‍🔧 AGENT VIEW -->
    <!-- ========================= -->
    @else

        <!-- My Stats -->
        <div class="grid grid-cols-3 gap-4 mb-8">

            <div class="bg-white p-4 rounded-xl shadow">
                <p class="text-gray-500 text-sm">Verified</p>
                <p class="text-2xl font-semibold text-blue-600">{{ $verified }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow">
                <p class="text-gray-500 text-sm">Missing</p>
                <p class="text-2xl font-semibold text-red-600">{{ $missing }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow">
                <p class="text-gray-500 text-sm">Remaining</p>
                <p class="text-2xl font-semibold text-gray-800">
                    {{ $total - $verified }}
                </p>
            </div>

        </div>

        <!-- Assigned Campuses -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Your Campuses</h2>

            <div class="bg-white rounded-xl shadow overflow-hidden">

                @forelse($campusStats as $campus)

                    <a href="{{ route('audits.verify', [$audit->id, 'campus_id' => $campus['id']]) }}"
                        class="flex items-center justify-between px-5 py-4 border-b last:border-none
                                hover:bg-gray-50 transition group">

                            <div class="flex flex-col gap-1 w-full">

                                <!-- NAME -->
                                <span class="font-medium text-gray-800">
                                    {{ $campus['name'] }}
                                </span>

                                <!-- SIMPLE STATUS + PERCENT -->
                                @php
                                    $statusStyles = match($campus['status']) {
                                        'completed' => 'text-green-600',
                                        'in_progress' => 'text-amber-600',
                                        default => 'text-red-600',
                                    };

                                    $statusLabel = ucfirst(str_replace('_', ' ', $campus['status']));
                                @endphp

                                <span class="text-xs font-medium {{ $statusStyles }}">
                                    {{ $campus['percent'] }}% • {{ $statusLabel }}
                                </span>

                            </div>

                            <!-- CTA -->
                            @php
                                $ctaText = match($campus['status']) {
                                    'completed' => 'View',
                                    'in_progress' => 'Continue',
                                    default => 'Start',
                                };
                            @endphp

                            <div class="ml-4 text-blue-600 text-sm font-semibold flex items-center gap-1 shrink-0">
                                {{ $ctaText }}
                                <span class="transition-transform group-hover:translate-x-1">→</span>
                            </div>

                        </a>

                    </a>

                @empty

                    <div class="p-4 text-gray-500">
                        No campuses assigned
                    </div>

                @endforelse

            </div>

        </div>

    @endif

    <script>
    window.addEventListener('load', () => {
        document.querySelectorAll('[data-width]').forEach((el, index) => {
            setTimeout(() => {
                el.style.width = el.dataset.width + '%';
            }, index * 100); // stagger animation (🔥 nice effect)
        });
    });
    </script>

</div>

</x-app-layout>