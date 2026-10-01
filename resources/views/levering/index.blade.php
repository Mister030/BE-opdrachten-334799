<x-app-layout>
    @unless ($isVoorradig)
        {{-- Scenario_02: na 4 seconden terug naar Overzicht Magazijn Jamin. --}}
        @push('head')
            <meta http-equiv="refresh" content="4;url={{ route('magazijn.index') }}">
        @endpush
    @endunless

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Levering Informatie') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <dl class="mb-6 space-y-1">
                        <div><span class="font-semibold">Naam leverancier:</span> {{ $leverancier?->Naam }}</div>
                        <div><span class="font-semibold">Contactpersoon leverancier:</span> {{ $leverancier?->ContactPersoon }}</div>
                        <div><span class="font-semibold">Leveranciernummer:</span> {{ $leverancier?->LeverancierNummer }}</div>
                        <div><span class="font-semibold">Mobiel:</span> {{ $leverancier?->Mobiel }}</div>
                    </dl>

                    <table class="min-w-full border border-gray-300 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left">Naam Product</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Datum laatste levering</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Aantal</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Eerstvolgende levering</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($isVoorradig)
                                @foreach ($leveringen as $levering)
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2">{{ $levering->Naam }}</td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            {{ \Carbon\Carbon::parse($levering->DatumLevering)->format('d-m-Y') }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $levering->Aantal }}</td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            {{ $levering->DatumEerstVolgendeLevering
                                                ? \Carbon\Carbon::parse($levering->DatumEerstVolgendeLevering)->format('d-m-Y')
                                                : '' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2" colspan="4">
                                        {{-- Tekst en datum letterlijk overgenomen uit scenario_02 van user story 01. --}}
                                        Er is van dit product op dit moment geen voorraad aanwezig, de verwachte eerstvolgende levering is: 30-04-2023
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    @unless ($isVoorradig)
                        <p class="mt-4 text-sm text-gray-600">
                            U wordt over 4 seconden teruggestuurd naar Overzicht Magazijn Jamin.
                        </p>
                    @endunless

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
