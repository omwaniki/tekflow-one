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

            <!-- STEP 1: CATEGORY SELECTOR -->
            <div class="bg-white border rounded-xl p-5 space-y-4">

                <h3 class="text-sm font-semibold text-gray-700">Select Upload Type</h3>

                <div class="flex gap-2 bg-gray-100 p-1 rounded-xl w-fit">
                    <button onclick="selectBulkType('staff')" id="staffBulkBtn"
                        class="px-4 py-2 text-sm rounded-lg bg-white shadow font-medium">
                        Staff Devices
                    </button>

                    <button onclick="selectBulkType('student')" id="studentBulkBtn"
                        class="px-4 py-2 text-sm rounded-lg text-gray-500">
                        Student Devices
                    </button>
                </div>

            </div>

            <!-- STEP 2: BULK CONTENT -->
            <div id="bulkContent" class="hidden border rounded-lg p-5 space-y-5 bg-white">

                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-800">Bulk Upload</h3>
                        <p class="text-sm text-gray-500">
                            Upload multiple assets using a CSV template
                        </p>
                    </div>

                    <a id="templateLink"
                    href="#"
                    class="px-3 py-2 border rounded-lg text-sm hover:bg-gray-100">
                        Download Template
                    </a>
                </div>

                <!-- Upload -->
                <form method="POST" action="{{ route('assets.preview') }}" enctype="multipart/form-data" class="flex gap-2">
                    @csrf

                    <!-- IMPORTANT: PASS TYPE -->
                    <input type="hidden" name="type" id="bulkType">

                    <input type="file" name="file" accept=".csv" required class="text-sm">

                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                        Upload CSV
                    </button>
                </form>

                <!--Upload preview-->
                @if(session('preview_data'))

                <div class="mt-6 border rounded-lg overflow-hidden">

                    <div class="bg-blue-50 px-4 py-2 text-sm font-medium text-blue-800">
                        Preview Data (First 10 Rows)
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">

                            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                <tr>
                                    @foreach(session('preview_data.headers') as $header)
                                        <th class="px-4 py-2">{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody class="divide-y">
                                @foreach(session('preview_data.rows') as $row)
                                    <tr>
                                        @foreach($row as $cell)
                                            <td class="px-4 py-2">{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    <!-- ✅ CONFIRM BUTTON -->
                    <div class="flex justify-end gap-3 p-4 border-t">

                        <form method="POST" action="{{ route('assets.import') }}">
                            @csrf

                            <!-- 🔥 THIS IS CRITICAL -->
                            <input type="hidden" name="file" value="{{ session('preview_data.file') }}">

                            <input type="hidden" name="type" value="{{ session('preview_data.type') }}">

                            <button class="bg-green-600 text-white px-4 py-2 rounded-lg">
                                Confirm Import
                            </button>
                        </form>

                        <a href="{{ route('assets.create') }}"
                        class="px-4 py-2 border rounded-lg text-gray-600">
                            Cancel
                        </a>

                    </div>

                </div>

                @endif

                <!--ERROR DISPLAY DURING IMPORT-->
                @if(session('import_summary'))

                    <div class="mt-6 space-y-4">

                        <!-- Summary -->
                        <div class="p-4 rounded-lg border bg-gray-50">
                            <p class="text-sm text-gray-700">
                                <strong>{{ session('import_summary.success') }}</strong> successful,
                                <strong>{{ session('import_summary.failed') }}</strong> failed
                            </p>
                        </div>

                        <!-- Errors Table -->
                        @if(!empty(session('import_summary.errors')))
                            <div class="border rounded-lg overflow-hidden">

                                <div class="bg-red-50 px-4 py-2 text-sm font-medium text-red-700">
                                    Upload Errors
                                </div>

                                <div class="max-h-64 overflow-y-auto">
                                    <table class="w-full text-sm text-left">

                                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                                            <tr>
                                                <th class="px-4 py-2">Row</th>
                                                <th class="px-4 py-2">Error</th>
                                            </tr>
                                        </thead>

                                        <tbody class="divide-y">

                                            @foreach(session('import_summary.errors') as $error)
                                                <tr>
                                                    <td class="px-4 py-2 font-medium text-gray-700">
                                                        {{ $error['row'] }}
                                                    </td>
                                                    <td class="px-4 py-2 {{ str_contains($error['message'], 'Invalid') ? 'text-red-600' : 'text-orange-500' }}">
                                                        {{ $error['message'] }}
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>

                                    </table>
                                </div>

                            </div>

                            <!-- Download Errors -->
                            <div>
                                <a href="{{ route('assets.downloadFailed') }}"
                                class="text-sm text-blue-600 hover:underline">
                                    Download Error Report
                                </a>
                            </div>
                        @endif

                    </div>

                @endif

                <!-- INSTRUCTIONS -->
                <div id="instructionsBox" class="bg-gray-50 border rounded-lg p-4 text-sm text-gray-600"></div>

                <!-- SAMPLE TABLE -->
                <div id="sampleTableBox" class="border rounded-lg overflow-hidden"></div>

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

/*Smart upload script*/
function selectBulkType(type) {

    const bulkContent = document.getElementById('bulkContent');
    const templateLink = document.getElementById('templateLink');
    const instructionsBox = document.getElementById('instructionsBox');
    const sampleTableBox = document.getElementById('sampleTableBox');
    const bulkTypeInput = document.getElementById('bulkType');

    const staffBtn = document.getElementById('staffBulkBtn');
    const studentBtn = document.getElementById('studentBulkBtn');

    // Show content
    bulkContent.classList.remove('hidden');

    // Set type
    bulkTypeInput.value = type;

    // Toggle button styles
    staffBtn.classList.remove('bg-white', 'shadow');
    studentBtn.classList.remove('bg-white', 'shadow');

    if (type === 'staff') {
        staffBtn.classList.add('bg-white', 'shadow');
    } else {
        studentBtn.classList.add('bg-white', 'shadow');
    }

    // TEMPLATE LINK
    templateLink.href = `/assets/template/${type}`;

    // =====================
    // STAFF CONFIG
    // =====================
    if (type === 'staff') {

        instructionsBox.innerHTML = `
            <ul class="list-disc ml-5 space-y-1">
                <li>Upload only <strong>staff devices</strong></li>
                <li><strong>Required:</strong> name, email, role, device_type</li>
                <li><strong>Status:</strong> active, faulty, or retired</li>
                <li><strong>Manufacture Date:</strong> YYYY-MM-DD (e.g. 2024-01-01)</li>
                <li>Use <strong>campus_name</strong> (e.g. Tatu City)</li>
                <li>serial_number must be unique</li>
            </ul>
        `;

        sampleTableBox.innerHTML = `
        <div class="bg-blue-50 px-4 py-2 text-sm font-medium text-blue-800">
            Staff CSV Sample
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-[12px] text-left">
                <thead class="bg-gray-50 text-gray-500 text-[11px] uppercase">
                    <tr>
                        <th>assigned_to_name</th>
                        <th>assigned_to_email</th>
                        <th>role</th>
                        <th>device_type</th>
                        <th>serial_number</th>
                        <th>status</th>
                        <th>manufacture_date</th>
                        <th>campus_name</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-3 py-2">John Doe</td>
                        <td class="px-3 py-2">john@email.com</td>
                        <td class="px-3 py-2">Teacher</td>
                        <td class="px-3 py-2">Laptop</td>
                        <td class="px-3 py-2">SN12345</td>
                        <td class="px-3 py-2">active</td> <!-- ✅ ADDED -->
                        <td class="px-3 py-2">2024-01-01</td> <!-- ✅ ADDED -->
                        <td class="px-3 py-2">Tatu City</td>
                    </tr>
                </tbody>
            </table>
        </div>
        `;
    }

    // =====================
    // STUDENT CONFIG
    // =====================
    if (type === 'student') {

        instructionsBox.innerHTML = `
            <ul class="list-disc ml-5 space-y-1">
                <li>Upload only <strong>student devices</strong></li>
                <li><strong>Required:</strong> device_type, brand, model</li>
                <li><strong>Status:</strong> active, faulty, or retired</li>
                <li><strong>Manufacture Date:</strong> YYYY-MM-DD (e.g. 2024-01-01)</li>
                <li>Use <strong>campus_name</strong> (e.g. Tatu City)</li>
                <li>serial_number must be unique</li>
            </ul>
        `;

        sampleTableBox.innerHTML = `
        <div class="bg-green-50 px-4 py-2 text-sm font-medium text-green-800">
            Student CSV Sample
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-[12px] text-left">
                <thead class="bg-gray-50 text-gray-500 text-[11px] uppercase">
                    <tr>
                        <th>device_type</th>
                        <th>brand</th>
                        <th>model</th>
                        <th>serial_number</th>
                        <th>status</th>
                        <th>manufacture_date</th>
                        <th>campus_name</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-3 py-2">Chromebook</td>
                        <td class="px-3 py-2">Lenovo</td>
                        <td class="px-3 py-2">100e</td>
                        <td class="px-3 py-2">SN67890</td>
                        <td class="px-3 py-2">active</td> <!-- ✅ ADDED -->
                        <td class="px-3 py-2">2024-01-01</td> <!-- ✅ ADDED -->
                        <td class="px-3 py-2">Boksburg</td>
                    </tr>
                </tbody>
            </table>
        </div>
        `;
    }
}

/*Keep the bulk section open*/
document.addEventListener("DOMContentLoaded", function () {

    const hasPreview = @json(session()->has('preview_data'));
    const selectedType = "{{ request('type') }}";

    if (hasPreview) {
        switchTab('bulk');
        document.getElementById('bulkContent').classList.remove('hidden');

        // 🔥 restore selected type (staff/student)
        if (selectedType) {
            selectBulkType(selectedType);
        }
    }

});

</script>

</x-app-layout>