<div class="relative w-full mx-auto space-y-4 p-5">

    {{-- Navigation Tabs --}}
    <div class="flex gap-2 mb-4">
        <button wire:click="setView('oneway')"
            class="px-4 py-2 rounded-full {{ $view === 'oneway' ? 'bg-blue-700 text-white' : 'bg-gray-200' }}">One-way</button>
        <button wire:click="setView('roundtrip')"
            class="px-4 py-2 rounded-full {{ $view === 'roundtrip' ? 'bg-blue-700 text-white' : 'bg-gray-200' }}">Round-trip</button>
        <button wire:click="setView('multicity')"
            class="px-4 py-2 rounded-full {{ $view === 'multicity' ? 'bg-blue-700 text-white' : 'bg-gray-200' }}">Multi-city</button>
    </div>

    {{-- Flight Segments --}}
    @foreach ($segments as $index => $segment)
        <div class="relative grid grid-cols-1 md:grid-cols-4 gap-4 p-4 border rounded-xl shadow-sm bg-white"
            wire:key="segment-{{ $index }}">

            <div class="col-span-1 md:col-span-4 relative">
                {{-- Origin --}}
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="segments.{{ $index }}.origin"
                        wire:focus="showDropdown({{ $index }}, 'origin')" placeholder="Origin Airport"
                        class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    @if (isset($showDropdowns[$index]['origin']) && $showDropdowns[$index]['origin'])
                        <div wire:click.away="hideDropdown({{ $index }}, 'origin')"
                            class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <ul>
                                @foreach ($this->getFilteredAirports($index, 'origin') as $code => $name)
                                    <li wire:click="selectAirport('{{ $code }}','{{ $name }}',{{ $index }},'origin')"
                                        class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
                                        <span class="font-semibold">{{ $code }}</span> - {{ $name }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                {{-- Swap --}}
                <div class="flex items-center justify-center">
                    <button type="button" wire:click="swapLocations({{ $index }})"
                        class="p-2 rounded-full bg-gray-100 border border-gray-300 hover:bg-gray-200">🔄</button>
                </div>

                {{-- Destination --}}
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="segments.{{ $index }}.destination"
                        wire:focus="showDropdown({{ $index }}, 'destination')" placeholder="Destination Airport"
                        class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    @if (isset($showDropdowns[$index]['destination']) && $showDropdowns[$index]['destination'])
                        <div wire:click.away="hideDropdown({{ $index }}, 'destination')"
                            class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <ul>
                                @foreach ($this->getFilteredAirports($index, 'destination') as $code => $name)
                                    <li wire:click="selectAirport('{{ $code }}','{{ $name }}',{{ $index }},'destination')"
                                        class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
                                        <span class="font-semibold">{{ $code }}</span> - {{ $name }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                {{-- Date --}}
                <div>
                    <input type="text" wire:model="segments.{{ $index }}.date" placeholder="Departure Date"
                        class="flatpickr-input w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>


                {{-- Return Date (for roundtrip) --}}
                @if ($view === 'roundtrip')
                    <div class="relative">
                        <input type="text" wire:model="returnDate" placeholder="Return Date"
                            class="flatpickr-input w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                @endif

                {{-- Remove --}}
                @if (count($segments) > 2)
                    <button type="button" wire:click="removeSegment({{ $index }})"
                        class="absolute top-2 right-2 text-red-500 hover:text-red-700">✖</button>
                @endif

            </div>

        </div>
    @endforeach


    {{-- Add Segment (for multicity) --}}
    @if ($view === 'multicity')
        <div class="flex justify-center">
            <button type="button" wire:click="addSegment"
                class="px-6 py-2 bg-blue-500 text-white rounded-full shadow hover:bg-blue-600">+ Add Another
                Flight</button>
        </div>
    @endif

    {{-- Traveler + Cabin --}}
    <div class="relative p-4 ">
        <div class="px-4 py-2 cursor-pointer bg-gray-100 border border-gray-300 rounded-lg shadow-sm flex justify-between items-center"
            wire:click="toggleTravelerDropdown">
            <span class="font-medium">{{ array_sum($travelers) }} Travelers</span>
            <span class="capitalize">{{ ucfirst($cabin) }}</span>
        </div>

        @if ($showTravelerDropdown)
            <div
                class="absolute z-20 mt-2 w-full bg-gray-100 border border-gray-300 rounded-xl shadow-lg p-4 space-y-4 max-h-96 overflow-y-auto">
                <div class="flex justify-end mb-2 md:hidden">
                    <button wire:click="toggleTravelerDropdown" class="text-gray-500 hover:text-gray-700">✖
                        Close</button>
                </div>

                {{-- Travelers --}}
                <div>
                    <h3 class="font-semibold mb-2">Travelers</h3>
                    @php $disabled = $travelers['student'] > 0; @endphp
                    @foreach (['adult' => 'Adult', 'child' => 'Child', 'infant' => 'Infant', 'senior' => 'Senior Citizen', 'student' => 'Student'] as $type => $label)
                        <div class="flex items-center justify-between mb-2">
                            <span>{{ $label }}</span>
                            <div class="flex items-center gap-2">
                                <button wire:click="decrementTraveler('{{ $type }}')"
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 border border-gray-300 hover:bg-gray-200"
                                    @disabled($disabled && $type !== 'student')>-</button>
                                <span>{{ $travelers[$type] }}</span>
                                <button wire:click="incrementTraveler('{{ $type }}')"
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 border border-gray-300 hover:bg-gray-200"
                                    @disabled($disabled && $type !== 'student')>+</button>
                            </div>
                        </div>
                    @endforeach

                    @if ($travelers['student'] > 0)
                        <p class="text-sm text-blue-600">Verification is required in the country.</p>
                    @endif
                    @if ($travelers['infant'] > 0 && $travelers['adult'] < 1 && $travelers['senior'] < 1)
                        <p class="text-sm text-red-600">At least one adult or senior is required for infants.</p>
                    @endif
                </div>

                {{-- Cabin --}}
                <div>
                    <h3 class="font-semibold mb-2">Cabin Class</h3>
                    <div class="flex flex-col gap-2">
                        @foreach (['economy' => 'Economy', 'premium' => 'Premium Economy', 'business' => 'Business', 'first' => 'First Class'] as $key => $label)
                            <label class="flex items-center gap-2">
                                <input type="radio" wire:model="cabin" value="{{ $key }}" />
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>

<script>
    document.addEventListener('livewire:load', initFlatpickrs);
    document.addEventListener('livewire:navigated', initFlatpickrs);

    function initFlatpickrs() {
        document.querySelectorAll('.flatpickr-input').forEach(function(el) {
            if (!el._flatpickr) {
                flatpickr(el, {
                    minDate: "today",
                    dateFormat: "Y-m-d"
                });
            }
        });
    }

    window.addEventListener('infant-error', event => {
        alert(event.detail.message);
    });
</script>
