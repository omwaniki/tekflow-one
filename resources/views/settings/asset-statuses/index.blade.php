<x-app-layout>

<div class="max-w-7xl mx-auto px-8 pt-10 pb-12 space-y-6">

    <!-- Header -->
    <div>
        <h2 class="text-3xl font-semibold text-gray-800">
            Asset Statuses
        </h2>
        <p class="text-gray-500 mt-1">
            Manage system-wide asset statuses
        </p>
    </div>

    <!-- Success -->
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Errors -->
    @if ($errors->any())
        <div class="bg-red-100 text-red-800 px-4 py-3 rounded-lg">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Create Status (UNCHANGED, just positioned properly) -->
    <div class="bg-white p-6 rounded-2xl border shadow-sm space-y-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Create New Status</h3>
            <p class="text-sm text-gray-500">Add a new asset status to standardize tracking</p>
        </div>

        <form method="POST" action="{{ route('asset-statuses.store') }}" 
              class="flex items-center gap-3">
            @csrf

            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                placeholder="Enter status name (e.g. Lost, Retired)"
                class="flex-1 border rounded-xl px-4 py-2"
                required
            >

            <select
                name="color"
                required
                class="min-w-[180px] border rounded-xl pl-4 pr-10 py-2 bg-white text-gray-700 appearance-none"
            >
                <option value="" disabled selected>Choose color</option>
                <option value="green">Green</option>
                <option value="red">Red</option>
                <option value="blue">Blue</option>
                <option value="purple">Purple</option>
                <option value="gray">Gray</option>
                <option value="yellow">Yellow</option>
                <option value="black">Black</option>
            </select>

            <button
                type="submit"
                class="flex items-center gap-2 px-5 py-2 rounded-xl text-white font-semibold shadow-md"
                style="background-color:#2563eb;">
                Create
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white shadow-sm rounded-xl overflow-hidden border">

        <table class="min-w-full">

            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">
                        Status
                    </th>

                    <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">
                        Preview
                    </th>

                    <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">
                        Used By
                    </th>

                    <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">
                        Active
                    </th>

                    <th class="text-right px-6 py-4 text-sm font-semibold text-gray-600">
                        Actions
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y">

                @forelse($statuses as $status)

                @php
                    $colorMap = [
                        'green' => 'bg-green-100 text-green-700',
                        'red' => 'bg-red-100 text-red-700',
                        'blue' => 'bg-blue-100 text-blue-700',
                        'purple' => 'bg-purple-100 text-purple-700',
                        'gray' => 'bg-gray-100 text-gray-700',
                        'yellow' => 'bg-yellow-100 text-yellow-700',
                        'black' => 'bg-gray-800 text-white',
                    ];
                    $classes = $colorMap[$status->color] ?? 'bg-gray-100 text-gray-700';
                @endphp

                <tr class="hover:bg-gray-50 transition">

                    <td class="px-6 py-4 font-medium text-gray-800">
                        {{ $status->name }}
                    </td>

                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-xs rounded-full {{ $classes }}">
                            {{ $status->name }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-gray-700">
                        {{ $status->assets_count }} assets
                    </td>

                    <td class="px-6 py-4 text-gray-700">
                        {{ $status->is_active ? 'Yes' : 'No' }}
                    </td>

                    <td class="px-6 py-4 text-right">

                        <button
                            type="button"
                            onclick='openEdit({{ $status->id }}, @json($status->name), @json($status->color))'
                            class="text-indigo-600 hover:text-indigo-800 font-medium mr-5">
                            Edit
                        </button>

                        @if($status->assets_count > 0)
                            <span class="text-gray-400 text-sm">In Use</span>
                        @else
                            <form method="POST" action="{{ route('asset-statuses.destroy', $status->id) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 font-medium">
                                    Delete
                                </button>
                            </form>
                        @endif

                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="5" class="text-center text-gray-500 py-6">
                        No statuses found.
                    </td>
                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

<!-- EDIT MODAL (UNCHANGED) -->
<div id="editModal"
     onclick="closeEdit()"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 transition-opacity duration-200 opacity-0">

    <div id="modalContent"
         onclick="event.stopPropagation()"
         class="bg-white w-[90%] max-w-md rounded-2xl shadow-xl p-6 space-y-4 transform transition-all duration-200 scale-95 opacity-0">

        <div>
            <h3 class="text-lg font-semibold text-gray-800">Edit Status</h3>
            <p class="text-sm text-gray-500">Update asset status details</p>
        </div>

        <form method="POST" id="editFormElement" class="space-y-4">
            @csrf
            @method('PUT')

            <input type="text" name="name" id="editName"
                   class="w-full border rounded-xl px-4 py-2" required>

            <select name="color" id="editColor"
                    class="w-full border rounded-xl px-4 py-2 bg-white" required>
                <option value="" disabled>Choose color</option>
                <option value="green">Green</option>
                <option value="red">Red</option>
                <option value="blue">Blue</option>
                <option value="purple">Purple</option>
                <option value="gray">Gray</option>
                <option value="yellow">Yellow</option>
                <option value="black">Black</option>
            </select>

            <div class="flex justify-end gap-2">
                <button type="button"
                        onclick="closeEdit()"
                        class="px-4 py-2 rounded-xl border text-gray-600 hover:bg-gray-100">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 rounded-xl text-white"
                        style="background-color:#16a34a;">
                    Update
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Scripts -->
<script>
function openEdit(id, name, color) {
    const modal = document.getElementById('editModal');
    const content = document.getElementById('modalContent');

    modal.classList.remove('hidden');

    setTimeout(() => {
        modal.classList.remove('opacity-0');
        content.classList.remove('scale-95', 'opacity-0');
    }, 10);

    document.getElementById('editName').value = name;
    document.getElementById('editColor').value = color;
    document.getElementById('editFormElement').action = `/settings/asset-statuses/${id}`;

    setTimeout(() => {
        document.getElementById('editName').focus();
    }, 50);

    document.addEventListener('keydown', handleEscClose);
    enableFocusTrap();
}

function closeEdit() {
    const modal = document.getElementById('editModal');
    const content = document.getElementById('modalContent');

    modal.classList.add('opacity-0');
    content.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        document.getElementById('editName').value = '';
        document.getElementById('editColor').value = '';
    }, 200);

    document.removeEventListener('keydown', handleEscClose);
}

function handleEscClose(e) {
    if (e.key === 'Escape') closeEdit();
}

function enableFocusTrap() {
    const modal = document.getElementById('editModal');

    const focusable = modal.querySelectorAll(
        'a[href], button:not([disabled]), textarea, input, select'
    );

    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    modal.addEventListener('keydown', function (e) {
        if (e.key !== 'Tab') return;

        if (e.shiftKey) {
            if (document.activeElement === first) {
                e.preventDefault();
                last.focus();
            }
        } else {
            if (document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        }
    });
}
</script>

</x-app-layout>