<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Trainertyp bearbeiten</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="mt-2 text-2xl">{{ $type->trainerfunktion }}</div>
                            <div class="mt-2 text-gray-500">Ändere die Definition und die Default-Werte für neue Zuordnungen.</div>
                        </div>
                        <div class="flex gap-2">
                            <a class="p-2 bg-blue-500 rounded shadow text-white" href="{{ route('admin.trainertyp.index') }}">Zur Liste</a>
                            <a class="p-2 bg-blue-500 rounded shadow text-white" href="/Adminmenu">Adminmenu</a>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="mt-4 p-3 rounded bg-green-100 text-green-800">{!! session('success') !!}</div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-4 p-3 rounded bg-red-100 text-red-800">
                            <ul class="list-disc ml-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('admin.trainertyp.update', $type->id) }}" class="space-y-4">
                        @include('admin.trainertyp._form')

                        <button type="submit" class="p-2 bg-blue-500 rounded shadow text-white">Speichern</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

