<x-app-layout>

<div class="max-w-3xl mx-auto px-8 pt-10">
    <div class="mb-6 text-sm text-gray-500 flex items-center gap-2">
        <a href="{{ route('audits.index') }}">Audits</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">Create Audit</span>
    </div>

    <h1 class="text-2xl font-semibold mb-6">Create Audit</h1>

    <form method="POST" action="{{ route('audits.store') }}" class="space-y-4">
        @csrf

        <input type="text" name="name"
               placeholder="Audit Name (e.g. Term 2 Audit)"
               class="w-full border rounded-lg px-4 py-2"
               required>

        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Create Audit
        </button>
    </form>

</div>

</x-app-layout>