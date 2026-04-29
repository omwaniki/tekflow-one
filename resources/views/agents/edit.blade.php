<x-app-layout>
<div class="space-y-6 max-w-3xl">

    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Edit Agent</h2>
        <p class="text-gray-500">Update agent details</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <form method="POST" action="{{ route('agents.update', $agent->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="name"
                    value="{{ old('name', $agent->name) }}"
                    class="w-full rounded-lg border-gray-300 focus:border-gray-500 focus:ring-gray-500" required>
            </div>

            <!-- Email + Phone -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email"
                        value="{{ old('email', $agent->email) }}"
                        class="w-full rounded-lg border-gray-300 focus:border-gray-500 focus:ring-gray-500" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone"
                        value="{{ old('phone', $agent->phone) }}"
                        class="w-full rounded-lg border-gray-300 focus:border-gray-500 focus:ring-gray-500">
                </div>
            </div>

            <!-- Role -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="roleSelect" name="role"
                    class="w-full rounded-lg border-gray-300 focus:border-gray-500 focus:ring-gray-500" required>

                    <option value="">Select role</option>

                    @foreach($roles as $role)
                        <option value="{{ $role->name }}"
                            {{ old('role', $agent->user->roles->first()->name ?? '') === $role->name ? 'selected' : '' }}>
                            {{ \Illuminate\Support\Str::headline($role->name) }}
                        </option>
                    @endforeach

                </select>
            </div>

            <!-- REGION -->
            <div id="regionField"
                 class="{{ old('role', $agent->user->roles->first()->name ?? '') === 'regional' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Region</label>

                <select id="regionSelect" name="region_id"
                    class="w-full rounded-lg border-gray-300 focus:border-gray-500 focus:ring-gray-500">

                    <option value="">Select region</option>

                    @foreach($regions as $region)
                        <option value="{{ $region->id }}"
                            {{ (string) old('region_id', $agent->region_id ?? $agent->user->region_id) === (string) $region->id ? 'selected' : '' }}>
                            {{ $region->name }}
                        </option>
                    @endforeach

                </select>
            </div>

            <!-- CAMPUS -->
            <div id="campusField"
                 class="{{ old('role', $agent->user->roles->first()->name ?? '') === 'regional' ? 'hidden' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Campus</label>

                <select id="campusSelect" name="campus_id"
                    class="w-full rounded-lg border-gray-300 focus:border-gray-500 focus:ring-gray-500">

                    <option value="">Select campus</option>

                    @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}"
                            data-region="{{ $campus->region_id }}"
                            {{ (string) old('campus_id', $agent->campus_id) === (string) $campus->id ? 'selected' : '' }}>
                            {{ $campus->name }}
                        </option>
                    @endforeach

                </select>
            </div>

            <!-- Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password"
                        class="w-full rounded-lg border-gray-300 focus:border-gray-500 focus:ring-gray-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                        class="w-full rounded-lg border-gray-300 focus:border-gray-500 focus:ring-gray-500">
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('agents.index') }}"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>

                <button type="submit"
                    class="px-5 py-2 rounded-lg bg-gray-900 text-white hover:bg-black transition">
                    Update Agent
                </button>
            </div>

        </form>
    </div>

</div>

<!-- ✅ FIXED SCRIPT -->
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

            campus.value = '';

            // ✅ ensure region is submitted
            region.disabled = false;

        } else {
            regionField.classList.add('hidden');
            campusField.classList.remove('hidden');

            // ✅ prevent sending empty region
            region.disabled = true;
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

    // ✅ run on load (VERY IMPORTANT FIX)
    toggleFields();
});
</script>

</x-app-layout>