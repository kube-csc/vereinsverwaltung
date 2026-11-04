<div class="space-y-8 bg-white p-6">
    <header class="rounded-xl border-l-4 border-gray-600 bg-gray-300 px-5 py-4 text-gray-900">
        <p class="text-xs font-bold uppercase tracking-widest text-gray-700">Fördermittelverwaltung</p>
        <h3 class="mt-1 text-lg font-bold">Programme und Budgets</h3>
        <p class="mt-1 text-sm text-gray-800">Förderprogramme, Fördertöpfe und deren Antragszuordnungen verwalten.</p>
    </header>
    @if (session()->has('message'))
        <div class="text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded p-3">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded p-3">{{ session('error') }}</div>
    @endif

    @forelse($programs as $program)
        <section wire:key="programm-{{ $program->id }}" class="overflow-hidden rounded-xl border-2 border-indigo-200 bg-white shadow-sm">
            <header class="flex flex-wrap items-start justify-between gap-2 bg-gray-300 px-4 py-3 text-gray-900">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">
                        {{ $program->name }}
                        @unless($program->aktiv)
                            <span class="ml-2 text-xs font-medium bg-gray-200 text-gray-700 rounded-full px-2 py-0.5">inaktiv</span>
                        @endunless
                    </h3>
                    @if($program->beschreibung)
                        <p class="text-sm text-gray-700">{{ $program->beschreibung }}</p>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="newPot({{ $program->id }})" class="rounded-md bg-blue-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">Fördertopf anlegen</button>
                    <button wire:click="editProgram({{ $program->id }})" class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">Bearbeiten</button>
                    <button wire:click="deleteProgram({{ $program->id }})" wire:confirm="Förderprogramm wirklich löschen?" class="rounded-md bg-red-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2">Löschen</button>
                </div>
            </header>

            <div class="space-y-3 p-3">
                @forelse($program->pots->sortByDesc('jahr') as $pot)
                    <article wire:key="topf-{{ $pot->id }}" class="overflow-hidden rounded-lg border border-blue-200">
                        <header class="flex flex-wrap items-start justify-between gap-3 bg-blue-50 px-4 py-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-semibold text-gray-900">{{ $pot->name }}@if($pot->jahr) ({{ $pot->jahr }})@endif</h4>
                                    <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium {{ $pot->istOffen() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $pot->statusLabel() }}</span>
                                </div>
                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-600">
                                    <span>
                                        @if($pot->laufzeit === 'fortlaufend')
                                            Fortlaufend
                                        @else
                                            {{ $pot->beginn?->format('d.m.Y') }} – {{ $pot->ende?->format('d.m.Y') }}
                                        @endif
                                    </span>
                                    <span>Max. je Konto: {{ $pot->max_applications_per_user ?? 'Unbegrenzt' }}</span>
                                    <span>Max. je Verein: {{ $pot->max_applications_per_club ?? 'Unbegrenzt' }}</span>
                                </div>
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @forelse($pot->applicationTypes as $type)
                                        <span class="inline-block rounded bg-blue-100 px-2 py-0.5 text-xs text-blue-900">{{ $type->name }}</span>
                                    @empty
                                        <span class="text-xs text-gray-500">Keine aktive Antragsart zugeordnet</span>
                                    @endforelse
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <button wire:click="editPot({{ $pot->id }})" class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">Bearbeiten</button>
                                <button wire:click="toggleGeschlossen({{ $pot->id }})" @if(! $pot->geschlossen && $pot->endeNochNichtErreicht()) wire:confirm="Die Endzeit dieses Fördertopfs ({{ $pot->ende->format('d.m.Y') }}) ist noch nicht erreicht. Wirklich vorzeitig schließen? Neue Anträge sind dann nicht mehr möglich." @endif class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">{{ $pot->geschlossen ? 'Öffnen' : 'Schließen' }}</button>
                                @if(! $pot->isSettled() && $pot->geschlossen)
                                    <button wire:click="prepareSettlement({{ $pot->id }})" class="rounded-md bg-green-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2">Endabrechnung</button>
                                @endif
                                <button wire:click="deletePot({{ $pot->id }})" wire:confirm="Fördertopf wirklich löschen?" class="rounded-md bg-red-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2">Löschen</button>
                            </div>
                        </header>
                        <dl class="grid grid-cols-2 gap-3 px-4 py-3 text-sm sm:grid-cols-4">
                            <div>
                                <dt class="text-xs text-gray-500">Budget</dt>
                                <dd class="font-medium text-gray-900">{{ number_format((float) $pot->budget, 2, ',', '.') }} €</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">Gebunden (inkl. bewilligt)</dt>
                                <dd class="text-gray-900">{{ number_format($pot->gebunden(), 2, ',', '.') }} €</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">Bewilligt</dt>
                                <dd class="text-gray-900">{{ number_format($pot->bewilligt(), 2, ',', '.') }} €</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">Verfügbar</dt>
                                <dd class="font-semibold text-gray-900">{{ number_format($pot->verfuegbar(), 2, ',', '.') }} €</dd>
                            </div>
                        </dl>
                        @if($pot->isSettled())
                            <p class="border-t border-gray-200 bg-green-50 px-4 py-2 text-xs text-green-900">
                                Abgerechnet am {{ $pot->settled_at->format('d.m.Y H:i') }}: bewilligt {{ number_format((float) $pot->settlement_total, 2, ',', '.') }} €, Anteil {{ number_format((float) $pot->settlement_factor * 100, 2, ',', '.') }} %.
                            </p>
                            @can('antraege.verwalten')
                                <div class="border-t border-gray-200 bg-green-50 px-4 pb-3">
                                    <button wire:click="revokeSettlement({{ $pot->id }})" wire:confirm="Endabrechnung wirklich zurücknehmen? Die endgültigen Beträge werden gelöscht und sind für Antragsteller nicht mehr sichtbar." class="rounded-md border border-red-300 bg-white px-3 py-1.5 text-sm font-semibold text-red-700 hover:bg-red-50">Endabrechnung zurücknehmen</button>
                                </div>
                            @endcan
                            @php($payouts = $pot->openPayouts())
                            <div class="space-y-2 border-t border-gray-200 bg-white px-4 py-3 text-sm">
                                <h5 class="font-semibold text-gray-900">Überweisungen (SEPA)</h5>
                                <div>
                                    <label class="text-xs font-semibold text-gray-700">Auftraggeberkonto</label>
                                    <select wire:change="assignPotAccount({{ $pot->id }}, $event.target.value)" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                        <option value="" @selected(! $pot->bank_account_id)>{{ $payerAccounts->count() === 1 ? 'Automatisch: ' . $payerAccounts->first()->bezeichnung : 'Bitte Konto wählen' }}</option>
                                        @foreach($payerAccounts as $payerAccount)
                                            <option value="{{ $payerAccount->id }}" @selected($pot->bank_account_id === $payerAccount->id)>{{ $payerAccount->bezeichnung }} – {{ $payerAccount->maskedIban() }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error for="potAccount{{ $pot->id }}" class="mt-1" />
                                </div>
                                <p class="text-xs text-gray-600">
                                    Offen mit Bankverbindung: {{ $payouts['ready']->count() }}
                                    ({{ number_format($payouts['ready']->sum(fn ($a) => (float) $a->final_amount), 2, ',', '.') }} €).
                                    Ohne Bankverbindung: {{ $payouts['missing']->count() }}.
                                    Ergänzte oder geänderte Bankdaten werden beim nächsten Export automatisch berücksichtigt.
                                </p>
                                @foreach($payouts['missing'] as $missing)
                                    <p class="text-xs text-orange-800">#{{ $missing->id }} {{ trim($missing->user->vorname . ' ' . $missing->user->nachname) ?: $missing->user->name }}: Bankverbindung fehlt.</p>
                                @endforeach
                                @if($payouts['ready']->isNotEmpty())
                                    <button wire:click="downloadPayoutFile({{ $pot->id }})" class="rounded-md bg-blue-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-blue-800">Überweisungsdatei erzeugen</button>
                                @endif
                            </div>
                        @endif
                        @if($settlementPreview && $settlementPotId === $pot->id)
                            <div class="border-t border-gray-200 bg-gray-50 px-4 py-3 text-sm">
                                <h5 class="font-semibold text-gray-900">Vorschau der Endabrechnung</h5>
                                <p class="mt-1 text-xs text-gray-600">
                                    Topfvolumen {{ number_format($settlementPreview['budget_cent'] / 100, 2, ',', '.') }} €,
                                    bewilligt {{ number_format($settlementPreview['total_cent'] / 100, 2, ',', '.') }} €,
                                    Anteil {{ number_format($settlementPreview['factor'] * 100, 2, ',', '.') }} %.
                                </p>
                                <table class="mt-2 w-full text-left text-xs">
                                    <thead><tr class="text-gray-600"><th class="py-1">Antrag</th><th class="py-1">Bewilligt</th><th class="py-1">Endgültig</th></tr></thead>
                                    <tbody>
                                        @foreach($settlementPreview['rows'] as $row)
                                            <tr class="border-t border-gray-200">
                                                <td class="py-1">#{{ $row['application']->id }} {{ $row['application']->applicationType->name }} – {{ trim($row['application']->user->vorname . ' ' . $row['application']->user->nachname) ?: $row['application']->user->name }}</td>
                                                <td class="py-1">{{ number_format($row['approved_cent'] / 100, 2, ',', '.') }} €</td>
                                                <td class="py-1 font-semibold">{{ number_format($row['final_cent'] / 100, 2, ',', '.') }} €</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="mt-3 flex gap-2">
                                    <button wire:click="settlePot({{ $pot->id }})" wire:confirm="Endabrechnung verbindlich durchführen? Die endgültigen Beträge werden gespeichert und für Antragsteller sichtbar." class="rounded-md bg-green-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-green-800">Endabrechnung durchführen</button>
                                    <button wire:click="cancelSettlement" class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Abbrechen</button>
                                </div>
                            </div>
                        @endif
                    </article>
                @empty
                    <p class="px-4 py-3 text-center text-sm text-gray-500">Noch keine Fördertöpfe.</p>
                @endforelse
            </div>
        </section>
    @empty
        <p class="text-sm text-gray-500">Noch keine Förderprogramme angelegt.</p>
    @endforelse

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <form wire:submit.prevent="saveProgram" class="space-y-3 rounded-xl border-2 border-indigo-200 bg-indigo-50 p-5">
            <h4 class="border-b border-indigo-200 pb-3 font-bold text-indigo-900">{{ $programId ? 'Förderprogramm bearbeiten' : 'Neues Förderprogramm' }}</h4>
            <div>
                <x-label value="Name" />
                <x-input type="text" wire:model="programName" class="w-full" />
                <x-input-error for="programName" class="mt-1" />
            </div>
            <div>
                <x-label value="Beschreibung (optional)" />
                <textarea wire:model="programBeschreibung" rows="2" class="w-full border-gray-300 rounded-md shadow-sm text-sm"></textarea>
            </div>
            <label class="inline-flex items-center text-sm text-gray-700">
                <input type="checkbox" wire:model="programAktiv" class="h-5 w-5 rounded border-2 border-gray-600 text-blue-600 focus:ring-blue-500">
                <span class="ml-2">Programm ist aktiv</span>
            </label>
            <div class="flex items-center space-x-4">
                <x-button>{{ $programId ? 'Änderungen speichern' : 'Programm speichern' }}</x-button>
                @if($programId)
                    <button type="button" wire:click="cancelProgram" class="text-sm text-gray-700 underline">Abbrechen</button>
                @endif
            </div>
        </form>

        <form wire:submit.prevent="savePot" class="space-y-3 rounded-xl border-2 border-blue-200 bg-blue-50 p-5">
            <h4 class="border-b border-blue-200 pb-3 font-bold text-blue-900">{{ $potId ? 'Fördertopf bearbeiten' : 'Neuer Fördertopf' }}</h4>
            <div>
                <x-label value="Förderprogramm" />
                <select wire:model="potProgramId" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">– bitte wählen –</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}">{{ $program->name }}</option>
                    @endforeach
                </select>
                <x-input-error for="potProgramId" class="mt-1" />
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <x-label value="Name" />
                    <x-input type="text" wire:model="potName" class="w-full" />
                    <x-input-error for="potName" class="mt-1" />
                </div>
                <div>
                    <x-label value="Jahr" />
                    <x-input type="text" wire:model="potJahr" class="w-full" />
                    <x-input-error for="potJahr" class="mt-1" />
                </div>
            </div>
            <div>
                <x-label value="Laufzeit" />
                <select wire:model.live="potLaufzeit" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="befristet">Befristete Antragsrunde</option>
                    <option value="fortlaufend">Fortlaufend geöffnet (bis das Budget erschöpft ist)</option>
                </select>
            </div>
            @if($potLaufzeit === 'befristet')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-label value="Beginn" />
                        <x-input type="date" wire:model="potBeginn" class="w-full" />
                        <x-input-error for="potBeginn" class="mt-1" />
                    </div>
                    <div>
                        <x-label value="Frist (Ende)" />
                        <x-input type="date" wire:model="potEnde" class="w-full" />
                        <x-input-error for="potEnde" class="mt-1" />
                    </div>
                </div>
            @endif
            <div>
                <x-label value="Budget in Euro" />
                <x-input type="text" wire:model="potBudget" class="w-full" placeholder="z. B. 15.000,00" />
                <x-input-error for="potBudget" class="mt-1" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <x-label value="Max. Anträge je Antragstellerkonto (optional)" />
                    <x-input type="number" min="1" wire:model="potMaxApplicationsPerUser" class="w-full" placeholder="Unbegrenzt" />
                    <x-input-error for="potMaxApplicationsPerUser" class="mt-1" />
                </div>
                <div>
                    <x-label value="Max. Anträge je Verein (optional)" />
                    <x-input type="number" min="1" wire:model="potMaxApplicationsPerClub" class="w-full" placeholder="Unbegrenzt" />
                    <x-input-error for="potMaxApplicationsPerClub" class="mt-1" />
                </div>
            </div>
            <p class="text-xs text-gray-600">Beide Höchstzahlen sind optional und standardmäßig unbegrenzt: leer lassen bedeutet unbegrenzt. Eine Vereinsgrenze ist nur bei Antragsarten mit erforderlicher Vereinszuordnung möglich. Entwürfe zählen nicht; eingereichte Anträge zählen auch nach Ablehnung oder Rücknahme weiter.</p>
            <div>
                <x-label value="Auftraggeberkonto für Überweisungen (optional)" />
                <select wire:model="potBankAccountId" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Kein Konto</option>
                    @foreach($payerAccounts as $payerAccount)
                        <option value="{{ $payerAccount->id }}">{{ $payerAccount->bezeichnung }} – {{ $payerAccount->maskedIban() }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-600">Pro Fördertopf ist genau ein Konto möglich. Gibt es nur ein Vereinskonto, wird es automatisch genutzt.</p>
                <x-input-error for="potBankAccountId" class="mt-1" />
            </div>
            <div>
                <x-label value="Beschreibung (optional)" />
                <textarea wire:model="potBeschreibung" rows="2" class="w-full border-gray-300 rounded-md shadow-sm text-sm"></textarea>
            </div>
            <div>
                <x-label value="Zugeordnete Antragsarten" />
                <select wire:model="potApplicationTypeIds" multiple size="5" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    @foreach($applicationTypes as $applicationType)
                        <option value="{{ $applicationType->id }}">{{ $applicationType->name }}{{ $applicationType->aktiv ? '' : ' (inaktiv)' }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-600">Strg (Windows) bzw. Cmd (Mac) gedrückt halten, um mehrere Antragsarten auszuwählen. Nur offene, zugeordnete Fördertöpfe werden Antragstellenden angeboten.</p>
                <x-input-error for="potApplicationTypeIds" class="mt-1" />
                <x-input-error for="potApplicationTypeIds.*" class="mt-1" />
            </div>
            <div class="flex items-center space-x-4">
                <x-button>{{ $potId ? 'Änderungen speichern' : 'Fördertopf speichern' }}</x-button>
                @if($potId || $potProgramId)
                    <button type="button" wire:click="cancelPot" class="text-sm text-gray-700 underline">Abbrechen</button>
                @endif
            </div>
        </form>
    </div>

    <section class="space-y-4 rounded-xl border-2 border-gray-300 bg-gray-50 p-5">
        <h4 class="border-b border-gray-300 pb-3 font-bold text-gray-900">Auftraggeberkonten für Überweisungen</h4>
        @if($payerAccounts->isNotEmpty())
            <table class="min-w-full text-sm">
                <thead><tr class="text-left text-xs font-semibold uppercase text-gray-600"><th class="py-2 pr-4">Bezeichnung</th><th class="py-2 pr-4">Kontoinhaber</th><th class="py-2 pr-4">IBAN</th><th class="py-2 text-right">Aktion</th></tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($payerAccounts as $payerAccount)
                        <tr wire:key="konto-{{ $payerAccount->id }}">
                            <td class="py-2 pr-4 font-medium text-gray-900">{{ $payerAccount->bezeichnung }}</td>
                            <td class="py-2 pr-4">{{ $payerAccount->kontoinhaber }}</td>
                            <td class="py-2 pr-4">{{ $payerAccount->maskedIban() }}</td>
                            <td class="py-2 text-right">
                                <button type="button" wire:click="editAccount({{ $payerAccount->id }})" class="text-blue-700 hover:underline">Bearbeiten</button>
                                <button type="button" wire:click="deleteAccount({{ $payerAccount->id }})" wire:confirm="Konto wirklich entfernen?" class="ml-3 text-red-700 hover:underline">Entfernen</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-sm text-gray-600">Noch keine Auftraggeberkonten angelegt.</p>
        @endif
        <form wire:submit.prevent="saveAccount" class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <x-label value="Bezeichnung" />
                <x-input type="text" wire:model="accountBezeichnung" class="w-full" placeholder="z. B. Jugendförderung Sparkasse" />
                <x-input-error for="accountBezeichnung" class="mt-1" />
            </div>
            <div>
                <x-label value="Kontoinhaber" />
                <x-input type="text" wire:model="accountKontoinhaber" class="w-full" />
                <x-input-error for="accountKontoinhaber" class="mt-1" />
            </div>
            <div>
                <x-label value="IBAN" />
                <x-input type="text" wire:model="accountIban" class="w-full" autocomplete="off" />
                <x-input-error for="accountIban" class="mt-1" />
            </div>
            <div>
                <x-label value="BIC (optional)" />
                <x-input type="text" wire:model="accountBic" class="w-full" autocomplete="off" />
                <x-input-error for="accountBic" class="mt-1" />
            </div>
            <div>
                <x-label value="Bank (optional)" />
                <x-input type="text" wire:model="accountBankName" class="w-full" />
            </div>
            <div class="flex items-end space-x-4">
                <x-button>{{ $accountId ? 'Änderungen speichern' : 'Konto speichern' }}</x-button>
                @if($accountId)
                    <button type="button" wire:click="cancelAccount" class="text-sm text-gray-700 underline">Abbrechen</button>
                @endif
            </div>
        </form>
        <p class="text-xs text-gray-600">Die Angaben werden verschlüsselt gespeichert.</p>
    </section>
</div>
