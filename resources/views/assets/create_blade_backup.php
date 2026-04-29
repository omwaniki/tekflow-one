<x-app-layout>

<div class="max-w-7xl mx-auto px-8 pt-10 pb-12 space-y-6">

    <!-- Breadcrumb -->
    <x-breadcrumbs :links="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Assets', 'url' => route('assets.index')],
        ['label' => 'Create']
    ]" />

    <!-- Header -->
    <div>
        <h1 class="text-3xl font-semibold text-gray-800">Add New Asset</h1>
        <p class="text-gray-500 mt-1">Register a staff or student device</p>
    </div>

    <!-- Card -->
    <div class="bg-white shadow-sm rounded-xl border p-6 space-y-6">

        <!-- Toggle -->
        <div class="relative inline-flex bg-gray-100 p-1 rounded-xl w-fit">
            <div id="toggleIndicator"
                class="absolute top-1 bottom-1 left-1 w-1/2 bg-white rounded-lg shadow transition-all duration-300">
            </div>

            <button onclick="switchTab('manual')" id="manualTab"
                class="relative z-10 px-5 py-2 text-sm font-medium text-gray-900">
                Manual Entry
            </button>

            <button onclick="switchTab('bulk')" id="bulkTab"
                class="relative z-10 px-5 py-2 text-sm font-medium text-gray-500">
                Bulk Upload
            </button>
        </div>

        <!-- ================= MANUAL ================= -->
        <div id="manualSection">

            <form method="POST" action="{{ route('assets.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Asset Category
                    </label>

                    <select name="type" id="type"
                        class="w-full border-gray-300 rounded-lg h-10 px-3 text-sm">
                        <option value="">Select category</option>
                        <option value="staff">Staff Device</option>
                        <option value="student">Student Device</option>
                    </select>
                </div>

                <div id="dynamicFields" class="hidden space-y-6">

                    <!-- STAFF -->
                    <div id="staffFields" class="hidden space-y-4">
                        <h4 class="text-sm font-semibold text-gray-700">Staff Details</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="text" name="assigned_to_name" placeholder="Staff Name" class="w-full border-gray-300 rounded-lg">
                            <input type="email" name="assigned_to_email" placeholder="Staff Email" class="w-full border-gray-300 rounded-lg">
                            <input type="text" name="role" placeholder="Role" class="w-full border-gray-300 rounded-lg">
                            <input type="text" name="device_type" placeholder="Device Type" class="w-full border-gray-300 rounded-lg">
                        </div>
                    </div>

                    <!-- STUDENT -->
                    <div id="studentFields" class="hidden space-y-4">
                        <h4 class="text-sm font-semibold text-gray-700">Student Device</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="text" name="brand" placeholder="Brand" class="w-full border-gray-300 rounded-lg">
                            <input type="text" name="model" placeholder="Model" class="w-full border-gray-300 rounded-lg">
                        </div>
                    </div>

                    <!-- SHARED -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-semibold text-gray-700">Asset Details</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <input type="text" name="serial_number" placeholder="Serial Number" class="w-full border-gray-300 rounded-lg" required>

                            <select name="status" class="w-full border-gray-300 rounded-lg h-10 px-3 text-sm">
                                <option value="active">Active</option>
                                <option value="faulty">Faulty</option>
                                <option value="retired">Retired</option>
                            </select>

                            <!-- FIXED DATE FIELD -->
                            <div>
                               

                                <input 
                                    type="date" 
                                    name="manufacture_date" 
                                    class="w-full border-gray-300 rounded-lg h-10 px-3 text-sm"
                                >

                                <p class="text-xs text-gray-500 mt-1">
                                    Asset manufacture date (Format: YYYY-MM-DD, e.g. 2024-01-15)
                                </p>
                            </div>

                            <select name="campus_id" class="w-full border-gray-300 rounded-lg h-10 px-3 text-sm">
                                <option value="">Select Campus</option>
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <!-- ACTIONS -->
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('assets.index') }}"
                           class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">
                            Cancel
                        </a>

                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium shadow-sm">
                            Save Asset
                        </button>
                    </div>

                </div>

            </form>

        </div>

        <!-- ================= BULK ================= -->
        <div id="bulkSection" class="hidden space-y-6">

            <div class="border rounded-lg p-5 space-y-5">

                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-800">Bulk Upload</h3>
                        <p class="text-sm text-gray-500">
                            Upload multiple assets using a CSV template
                        </p>
                    </div>

                    <a href="{{ route('assets.template.download') }}"
                       class="px-3 py-2 border rounded-lg text-sm hover:bg-gray-100">
                        Download Template
                    </a>
                </div>

                <!-- Upload -->
                <form method="POST" action="{{ route('assets.import') }}" enctype="multipart/form-data" class="flex gap-2">
                    @csrf
                    <input type="file" name="file" accept=".csv" required class="text-sm">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                        Upload CSV
                    </button>
                </form>

                <!-- Instructions -->
                <div class="bg-gray-50 border rounded-lg p-4 text-sm text-gray-600">
                    <ul class="list-disc ml-5 space-y-1">
                        <li><strong>Device Type</strong>: staff or student</li>
                        <li>Staff → name + email + role + device_type</li>
                        <li>Student → brand + model</li>
                        <li>Use <strong>campus_name</strong> (e.g. Tatu City)</li>
                        <li>serial_number must be unique</li>
                        <li>Date format must be <strong>YYYY-MM-DD</strong></li>
                    </ul>
                </div>

                <!-- Table Sample -->
                <div class="border rounded-lg overflow-hidden">

                    <div class="bg-blue-50 px-4 py-2 text-sm font-medium text-blue-800">
                        Sample CSV Structure
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-[12px] text-left">

                            <thead class="bg-gray-50 text-gray-500 text-[11px] uppercase tracking-wide">
                                <tr>
                                    <th class="px-3 py-2">Category</th>
                                    <th class="px-3 py-2">Staff Name</th>
                                    <th class="px-3 py-2">Staff Email</th>
                                    <th class="px-3 py-2">Role</th>
                                    <th class="px-3 py-2">Device Type</th>
                                    <th class="px-3 py-2">Brand</th>
                                    <th class="px-3 py-2">Model</th>
                                    <th class="px-3 py-2">Serial No</th>
                                    <th class="px-3 py-2">Status</th>
                                    <th class="px-3 py-2">Manufacture Date</th>
                                    <th class="px-3 py-2">Campus</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y text-gray-700 text-[12px]">

                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2">Staff</td>
                                    <td class="px-3 py-2">John Doe</td>
                                    <td class="px-3 py-2">john@email.com</td>
                                    <td class="px-3 py-2">Teacher</td>
                                    <td class="px-3 py-2">Laptop</td>
                                    <td class="px-3 py-2">Lenovo</td>
                                    <td class="px-3 py-2">ThinkBook</td>
                                    <td class="px-3 py-2">SN12345</td>
                                    <td class="px-3 py-2">Active</td>
                                    <td class="px-3 py-2">2024-01-01</td>
                                    <td class="px-3 py-2">Tatu City</td>
                                </tr>

                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2">Student</td>
                                    <td class="px-3 py-2 text-gray-400">—</td>
                                    <td class="px-3 py-2 text-gray-400">—</td>
                                    <td class="px-3 py-2 text-gray-400">—</td>
                                    <td class="px-3 py-2">Chromebook</td>
                                    <td class="px-3 py-2">Lenovo</td>
                                    <td class="px-3 py-2">100e Chromebook</td>
                                    <td class="px-3 py-2">SN67890</td>
                                    <td class="px-3 py-2">Active</td>
                                    <td class="px-3 py-2">2024-01-01</td>
                                    <td class="px-3 py-2">Boksburg</td>
                                </tr>

                            </tbody>

                        </table>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
function switchTab(tab) {
    const indicator = document.getElementById('toggleIndicator');

    if (tab === 'manual') {
        indicator.style.transform = 'translateX(0%)';
        manualSection.classList.remove('hidden');
        bulkSection.classList.add('hidden');
    } else {
        indicator.style.transform = 'translateX(100%)';
        manualSection.classList.add('hidden');
        bulkSection.classList.remove('hidden');
    }
}

const typeSelect = document.getElementById('type');
const dynamicFields = document.getElementById('dynamicFields');
const staffFields = document.getElementById('staffFields');
const studentFields = document.getElementById('studentFields');

typeSelect.addEventListener('change', function() {
    const value = this.value;

    dynamicFields.classList.toggle('hidden', !value);
    staffFields.classList.add('hidden');
    studentFields.classList.add('hidden');

    if (value === 'staff') staffFields.classList.remove('hidden');
    if (value === 'student') studentFields.classList.remove('hidden');
});
</script>

</x-app-layout>