<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Regatta Verwaltung') }} {{ Session::get('regattaSelectUeberschrift') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="mt-8 text-2xl">
                        FAQ
                    </div>

                    <div class="mt-6 text-gray-500">
                        In diesem Bereich werden die FAQs bearbeitet.
                    </div>
                </div>

                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">FAQ Verwaltung</div>
                        </div>

                        <div class="ml-12">
                            <div class="mt-2 text-sm text-gray-500">

                                <div class="my-4 flex">
                                    <a href="{{ route('faq.create') }}" title="Neu">
                                        <box-icon name='plus'></box-icon>
                                    </a>
                                </div>

                                <div style="text-align: left">
                                    <div>
                                        @if (session()->has('success'))
                                            <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                                {!! session('success') !!}
                                            </div>
                                        @endif
                                    </div>

                                    @php
                                        // Kategorien nach category_sort_order sortieren (Fallback: Name)
                                        $sortedFaqs = $faqs->sortBy(function ($items, $category) {
                                            $first = $items->first();
                                            return [
                                                (int) ($first->category_sort_order ?? 0),
                                                (string) $category,
                                            ];
                                        });

                                        $categories = $sortedFaqs->keys()->values()->all();
                                    @endphp

                                    @forelse($sortedFaqs as $category => $items)
                                        @php
                                            $catIndex = array_search($category, $categories, true);
                                            $categoryEncoded = rawurlencode($category);
                                            $categorySortOrder = (int) ($items->first()->category_sort_order ?? 0);
                                        @endphp

                                        <div class="rounded border shadow p-3 my-2 bg-blue-200">
                                            <div class="justify-between my-2">
                                                <div class="flex items-center justify-between">
                                                    <p class="font-bold text-lg">
                                                        {{ $category }}
                                                        <span class="ml-2 text-xs text-gray-500 font-semibold">(Sort: {{ $categorySortOrder }})</span>
                                                    </p>

                                                    <div class="flex flex-col">
                                                        <a class="text-blue-700 {{ $catIndex === 0 ? 'pointer-events-none opacity-40' : '' }}"
                                                           href="{{ route('faq.category.up', $categoryEncoded) }}" title="Kategorie nach oben">▲</a>
                                                        <a class="text-blue-700 {{ $catIndex === (count($categories) - 1) ? 'pointer-events-none opacity-40' : '' }}"
                                                           href="{{ route('faq.category.down', $categoryEncoded) }}" title="Kategorie nach unten">▼</a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-2 overflow-x-auto">
                                                <table class="min-w-full text-sm">
                                                    <thead>
                                                    <tr class="text-left border-b">
                                                        <th class="py-2 pr-4">Sort</th>
                                                        <th class="py-2 pr-4">Frage</th>
                                                        <th class="py-2 pr-4">Aktiv</th>
                                                        <th class="py-2 pr-4">Aktionen</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($items as $index => $faq)
                                                        <tr class="border-b">
                                                            <td class="py-2 pr-4 whitespace-nowrap">
                                                                <div class="flex items-center gap-2">
                                                                    <span class="text-gray-500">{{ $faq->sort_order }}</span>

                                                                    <div class="flex flex-col">
                                                                        <a class="text-blue-700 {{ $index === 0 ? 'pointer-events-none opacity-40' : '' }}"
                                                                           href="{{ route('faq.up', $faq->id) }}" title="Nach oben">▲</a>
                                                                        <a class="text-blue-700 {{ $index === (count($items) - 1) ? 'pointer-events-none opacity-40' : '' }}"
                                                                           href="{{ route('faq.down', $faq->id) }}" title="Nach unten">▼</a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="py-2 pr-4">
                                                                <div class="font-semibold">{{ $faq->question }}</div>
                                                            </td>
                                                            <td class="py-2 pr-4 whitespace-nowrap">
                                                                @if($faq['is_active']==1)
                                                                    <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('FAQ/inaktiv/'.$faq->id) }}" title="Aktiv">
                                                                        <box-icon name='show'></box-icon>
                                                                    </a>
                                                                @endif
                                                                @if($faq['is_active']==0)
                                                                    <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('FAQ/aktiv/'.$faq->id) }}" title="Inaktiv">
                                                                        <box-icon name='hide'></box-icon>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                            <td class="py-2 pr-4 whitespace-nowrap">
                                                                <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ route('faq.edit', $faq->id) }}" title="Bearbeiten">
                                                                    <box-icon name='edit' type='solid'></box-icon>
                                                                </a>

                                                                <form class="inline" action="{{ route('faq.destroy', $faq->id) }}" method="post"
                                                                      onsubmit="return confirm('FAQ wirklich löschen?\n\nKategorie: {{ addslashes($category) }}\nFrage: {{ addslashes($faq->question) }}');">
                                                                    @csrf
                                                                    <button class="ml-2 btn btn-sm btn-outline-primary" type="submit" title="Löschen">
                                                                        <box-icon name='trash' type='solid'></box-icon>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="mt-6 text-gray-500">Noch keine FAQs vorhanden.</div>
                                    @endforelse

                                    <br>
                                    <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Regattamenu"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-200 md:border-t-0 md:border-l">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
