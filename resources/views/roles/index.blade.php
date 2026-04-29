<x-app-layout>

<div class="max-w-7xl mx-auto px-8 pt-10 pb-12">

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-gray-800">Roles & Permissions</h1>
        <p class="text-gray-500 mt-1">Manage system roles and access control</p>
    </div>

    <!-- Create Role -->
    <div class="bg-white p-6 rounded-xl border mb-6">
        <form method="POST" action="{{ route('roles.store') }}">
            @csrf

            <div class="flex flex-col sm:flex-row items-center gap-4">

                <input type="text" name="name"
                    placeholder="New role name"
                    class="w-full flex-1 border-gray-300 rounded-xl focus:ring-gray-800 focus:border-gray-800">

                <button type="submit"
                    class="bg-gray-800 text-white px-5 py-2 rounded-xl whitespace-nowrap hover:bg-gray-900 transition">
                    Create Role
                </button>

            </div>
        </form>
    </div>

    <!-- Roles List -->
    @foreach($roles as $role)
    <div class="bg-white p-6 rounded-xl border mb-6">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">
                {{ ucfirst($role->name) }}
            </h2>

            <form method="POST" action="{{ route('roles.destroy', $role->id) }}">
                @csrf
                @method('DELETE')
                <button class="text-red-500 text-sm hover:text-red-600">
                    Delete
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('roles.update', $role->id) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">

                @foreach($permissions as $permission)
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox"
                            name="permissions[]"
                            value="{{ $permission->name }}"
                            class="rounded border-gray-300 text-gray-800 focus:ring-gray-800"
                            {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>

                        {{ $permission->name }}
                    </label>
                @endforeach

            </div>

            <div class="mt-4">
                <button type="submit"
                    class="bg-gray-800 text-white px-4 py-2 rounded-xl text-sm hover:bg-gray-900 transition">
                    Save Permissions
                </button>
            </div>

        </form>
    </div>
    @endforeach

</div>

</x-app-layout>