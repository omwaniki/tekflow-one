<x-app-layout>
<div class="max-w-4xl mx-auto px-8 py-10">

    <h1 class="text-2xl font-semibold text-gray-800 mb-6">
        Appearance Settings
    </h1>

    <div class="bg-white p-6 rounded-xl shadow">

        <form method="POST" action="{{ route('settings.appearance.update') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-2">
                    Primary Color
                </label>

                <input type="color" name="primary_color"
                       value="{{ $primaryColor }}"
                       class="w-20 h-12 border rounded">
            </div>

            <button class="btn-primary px-4 py-2 rounded-lg">
                Save Changes
            </button>

        </form>

    </div>

</div>
</x-app-layout>