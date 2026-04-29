<x-app-layout>

<div class="max-w-7xl mx-auto px-6 md:px-8 pt-8 pb-12">
    <div class="mb-6 text-sm text-gray-500 flex items-center gap-2">
        <a href="{{ route('audits.index') }}">Audits</a>
        <span>/</span>
        <span>{{ $audit->name }}</span>
        <span>/</span>
        <span class="text-gray-800 font-medium">Verify</span>
    </div>

    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-3xl font-semibold text-gray-800">
            {{ $audit->name }} — Verification
        </h1>

        @if($selectedCampus)
        <div class="mt-2">
            <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-700 font-medium shadow-sm">
                {{ $selectedCampus->name }}
            </span>
        </div>
        @endif

        <p class="text-gray-500 mt-1">
            Progress: {{ $completed }} / {{ $total }}
        </p>
    </div>

    @if(isset($userCampuses) && count($userCampuses) > 1)
    <form method="GET" class="mb-4 flex items-center gap-2">

        <span class="text-gray-400 text-xs uppercase tracking-wide">
            Campus
        </span>

        <select name="campus_id"
            onchange="this.form.submit()"
            class="border border-gray-300 rounded-lg px-3 py-2 bg-white shadow-sm">

            <option value="">Select Campus</option>

            @foreach($userCampuses as $campus)
                <option value="{{ $campus->id }}"
                    {{ request('campus_id') == $campus->id ? 'selected' : '' }}>
                    {{ $campus->name }}
                </option>
            @endforeach

        </select>

    </form>
    @endif

    @if(isset($userCampuses) && count($userCampuses) > 1 && !request('campus_id'))
        <div class="p-4 bg-yellow-50 text-yellow-700 rounded-lg mb-4">
            Please select a campus to begin verification.
        </div>
    @endif

    <!-- 🔥 STICKY CONTROL BAR -->
    <div class="sticky top-0 z-10 bg-white border rounded-xl p-4 mb-6 shadow-sm">

        <div class="flex flex-wrap items-center justify-between gap-4">

            <!-- LEFT: ACTIONS -->
            <div class="flex items-center gap-3 flex-wrap">

                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" id="selectAll">
                    Select All
                </label>

                <button onclick="bulkUpdate(1)"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                    Found
                </button>

                <button onclick="bulkUpdate(0)"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                    Missing
                </button>

                <button onclick="bulkReset()"
                    class="bg-gray-200 px-4 py-2 rounded-lg text-sm">
                    Reset
                </button>

            </div>

            <!-- RIGHT: SEARCH + FILTER -->
            <div class="flex items-center gap-3 w-full md:w-auto">

                <input type="text"
                       id="searchInput"
                       placeholder="Search..."
                       class="border rounded-lg px-4 py-2 text-sm w-full md:w-64">

                <select id="statusFilter"
                        class="border rounded-lg px-3 py-2 pr-10 text-sm bg-white appearance-none">
                    <option value="all">All</option>
                    <option value="found">Found</option>
                    <option value="missing">Missing</option>
                    <option value="not_verified">Not Verified</option>
                </select>

            </div>

        </div>

        <!-- 🔥 RESULT COUNT -->
        <div class="mt-3 text-xs text-gray-500">
            Showing <span id="visibleCount">{{ $records->count() }}</span> of {{ $records->count() }} assets
        </div>

    </div>

    <!-- ASSET LIST -->
    <div id="assetList" class="space-y-3">

        @foreach($records as $record)
        <div class="asset-row bg-white border rounded-xl p-4 flex items-center justify-between transition"
             data-name="{{ strtolower(($record->asset->brand ?? '') . ' ' . ($record->asset->model ?? '')) }}"
             data-serial="{{ strtolower($record->asset->serial_number) }}"
             data-status="{{ is_null($record->found) ? 'not_verified' : ($record->found ? 'found' : 'missing') }}">

            <!-- LEFT -->
            <div class="flex items-center gap-4">

                <input type="checkbox"
                       class="record-checkbox"
                       value="{{ $record->id }}">

                <div>
                    <p class="font-medium text-gray-800 text-sm">
                        {{ 
                            $record->asset->name 
                            ?? $record->asset->device_type 
                            ?? trim(($record->asset->brand ?? '') . ' ' . ($record->asset->model ?? '')) 
                            ?: 'Unknown Device'
                        }}
                    </p>

                    <p class="text-xs text-gray-500">
                        {{ $record->asset->serial_number }}
                    </p>
                </div>

            </div>

            <!-- RIGHT -->
            <div class="flex items-center gap-2">

                <button onclick="updateRecord({{ $record->id }}, 1, this)"
                    class="px-3 py-1 rounded-lg border text-xs
                    {{ $record->found === 1 ? 'bg-green-600 text-white' : '' }}">
                    Found
                </button>

                <button onclick="updateRecord({{ $record->id }}, 0, this)"
                    class="px-3 py-1 rounded-lg border text-xs
                    {{ $record->found === 0 ? 'bg-red-600 text-white' : '' }}">
                    Missing
                </button>

            </div>

        </div>
        @endforeach

    </div>

</div>

<!-- 🔥 SCRIPT -->
<script>

// 🔊 SOUND
const beep = new Audio("https://www.soundjay.com/buttons/sounds/button-16.mp3");

// SELECT ALL
document.getElementById('selectAll').addEventListener('change', function(){
    document.querySelectorAll('.record-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
});

// GET IDS
function getSelectedIds(){
    return Array.from(document.querySelectorAll('.record-checkbox:checked'))
        .map(cb => cb.value);
}

// BULK
function bulkUpdate(found){

    const ids = getSelectedIds();
    if(!ids.length) return alert('Select assets');

    fetch(`/audit-records/bulk-update`, {
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body:JSON.stringify({ids, found})
    }).then(()=>location.reload());
}

function bulkReset(){
    bulkUpdate(null);
}

// 🔍 FILTER + COUNT
function filterRecords(){

    const search = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;

    let visible = 0;

    document.querySelectorAll('.asset-row').forEach(row => {

        const name = row.dataset.name;
        const serial = row.dataset.serial;
        const s = row.dataset.status;

        const show =
            (name.includes(search) || serial.includes(search)) &&
            (status === 'all' || status === s);

        row.style.display = show ? 'flex' : 'none';

        if(show) visible++;
    });

    document.getElementById('visibleCount').innerText = visible;
}

document.getElementById('searchInput').addEventListener('input', filterRecords);
document.getElementById('statusFilter').addEventListener('change', filterRecords);

// ⚡ SMART FLOW
function updateRecord(id, found, btn){

    beep.play();

    fetch(`/audit-records/${id}`, {
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body:JSON.stringify({found})
    }).then(()=>{

        const row = btn.closest('.asset-row');
        row.style.opacity = '0.5';

        let next = row.nextElementSibling;

        while(next && next.style.display === 'none'){
            next = next.nextElementSibling;
        }

        if(next){
            next.scrollIntoView({behavior:'smooth', block:'center'});

            const cb = next.querySelector('.record-checkbox');
            if(cb) cb.checked = true;
        }
    });
}

// 📱 SWIPE
document.querySelectorAll('.asset-row').forEach(row => {

    let startX = 0;

    row.addEventListener('touchstart', e => {
        startX = e.touches[0].clientX;
    });

    row.addEventListener('touchend', e => {

        const diff = e.changedTouches[0].clientX - startX;
        const id = row.querySelector('.record-checkbox').value;

        if(diff > 80) updateRecord(id, 1, row);
        if(diff < -80) updateRecord(id, 0, row);

    });

});

</script>

</x-app-layout>