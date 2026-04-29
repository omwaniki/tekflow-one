<x-app-layout>
<div class="space-y-6 max-w-3xl">

    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Create Agent</h2>
        <p class="text-gray-500">Add a new agent or send an invite</p>
    </div>

    <!-- Toggle -->
    <div class="flex gap-2 bg-gray-100 p-1 rounded-xl w-fit">
        <button onclick="switchTab('create')" id="createTab"
            class="px-4 py-2 rounded-lg bg-white shadow text-sm font-medium">
            Create Agent
        </button>

        <button onclick="switchTab('invite')" id="inviteTab"
            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600">
            Invite Agent
        </button>
    </div>

    <div id="createForm" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <form method="POST" action="{{ route('agents.store') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <input type="text" name="name" value="{{ old('name') }}"
                class="w-full rounded-lg border-gray-300" placeholder="Full Name" required>

            <!-- Email + Phone -->
            <div class="grid grid-cols-2 gap-4">
                <input type="email" name="email" value="{{ old('email') }}"
                    class="rounded-lg border-gray-300" placeholder="Email" required>

                <input type="text" name="phone" value="{{ old('phone') }}"
                    class="rounded-lg border-gray-300" placeholder="Phone">
            </div>

            <!-- Role -->
            <select id="roleSelect" name="role"
                class="w-full rounded-lg border-gray-300" required>
                <option value="">Select role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>

            <!-- Region -->
            <div id="regionField" class="hidden">
                <select id="regionSelect" name="region_id"
                    class="w-full rounded-lg border-gray-300">
                    <option value="">Select region</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Campus -->
            <div id="campusField">
                <select id="campusSelect" name="campus_id"
                    class="w-full rounded-lg border-gray-300">
                    <option value="">Select campus</option>
                    @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}" data-region="{{ $campus->region_id }}">
                            {{ $campus->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Password -->
            <div class="grid grid-cols-2 gap-4">
                <input type="password" name="password" class="rounded-lg border-gray-300" required>
                <input type="password" name="password_confirmation" class="rounded-lg border-gray-300" required>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2">
                <a href="{{ route('agents.index') }}" class="border px-4 py-2 rounded-lg">Cancel</a>
                <button class="bg-black text-white px-5 py-2 rounded-lg">Create</button>
            </div>

        </form>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const role = document.getElementById('roleSelect');
    const regionField = document.getElementById('regionField');
    const campusField = document.getElementById('campusField');
    const campus = document.getElementById('campusSelect');
    const region = document.getElementById('regionSelect');

    function toggleFields() {
        if (['regional', 'manager'].includes(role.value)) {
            regionField.classList.remove('hidden');
            campusField.classList.add('hidden');
            campus.value = ''; // 🔥 CLEAR campus
        } else {
            regionField.classList.add('hidden');
            campusField.classList.remove('hidden');
        }
    }

    function syncRegionFromCampus() {
        const selected = campus.options[campus.selectedIndex];
        const regionId = selected.getAttribute('data-region');

        if (regionId) {
            region.value = regionId;
        }
    }

    role.addEventListener('change', toggleFields);
    campus.addEventListener('change', syncRegionFromCampus);

    toggleFields();
});
</script>

</x-app-layout>