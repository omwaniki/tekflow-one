<x-app-layout>

<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Agents</h2>
            <p class="text-gray-500">Manage system agents and technicians</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('agents.invite') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                Invite Agent
            </a>

            <a href="{{ route('agents.create') }}"
               class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-900">
                + Add Agent
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <!-- 🔥 BULK + SEARCH -->
        <div class="flex items-center justify-between p-4 border-b bg-gray-50">

            <div class="flex items-center gap-3">
                <button id="bulk-delete"
                    class="bg-red-600 text-white px-3 py-2 rounded-lg text-sm disabled:opacity-50"
                    disabled>
                    Delete Selected
                </button>

                <span id="selected-count" class="text-sm text-gray-500"></span>
            </div>

            <input type="text" id="search"
                   placeholder="Search agents..."
                   class="border rounded-lg px-3 py-2 text-sm w-64">
        </div>

        <!-- 🔥 BULK FORM -->
        <form method="POST" action="{{ route('agents.bulkDelete') }}" id="bulk-form">
            @csrf
            @method('DELETE')

            <input type="hidden" name="ids" id="selected-ids">

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">

                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <!-- CHECKBOX -->
                            <th class="px-6 py-3">
                                <input type="checkbox" id="select-all">
                            </th>

                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Phone</th>
                            <th class="px-6 py-3">Campus</th>
                            <th class="px-6 py-3">Region</th>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>

                    <!-- 🔥 ADD ID HERE -->
                    <tbody id="table-body" class="divide-y">

                        @forelse($agents as $agent)

                            @php
                                $role = $agent->user?->getRoleNames()->first();
                            @endphp

                            <tr class="hover:bg-gray-50">

                                <!-- CHECKBOX -->
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="row-checkbox" value="{{ $agent->id }}">
                                </td>

                                <td class="px-6 py-4 font-medium text-gray-800">
                                    {{ $agent->name }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $agent->email ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $agent->phone ?? '-' }}
                                </td>

                                <!-- 🔥 ORIGINAL CAMPUS LOGIC (UNCHANGED) -->
                                <td class="px-6 py-4 text-gray-600">
                                    @if($role === 'global' || $role === 'admin')
                                        <span class="text-gray-500 italic">
                                            All Campuses
                                        </span>

                                    @elseif(in_array($role, ['regional', 'manager']))
                                        <span class="text-gray-500 italic">
                                            All {{ $agent->region->name ?? '' }} Campuses
                                        </span>

                                    @else
                                        {{ $agent->campus->name ?? '-' }}
                                    @endif
                                </td>

                                <!-- 🔥 ORIGINAL REGION LOGIC (UNCHANGED) -->
                                <td class="px-6 py-4 text-gray-600">
                                    @if($role === 'global' || $role === 'admin')
                                        <span class="text-gray-500 italic">
                                            All Regions
                                        </span>
                                    @else
                                        {{ $agent->region->name ?? '-' }}
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @if($role)
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                            {{ ucfirst($role) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-3">

                                        <a href="{{ route('agents.edit', $agent) }}"
                                           class="text-blue-600 hover:underline text-sm">
                                            Edit
                                        </a>

                                        <form method="POST"
                                              action="{{ route('agents.destroy', $agent) }}"
                                              onsubmit="return confirm('Are you sure you want to delete this agent?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="text-red-600 hover:underline text-sm">
                                                Delete
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10 text-gray-400">
                                    No agents found
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </form>

        <!-- 🔥 PAGINATION -->
        <div id="pagination" class="p-4">
            {{ $agents->links() }}
        </div>

    </div>

</div>

<!-- 🔥 JS (LIVE SEARCH + BULK) -->
<script>
let debounceTimer;

const searchInput = document.getElementById('search');
const tableBody = document.getElementById('table-body');
const pagination = document.getElementById('pagination');

searchInput.addEventListener('keyup', function () {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        fetch(`{{ route('agents.index') }}?search=${this.value}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            tableBody.innerHTML = data.table;
            pagination.innerHTML = data.pagination;
            initCheckboxes();
        });
    }, 300);
});

function initCheckboxes() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const bulkDeleteBtn = document.getElementById('bulk-delete');
    const selectedIdsInput = document.getElementById('selected-ids');
    const selectedCount = document.getElementById('selected-count');

    function updateSelection() {
        const selected = [...checkboxes]
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        bulkDeleteBtn.disabled = selected.length === 0;
        selectedIdsInput.value = selected.join(',');
        selectedCount.innerText = selected.length + ' selected';
    }

    selectAll?.addEventListener('change', () => {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateSelection();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateSelection);
    });

    bulkDeleteBtn.addEventListener('click', (e) => {
        if (!confirm('Delete selected agents?')) {
            e.preventDefault();
        }
    });
}

initCheckboxes();
</script>

</x-app-layout>