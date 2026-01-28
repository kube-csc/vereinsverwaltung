<?php

namespace App\Http\Controllers;

use App\Http\Requests\FaqStoreRequest;
use App\Http\Requests\FaqUpdateRequest;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $faqs = Faq::query()->ordered()->get()->groupBy('category');

        return view('regattaManagement.faq.index', compact('faqs'));
    }

    public function create(): View
    {
        $categories = Faq::query()
            ->select('category', 'category_sort_order')
            ->distinct()
            ->orderBy('category_sort_order')
            ->orderBy('category')
            ->pluck('category');

        return view('regattaManagement.faq.create', compact('categories'));
    }

    public function store(FaqStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $data['is_active'] ?? true;

        DB::transaction(function () use (&$data) {
            // category_sort_order wird pro Kategorie identisch gespeichert
            $catOrder = Faq::query()
                ->where('category', $data['category'])
                ->lockForUpdate()
                ->value('category_sort_order');

            // Neue Kategorie? -> ans Ende der Kategorien hängen
            if ($catOrder === null) {
                $maxCat = Faq::query()->lockForUpdate()->max('category_sort_order');
                $catOrder = ($maxCat ?? 0) + 10;
            }

            $data['category_sort_order'] = (int) $catOrder;

            // sort_order wird beim Anlegen automatisch ermittelt (pro Kategorie)
            $max = Faq::query()
                ->where('category', $data['category'])
                ->lockForUpdate()
                ->max('sort_order');

            $data['sort_order'] = ($max ?? 0) + 10;

            Faq::query()->create($data);

            $this->normalizeCategorySortOrder();
            $this->normalizeSortOrder($data['category']);
        });

        return redirect()->route('faq.index')->with('success', 'FAQ wurde angelegt.');
    }

    public function edit(int $faq_id): View
    {
        $faq = Faq::query()->findOrFail($faq_id);

        $categories = Faq::query()
            ->select('category', 'category_sort_order')
            ->distinct()
            ->orderBy('category_sort_order')
            ->orderBy('category')
            ->pluck('category');

        return view('regattaManagement.faq.edit', compact('faq', 'categories'));
    }

    public function update(FaqUpdateRequest $request, int $faq_id): RedirectResponse
    {
        $faq = Faq::query()->findOrFail($faq_id);
        $oldCategory = $faq->category;

        $data = $request->validated();
        $data['is_active'] = $data['is_active'] ?? false;

        DB::transaction(function () use ($faq, $oldCategory, &$data) {
            // Kategorie-Wechsel: neue sort_order ans Ende der neuen Kategorie
            if (($data['category'] ?? $faq->category) !== $oldCategory) {
                // category_sort_order der Ziel-Kategorie übernehmen (oder neue Kategorie ans Ende)
                $catOrder = Faq::query()
                    ->where('category', $data['category'])
                    ->lockForUpdate()
                    ->value('category_sort_order');

                if ($catOrder === null) {
                    $maxCat = Faq::query()->lockForUpdate()->max('category_sort_order');
                    $catOrder = ($maxCat ?? 0) + 10;
                }

                $data['category_sort_order'] = (int) $catOrder;

                $max = Faq::query()
                    ->where('category', $data['category'])
                    ->lockForUpdate()
                    ->max('sort_order');

                $data['sort_order'] = ($max ?? 0) + 10;
            }

            $faq->update($data);

            $this->normalizeCategorySortOrder();
            $this->normalizeSortOrder($oldCategory);
            $this->normalizeSortOrder($faq->category);
        });

        return redirect()->route('faq.index')->with('success', 'FAQ wurde gespeichert.');
    }

    public function destroy(int $faq_id): RedirectResponse
    {
        $faq = Faq::query()->findOrFail($faq_id);
        $category = $faq->category;

        $faq->delete();

        $this->normalizeSortOrder($category);
        $this->normalizeCategorySortOrder();

        return redirect()->route('faq.index')->with('success', 'FAQ wurde gelöscht.');
    }

    public function up(int $faq_id): RedirectResponse
    {
        $faq = Faq::query()->findOrFail($faq_id);

        DB::transaction(function () use ($faq) {
            $current = Faq::query()->whereKey($faq->id)->lockForUpdate()->first();

            $prev = Faq::query()
                ->where('category', $current->category)
                ->where(function ($q) use ($current) {
                    $q->where('sort_order', '<', $current->sort_order)
                      ->orWhere(function ($q2) use ($current) {
                          $q2->where('sort_order', '=', $current->sort_order)
                             ->where('id', '<', $current->id);
                      });
                })
                ->orderBy('sort_order', 'desc')
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            if (!$prev) {
                return;
            }

            $tmp = $current->sort_order;
            $current->update(['sort_order' => $prev->sort_order]);
            $prev->update(['sort_order' => $tmp]);
        });

        $this->normalizeSortOrder($faq->category);

        return redirect()->back()->with('success', 'FAQ wurde eine Position nach oben verschoben.');
    }

    public function down(int $faq_id): RedirectResponse
    {
        $faq = Faq::query()->findOrFail($faq_id);

        DB::transaction(function () use ($faq) {
            $current = Faq::query()->whereKey($faq->id)->lockForUpdate()->first();

            $next = Faq::query()
                ->where('category', $current->category)
                ->where(function ($q) use ($current) {
                    $q->where('sort_order', '>', $current->sort_order)
                      ->orWhere(function ($q2) use ($current) {
                          $q2->where('sort_order', '=', $current->sort_order)
                             ->where('id', '>', $current->id);
                      });
                })
                ->orderBy('sort_order')
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (!$next) {
                return;
            }

            $tmp = $current->sort_order;
            $current->update(['sort_order' => $next->sort_order]);
            $next->update(['sort_order' => $tmp]);
        });

        $this->normalizeSortOrder($faq->category);

        return redirect()->back()->with('success', 'FAQ wurde eine Position nach unten verschoben.');
    }

    public function categoryUp(string $categoryEncoded): RedirectResponse
    {
        $category = rawurldecode($categoryEncoded);

        // sicherstellen, dass alle Kategorien eine Sortierung haben
        $this->normalizeCategorySortOrder();

        DB::transaction(function () use ($category) {
            // aktuelle Kategorie sperren
            $current = Faq::query()
                ->where('category', $category)
                ->lockForUpdate()
                ->first(['id', 'category', 'category_sort_order']);

            if (!$current) {
                return;
            }

            // vorherige Kategorie anhand category_sort_order bestimmen
            $prev = Faq::query()
                ->select('category', 'category_sort_order')
                ->distinct()
                ->where(function ($q) use ($current) {
                    $q->where('category_sort_order', '<', $current->category_sort_order)
                        ->orWhere(function ($q2) use ($current) {
                            $q2->where('category_sort_order', '=', $current->category_sort_order)
                                ->where('category', '<', $current->category);
                        });
                })
                ->orderBy('category_sort_order', 'desc')
                ->orderBy('category', 'desc')
                ->lockForUpdate()
                ->first();

            if (!$prev) {
                return;
            }

            $tmp = $current->category_sort_order;

            Faq::query()->where('category', $current->category)->update(['category_sort_order' => $prev->category_sort_order]);
            Faq::query()->where('category', $prev->category)->update(['category_sort_order' => $tmp]);
        });

        $this->normalizeCategorySortOrder();

        return redirect()->back()->with('success', 'Kategorie wurde eine Position nach oben verschoben.');
    }

    public function categoryDown(string $categoryEncoded): RedirectResponse
    {
        $category = rawurldecode($categoryEncoded);

        // sicherstellen, dass alle Kategorien eine Sortierung haben
        $this->normalizeCategorySortOrder();

        DB::transaction(function () use ($category) {
            $current = Faq::query()
                ->where('category', $category)
                ->lockForUpdate()
                ->first(['id', 'category', 'category_sort_order']);

            if (!$current) {
                return;
            }

            $next = Faq::query()
                ->select('category', 'category_sort_order')
                ->distinct()
                ->where(function ($q) use ($current) {
                    $q->where('category_sort_order', '>', $current->category_sort_order)
                        ->orWhere(function ($q2) use ($current) {
                            $q2->where('category_sort_order', '=', $current->category_sort_order)
                                ->where('category', '>', $current->category);
                        });
                })
                ->orderBy('category_sort_order')
                ->orderBy('category')
                ->lockForUpdate()
                ->first();

            if (!$next) {
                return;
            }

            $tmp = $current->category_sort_order;

            Faq::query()->where('category', $current->category)->update(['category_sort_order' => $next->category_sort_order]);
            Faq::query()->where('category', $next->category)->update(['category_sort_order' => $tmp]);
        });

        $this->normalizeCategorySortOrder();

        return redirect()->back()->with('success', 'Kategorie wurde eine Position nach unten verschoben.');
    }

    private function normalizeSortOrder(string $category): void
    {
        DB::transaction(function () use ($category) {
            $rows = Faq::query()
                ->where('category', $category)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->lockForUpdate()
                ->get(['id']);

            $pos = 10;
            foreach ($rows as $row) {
                Faq::query()->whereKey($row->id)->update(['sort_order' => $pos]);
                $pos += 10;
            }
        });
    }

    private function normalizeCategorySortOrder(): void
    {
        DB::transaction(function () {
            // Backfill: falls noch 0/NULL Werte existieren, initial alphabetisch einsortieren
            Faq::query()
                ->where('category_sort_order', 0)
                ->update(['category_sort_order' => 10]);

            $rows = Faq::query()
                ->select('category')
                ->distinct()
                ->orderBy('category_sort_order')
                ->orderBy('category')
                ->lockForUpdate()
                ->get();

            $pos = 10;
            foreach ($rows as $row) {
                Faq::query()->where('category', $row->category)->update(['category_sort_order' => $pos]);
                $pos += 10;
            }
        });
    }

    public function aktiv(int $faq_id): RedirectResponse
    {
        $faq = Faq::query()->findOrFail($faq_id);
        $faq->update(['is_active' => true]);

        return redirect()->back()->with('success', 'FAQ wurde aktiviert.');
    }

    public function inaktiv(int $faq_id): RedirectResponse
    {
        $faq = Faq::query()->findOrFail($faq_id);
        $faq->update(['is_active' => false]);

        return redirect()->back()->with('success', 'FAQ wurde deaktiviert.');
    }
}
