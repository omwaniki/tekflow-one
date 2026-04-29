<x-app-layout>

<div class="space-y-6">

    {{-- PAGE TITLE --}}
    <div>
        <h2 class="text-2xl font-bold text-gray-800">
            Dashboard Overview
        </h2>
        <p class="text-gray-500">
            System-wide asset summary
        </p>
    </div>

    {{-- ================= QUICK ACTIONS ================= --}}
    <div class="bg-white p-6 rounded-xl shadow-sm">

        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                Quick Actions
            </h3>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            <a href="{{ route('assets.create') }}"
               class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg text-center transition">
                <div class="text-lg font-semibold text-gray-800">+ Asset</div>
                <div class="text-xs text-gray-500">Add new asset</div>
            </a>

            <a href="{{ route('assignments.create') }}"
               class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg text-center transition">
                <div class="text-lg font-semibold text-gray-800">+ Assign</div>
                <div class="text-xs text-gray-500">Assign asset</div>
            </a>

            <a href="{{ route('audits.create') }}"
               class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg text-center transition">
                <div class="text-lg font-semibold text-gray-800">+ Audit</div>
                <div class="text-xs text-gray-500">Start audit</div>
            </a>

            <a href="{{ route('assets.index') }}"
               class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg text-center transition">
                <div class="text-lg font-semibold text-gray-800">View Assets</div>
                <div class="text-xs text-gray-500">Go to register</div>
            </a>

        </div>

    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white p-6 rounded-xl shadow-sm">
            <p class="text-sm text-gray-500">Total Assets</p>
            <h3 class="text-3xl font-bold text-gray-800">
                {{ $totalAssets }}
            </h3>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm">
            <p class="text-sm text-gray-500">Campuses</p>
            <h3 class="text-3xl font-bold text-gray-800">
                {{ $totalCampuses }}
            </h3>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm">
            <p class="text-sm text-gray-500">Regions</p>
            <h3 class="text-3xl font-bold text-gray-800">
                {{ $totalRegions }}
            </h3>
        </div>

    </div>

    {{-- ================= ACTIVITY + CHARTS ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- RECENT ACTIVITY --}}
        <div class="bg-white p-6 rounded-xl shadow-sm">

            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                Recent Activity
            </h3>

            <div class="space-y-3 text-sm text-gray-600">

                {{-- 🔥 You can later replace with dynamic data --}}
                <div class="flex justify-between">
                    <span>Asset assigned</span>
                    <span class="text-gray-400">5 min ago</span>
                </div>

                <div class="flex justify-between">
                    <span>Movement recorded</span>
                    <span class="text-gray-400">1 hr ago</span>
                </div>

                <div class="flex justify-between">
                    <span>Audit completed</span>
                    <span class="text-gray-400">Today</span>
                </div>

                <div class="flex justify-between">
                    <span>New asset added</span>
                    <span class="text-gray-400">Yesterday</span>
                </div>

            </div>

        </div>

        {{-- ASSET STATUS CHART --}}
        <div class="bg-white p-6 rounded-xl shadow-sm col-span-1 lg:col-span-1">

            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    Asset Status Overview
                </h3>
                <p class="text-sm text-gray-500">
                    Distribution of assets by condition
                </p>
            </div>

            <div class="h-64 flex justify-center items-center">
                <canvas id="statusChart"></canvas>
            </div>

        </div>

        {{-- CAMPUS CHART --}}
        <div class="bg-white p-6 rounded-xl shadow-sm">

            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    Assets by Campus
                </h3>
                <p class="text-sm text-gray-500">
                    Comparison across campuses
                </p>
            </div>

            <div class="h-64">
                <canvas id="campusChart"></canvas>
            </div>

        </div>

    </div>

    {{-- TABLE --}}
    <div class="bg-white p-6 rounded-xl shadow-sm">

        <h3 class="text-lg font-semibold mb-4 text-gray-800">
            Assets by Campus
        </h3>

        <table class="w-full text-left">

            <thead>
                <tr class="text-gray-500 text-sm border-b">
                    <th class="pb-2">Campus</th>
                    <th class="pb-2">Assets</th>
                </tr>
            </thead>

            <tbody>
                @foreach($assetsByCampus as $campus)
                <tr class="border-b">
                    <td class="py-2">{{ $campus->name }}</td>
                    <td class="py-2 font-semibold">
                        {{ $campus->assets_count }}
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>

</div>

{{-- ================= CHART JS ================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const statusData = {
    labels: ['Active', 'Faulty', 'Retired'],
    datasets: [{
        data: [
            {{ $statuses['active'] ?? 0 }},
            {{ $statuses['faulty'] ?? 0 }},
            {{ $statuses['retired'] ?? 0 }}
        ],
        backgroundColor: ['#10B981','#F59E0B','#EF4444'],
        borderWidth: 0
    }]
};

const ctx = document.getElementById('statusChart').getContext('2d');

let centerText = '';

const centerTextPlugin = {
    id: 'centerText',
    beforeDraw(chart) {
        if (centerText) {
            const { ctx, chartArea: { width, height } } = chart;
            ctx.save();
            ctx.font = 'bold 18px sans-serif';
            ctx.fillStyle = '#111827';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(centerText, width / 2, height / 2);
        }
    }
};

new Chart(ctx, {
    type: 'doughnut',
    data: statusData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '75%',
        onHover: (event, elements, chart) => {
            if (elements.length > 0) {
                const index = elements[0].index;
                const value = chart.data.datasets[0].data[index];
                centerText = `${value}`;
            } else {
                centerText = '';
            }
            chart.draw();
        },
        plugins: {
            legend: { position: 'bottom' }
        }
    },
    plugins: [centerTextPlugin]
});
</script>

<script>
const campusLabels = [
@foreach($assetsByCampus as $campus)
"{{ $campus->name }}",
@endforeach
];

const campusData = [
@foreach($assetsByCampus as $campus)
{{ $campus->assets_count }},
@endforeach
];
</script>

<script>
const campusCtx = document.getElementById('campusChart').getContext('2d');

new Chart(campusCtx, {
    type: 'bar',
    data: {
        labels: campusLabels,
        datasets: [{
            label: 'Assets',
            data: campusData,
            backgroundColor: '#0F172A',
            borderRadius: 6,
            maxBarThickness: 40
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false } },
            y: { beginAtZero: true }
        }
    }
});
</script>

</x-app-layout>