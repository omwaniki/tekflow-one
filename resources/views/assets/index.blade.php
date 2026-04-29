<x-app-layout>
    <div class="space-y-6 min-w-0">

        {{-- PAGE HEADER --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Assets</h2>
                <p class="text-gray-500">Manage and track all assets</p>
            </div>

            @can('create assets')
            <a href="{{ route('assets.create') }}"
            class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-900">
                + Add Asset
            </a>
            @endcan
        </div>

        {{-- KPI CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <p class="text-gray-500 text-sm">Active</p>
                <p class="text-3xl font-bold text-green-600 mt-2">{{ $statusCounts['active'] }}</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <p class="text-gray-500 text-sm">Faulty</p>
                <p class="text-3xl font-bold text-yellow-500 mt-2">{{ $statusCounts['faulty'] }}</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <p class="text-gray-500 text-sm">Retired</p>
                <p class="text-3xl font-bold text-red-500 mt-2">{{ $statusCounts['retired'] }}</p>
            </div>
        </div>

        {{-- FILTERS --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <form method="GET" action="{{ route('assets.index') }}" class="flex flex-col md:flex-row gap-4 items-center">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search staff, email, serial, brand, model..."
                    class="w-full md:w-80 border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-200"
                >

                <select
                    name="status"
                    class="w-full md:w-48 border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-200"
                >
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="faulty" {{ request('status') == 'faulty' ? 'selected' : '' }}>Faulty</option>
                    <option value="retired" {{ request('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                </select>

                <select
                    name="campus_id"
                    class="w-full md:w-48 border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-200"
                >
                    <option value="">All Campuses</option>
                    @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}" {{ request('campus_id') == $campus->id ? 'selected' : '' }}>
                            {{ $campus->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Actions -->
                 <div class="flex gap-2">

                    <!-- Search -->
                    <button
                        type="submit"
                        class="bg-gray-800 text-white px-6 py-3 rounded-xl hover:bg-gray-900"
                    >
                        Search
                    </button>

                    <!-- 🔥 Reset Icon (hidden initially) -->
                    <a href="{{ route('assets.index') }}"
                    id="resetFiltersBtn"
                    class="hidden inline-flex items-center justify-center w-11 h-11 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-transform duration-200 hover:rotate-[-20deg]"
                    title="Reset filters">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 14l-5-5m0 0l5-5m-5 5h11a4 4 0 110 8h-1" />
                        </svg>

                    </a>

                </div>
            

            </form>
        </div>

        {{-- TOGGLE / TABS --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-3">
            <div class="inline-flex bg-gray-100 rounded-xl p-1 gap-1">
                <button
                    type="button"
                    id="staffTabBtn"
                    onclick="showAssetTab('staff')"
                    class="asset-tab-btn px-5 py-2.5 rounded-lg text-sm font-medium bg-white text-gray-900 shadow-sm"
                >
                    Staff Devices
                </button>

                <button
                    type="button"
                    id="studentTabBtn"
                    onclick="showAssetTab('student')"
                    class="asset-tab-btn px-5 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:text-gray-900"
                >
                    Student Devices
                </button>
            </div>
        </div>

        {{-- STAFF DEVICES TABLE --}}
        <div id="staffPanel" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden min-w-0">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Staff Devices</h3>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="min-w-[1100px] w-full text-xs text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase tracking-wider">
                        <tr>
                            <th class="px-3 py-3 whitespace-nowrap">Subsidiary</th>
                            <th class="px-3 py-3 whitespace-nowrap">Staff Name</th>
                            <th class="px-3 py-3 whitespace-nowrap">Role</th>
                            <th class="px-3 py-3 whitespace-nowrap">Email</th>
                            <th class="px-3 py-3 whitespace-nowrap">Device Type</th>
                            <th class="px-3 py-3 whitespace-nowrap">Model</th>
                            <th class="px-3 py-3 whitespace-nowrap">Serial Number</th>
                            <th class="px-3 py-3 whitespace-nowrap">Device Status</th>
                            <th class="px-3 py-3 whitespace-nowrap">Manufacture Date</th>
                            <th class="px-3 py-3 whitespace-nowrap">Avg Device Age (Yrs)</th>
                            <th class="px-3 py-3 whitespace-nowrap">Agent</th>
                            <th class="px-3 py-3 whitespace-nowrap sticky right-0 bg-gray-50 z-10 text-right border-l border-gray-100 shadow-sm">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse($staffAssets as $asset)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                    {{ $asset->campus->name ?? '—' }}
                                </td>

                                <td class="px-3 py-2 whitespace-nowrap font-medium text-gray-800">
                                    {{ $asset->assigned_to_name ?? '—' }}
                                </td>

                                <td class="px-3 py-2 text-gray-700 max-w-[160px] truncate" title="{{ $asset->role }}">
                                    {{ $asset->role ?? '—' }}
                                </td>

                                <td class="px-3 py-2 text-gray-700 max-w-[220px] truncate" title="{{ $asset->assigned_to_email }}">
                                    {{ $asset->assigned_to_email ?? '—' }}
                                </td>

                                <td class="px-3 py-2 text-gray-700 max-w-[140px] truncate" title="{{ $asset->device_type }}">
                                    {{ $asset->device_type ?? '—' }}
                                </td>

                                <td class="px-3 py-2 text-gray-700 max-w-[140px] truncate" title="{{ $asset->model }}">
                                    {{ $asset->model ?? '—' }}
                                </td>


                                <td class="px-3 py-2 whitespace-nowrap font-mono text-[11px] text-gray-700">
                                    {{ $asset->serial_number ?? '—' }}
                                </td>

                                <td class="px-3 py-2 whitespace-nowrap">
                                    @if($asset->status === 'active')
                                        <span class="px-2 py-1 text-[11px] rounded-full bg-green-100 text-green-700">
                                            Active
                                        </span>
                                    @elseif($asset->status === 'faulty')
                                        <span class="px-2 py-1 text-[11px] rounded-full bg-yellow-100 text-yellow-700">
                                            Faulty
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-[11px] rounded-full bg-red-100 text-red-700">
                                            Retired
                                        </span>
                                    @endif
                                </td>

                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                    {{ $asset->manufacture_date ? \Carbon\Carbon::parse($asset->manufacture_date)->format('Y-m-d') : '—' }}
                                </td>

                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                    {{ $asset->age ?? 0 }}
                                </td>

                                <td class="px-3 py-2 whitespace-nowrap sticky right-0 bg-white z-10 text-right border-l border-gray-100">

                                    <td class="px-3 py-2 whitespace-nowrap sticky right-0 bg-white z-10 text-right border-l border-gray-100 shadow-[-4px_0_6px_-2px_rgba(0,0,0,0.05)]">

                                        <div class="flex justify-end items-center gap-2">

                                            <!-- Quick View -->
                                            <button onclick='openQuickView(@json($asset))'
                                                class="text-gray-500 hover:text-gray-700 transition"
                                                title="Quick View">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                            </button>

                                            <!-- View -->
                                            <!--<a href="{{ route('assets.show', $asset->id) }}"
                                            class="text-gray-500 hover:text-gray-700 transition"
                                            title="View">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5
                                                            c4.477 0 8.268 2.943 9.542 7
                                                            -1.274 4.057-5.065 7-9.542 7
                                                            -4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a> -->

                                            <!-- Timeline -->
                                            <!--<a href="{{ route('assets.timeline', $asset->id) }}"
                                            class="text-gray-500 hover:text-purple-600 transition"
                                            title="Timeline">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M3 3v18h18M7 14l3-3 3 3 5-5" />
                                                </svg>
                                            </a>-->

                                            <!-- Timeline -->
                                            <a href="{{ route('assets.show', $asset->id) }}"
                                            class="text-gray-500 hover:text-purple-600 transition"
                                            title="Timeline">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M3 3v18h18M7 14l3-3 3 3 5-5" />
                                                </svg>
                                            </a>

                                            <!-- Edit -->
                                            @can('edit assets')
                                            <a href="{{ route('assets.edit', $asset->id) }}"
                                            class="text-gray-500 hover:text-blue-600 transition"
                                            title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.213
                                                            3 21l1.787-4.5L16.862 3.487z" />
                                                </svg>
                                            </a>
                                            @endcan

                                            <!-- Delete -->
                                            @can('delete assets')
                                            <form method="POST" action="{{ route('assets.destroy', $asset->id) }}">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        onclick="return confirm('Delete this asset?')"
                                                        class="text-gray-500 hover:text-red-600 transition"
                                                        title="Delete">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 7h12M9 7V4h6v3m-7 4v6m4-6v6M5 7l1 14h12l1-14" />
                                                    </svg>
                                                </button>
                                            </form>
                                            @endcan

                                        </div>

                                    </td>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-4 py-6 text-center text-gray-500">
                                    No staff devices found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- STUDENT DEVICES TABLE --}}
        <div id="studentPanel" class="hidden bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden min-w-0">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Student Devices</h3>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="min-w-[1200px] w-full text-xs text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase tracking-wider">
                        <tr>
                            <th class="px-3 py-3 whitespace-nowrap">Subsidiary</th>
                            <th class="px-3 py-3 whitespace-nowrap">Device Brand</th>
                            <th class="px-3 py-3 whitespace-nowrap">Model</th>
                            <th class="px-3 py-3 whitespace-nowrap">Serial Number</th>
                            <th class="px-3 py-3 whitespace-nowrap">Device Status</th>
                            <th class="px-3 py-3 whitespace-nowrap">Manufacture Date</th>
                            <th class="px-3 py-3 whitespace-nowrap">Avg Device Age (Yrs)</th>
                            <th class="px-3 py-3 whitespace-nowrap sticky right-0 bg-gray-50 z-10 text-right border-l border-gray-100 shadow-sm">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse($studentAssets as $asset)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                    {{ $asset->campus->name ?? '—' }}
                                </td>

                                <td class="px-3 py-2 whitespace-nowrap font-medium text-gray-800">
                                    {{ $asset->brand ?? '—' }}
                                </td>

                                <td class="px-3 py-2 text-gray-700 max-w-[160px] truncate" title="{{ $asset->model }}">
                                    {{ $asset->model ?? '—' }}
                                </td>

                                <td class="px-3 py-2 whitespace-nowrap font-mono text-[11px] text-gray-700">
                                    {{ $asset->serial_number ?? '—' }}
                                </td>

                                <td class="px-3 py-2 whitespace-nowrap">
                                    @if($asset->status === 'active')
                                        <span class="px-2 py-1 text-[11px] rounded-full bg-green-100 text-green-700">
                                            Active
                                        </span>
                                    @elseif($asset->status === 'faulty')
                                        <span class="px-2 py-1 text-[11px] rounded-full bg-yellow-100 text-yellow-700">
                                            Faulty
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-[11px] rounded-full bg-red-100 text-red-700">
                                            Retired
                                        </span>
                                    @endif
                                </td>

                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                    {{ $asset->manufacture_date ? \Carbon\Carbon::parse($asset->manufacture_date)->format('Y-m-d') : '—' }}
                                </td>

                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                    {{ $asset->age ?? 0 }}
                                </td>

                                <td class="px-3 py-2 whitespace-nowrap sticky right-0 bg-white z-10 text-right border-l border-gray-100 shadow-[-4px_0_6px_-2px_rgba(0,0,0,0.05)]">

                                    <div class="flex justify-end items-center gap-2">

                                        <!-- Quick View -->
                                        <button onclick='openQuickView(@json($asset))'
                                            class="text-gray-500 hover:text-gray-700 transition"
                                            title="Quick View">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </button>

                                        <!-- View -->
                                        <a href="{{ route('assets.show', $asset->id) }}"
                                        class="text-gray-500 hover:text-gray-700 transition"
                                        title="View">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5
                                                        c4.477 0 8.268 2.943 9.542 7
                                                        -1.274 4.057-5.065 7-9.542 7
                                                        -4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        <!-- Timeline -->
                                        <a href="{{ route('assets.timeline', $asset->id) }}"
                                        class="text-gray-500 hover:text-purple-600 transition"
                                        title="Timeline">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 3v18h18M7 14l3-3 3 3 5-5" />
                                            </svg>
                                        </a>

                                        <!-- Edit -->
                                        @can('edit assets')
                                        <a href="{{ route('assets.edit', $asset->id) }}"
                                        class="text-gray-500 hover:text-blue-600 transition"
                                        title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.213
                                                        3 21l1.787-4.5L16.862 3.487z" />
                                            </svg>
                                        </a>
                                        @endcan

                                        <!-- Delete -->
                                        @can('delete assets')
                                        <form method="POST" action="{{ route('assets.destroy', $asset->id) }}">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    onclick="return confirm('Delete this asset?')"
                                                    class="text-gray-500 hover:text-red-600 transition"
                                                    title="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 7h12M9 7V4h6v3m-7 4v6m4-6v6M5 7l1 14h12l1-14" />
                                                </svg>
                                            </button>
                                        </form>
                                        @endcan

                                    </div>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                    No student devices found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{--Quick view overlay modal--}}
    <div id="quickViewModal"
        class="fixed inset-0 bg-black/40 hidden flex items-center justify-center z-50">

        <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-xl">

            <h3 class="text-lg font-semibold mb-4">Device Quick View</h3>

            <div id="quickViewContent"></div>

            <button onclick="closeQuickView()"
                    class="mt-6 px-4 py-2 bg-gray-800 text-white rounded-lg">
                Close
            </button>

        </div>
    </div>


    <script>
        function showAssetTab(tab) {
            const staffPanel = document.getElementById('staffPanel');
            const studentPanel = document.getElementById('studentPanel');
            const staffBtn = document.getElementById('staffTabBtn');
            const studentBtn = document.getElementById('studentTabBtn');

            if (tab === 'staff') {
                staffPanel.classList.remove('hidden');
                studentPanel.classList.add('hidden');

                staffBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
                staffBtn.classList.remove('text-gray-600');

                studentBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
                studentBtn.classList.add('text-gray-600');
            } else {
                studentPanel.classList.remove('hidden');
                staffPanel.classList.add('hidden');

                studentBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
                studentBtn.classList.remove('text-gray-600');

                staffBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
                staffBtn.classList.add('text-gray-600');
            }
        }

        function toggleDropdown(id) {
            const el = document.getElementById('dropdown-' + id);

            document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                if (d !== el) d.classList.add('hidden');
            });

            el.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('[onclick^="toggleDropdown"]')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                    d.classList.add('hidden');
                });
            }
        });

        function openQuickView(asset) {
            const modal = document.getElementById('quickViewModal');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // 🔥 ADD THIS LINE
            modal.style.pointerEvents = 'auto';

            document.getElementById('quickViewContent').innerHTML = `
                <div class="space-y-3 text-sm text-gray-700">

                    <div>
                        <p class="text-gray-500">Device</p>
                        <p class="font-medium">${asset.device_type ?? '—'}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Serial Number</p>
                        <p class="font-mono">${asset.serial_number ?? '—'}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Status</p>
                        <p class="font-medium capitalize">${asset.status}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Assigned To</p>
                        <p class="font-medium">${asset.assigned_to_name ?? '—'}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Email</p>
                        <p class="font-medium">${asset.assigned_to_email ?? '—'}</p>
                    </div>

                </div>
            `;
        }

        function closeQuickView() {
            const modal = document.getElementById('quickViewModal');

            modal.classList.add('hidden');
            modal.classList.remove('flex');

            // 🔥 THIS LINE FIXES THE DOUBLE-CLICK ISSUE
            modal.style.pointerEvents = 'none';
        }

        // ✅ ADD THIS HERE
        document.getElementById('quickViewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeQuickView();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeQuickView();
            }
        });
    </script>




    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const searchInput = document.querySelector('input[name="search"]');
        const statusSelect = document.querySelector('select[name="status"]');
        const campusSelect = document.querySelector('select[name="campus_id"]');
        const resetBtn = document.getElementById('resetFiltersBtn');

        function checkFilters() {
            const hasValue =
                (searchInput && searchInput.value.trim() !== '') ||
                (statusSelect && statusSelect.value !== '') ||
                (campusSelect && campusSelect.value !== '');

            if (hasValue) {
                resetBtn.classList.remove('hidden');
            } else {
                resetBtn.classList.add('hidden');
            }
        }

        // Listen to changes
        searchInput?.addEventListener('input', checkFilters);
        statusSelect?.addEventListener('change', checkFilters);
        campusSelect?.addEventListener('change', checkFilters);

        // Run on page load (for persisted filters)
        checkFilters();

    });

    function closeQuickView() {
    const modal = document.getElementById('quickViewModal');

    modal.classList.add('hidden');
    modal.classList.remove('flex');

    // 🔥 THIS IS THE FIX FOR DOUBLE-CLICK ISSUE
    modal.style.pointerEvents = 'none';
}
    </script>

    

</x-app-layout>