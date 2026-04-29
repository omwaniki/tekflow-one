<x-app-layout>

<div class="max-w-3xl mx-auto px-8 pt-10 pb-12">

    <div class="mb-8">

        <h1 class="text-3xl font-semibold text-gray-800">
            Edit Region
        </h1>

        <p class="text-gray-500 mt-1">
            Update the region information.
        </p>

    </div>

    <div class="bg-white shadow-sm rounded-xl border p-8">

        <form method="POST" action="{{ route('regions.update',$region->id) }}">

            @csrf
            @method('PUT')

            <div class="mb-6">

                <x-input-label for="name" :value="'Region Name'" />

                <x-text-input
                    id="name"
                    name="name"
                    type="text"
                    class="mt-2 block w-full"
                    :value="old('name',$region->name)"
                    required
                />

                <x-input-error :messages="$errors->get('name')" class="mt-2" />

            </div>

            <div class="flex justify-end gap-4">

                <a href="{{ route('regions.index') }}">
                    <x-secondary-button>
                        Cancel
                    </x-secondary-button>
                </a>

                <x-primary-button>
                    Update Region
                </x-primary-button>

            </div>

        </form>

    </div>

</div>

</x-app-layout>