<div class="relative w-full max-w-7xl mx-auto space-y-6 p-4 sm:p-6 md:p-8 bg-gray-50 min-h-screen">
    <!-- Navigation Tabs -->
    <div class="flex flex-wrap gap-3 mb-6 justify-center sm:justify-start">
        <button wire:click="setView('oneway')"
            class="px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 {{ $view === 'oneway' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-100' }}">
            One-way
        </button>
        <button wire:click="setView('roundtrip')"
            class="px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 {{ $view === 'roundtrip' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-100' }}">
            Round-trip
        </button>
        <button wire:click="setView('multicity')"
            class="px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 {{ $view === 'multicity' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-100' }}">
            Multi-city
        </button>
    </div>

    <!-- Flight Segments -->

    <div class="flex flex-col items-center justify-center  bg-gray-50 p-4">

        @foreach ($segments as $index => $segment)
            <div
                class="relative w-full md:w-auto flex flex-col md:flex-row items-center gap-4 md:gap-2 bg-gray-100 rounded-lg border border-gray-300 p-4 flex-wrap mb-3">

                <!-- Close / Remove Button (top-right corner) -->
                @if (count($segments) > 2 && $index > 1)
                    <button type="button" wire:click="removeSegment({{ $index }})"
                        class="absolute top-2 right-2 text-red-500 hover:text-red-700 z-20 bg-white rounded-full">
                        <svg class="w-6 h-6 t" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m15 9-6 6m0-6 6 6m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </button>
                @endif

                <!-- From / Origin Input -->
                <div class="relative flex-1 w-full md:w-64 md:mr-2">
                    <input type="text" wire:model.live.debounce.300ms="segments.{{ $index }}.origin"
                        wire:focus="showDropdown({{ $index }}, 'origin')" placeholder="Origin Airport"
                        class="w-full pl-4 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 placeholder-gray-500" />

                    @if (isset($showDropdowns[$index]['origin']) && $showDropdowns[$index]['origin'])
                        <div wire:click.away="hideDropdown({{ $index }}, 'origin')"
                            class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <ul>
                                @foreach ($this->getFilteredAirports($index, 'origin') as $code => $name)
                                    <li wire:click="selectAirport('{{ $code }}','{{ $name }}',{{ $index }},'origin')"
                                        class="px-4 py-2 hover:bg-blue-50 cursor-pointer text-sm">
                                        <span class="font-semibold">{{ $code }}</span> - {{ $name }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- Swap Button -->
                <div class="flex justify-end w-full md:w-auto md:-mx-6 z-10 -my-8 pr-2 md:pr-0">
                    <button type="button" wire:click="swapLocations({{ $index }})"
                        class="p-2 bg-white rounded-full shadow hover:bg-gray-100 transition-all border border-gray-400">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m-16 6h12m0 0l-4-4m4 4l-4 4" />
                        </svg>
                    </button>
                </div>

                <!-- To / Destination Input -->
                <div class="relative flex-1 w-full md:w-64 md:ml-2">
                    <input type="text" wire:model.live.debounce.300ms="segments.{{ $index }}.destination"
                        wire:focus="showDropdown({{ $index }}, 'destination')" placeholder="Destination Airport"
                        class="w-full pl-4 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 placeholder-gray-500" />

                    @if (isset($showDropdowns[$index]['destination']) && $showDropdowns[$index]['destination'])
                        <div wire:click.away="hideDropdown({{ $index }}, 'destination')"
                            class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <ul>
                                @foreach ($this->getFilteredAirports($index, 'destination') as $code => $name)
                                    <li wire:click="selectAirport('{{ $code }}','{{ $name }}',{{ $index }},'destination')"
                                        class="px-4 py-2 hover:bg-blue-50 cursor-pointer text-sm">
                                        <span class="font-semibold">{{ $code }}</span> - {{ $name }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- Departure Date -->
                <div class="flex-1 w-full md:w-48 -mt-3 md:mt-0">
                    <input type="text" wire:model="segments.{{ $index }}.date" placeholder="Departure Date"
                        class="flatpickr-input w-full px-4 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" />
                </div>

                <!-- Return Date (for roundtrip) -->
                @if ($view === 'roundtrip')
                    <div class="flex-1 w-full md:w-48 -mt-3 md:mt-0">
                        <input type="text" wire:model="returnDate" placeholder="Return Date"
                            class="flatpickr-input w-full px-4 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" />
                    </div>
                @endif
            </div>
        @endforeach


        <div
            class="relative w-full md:w-3/4 lg:w-2/3 xl:w-1/2 flex flex-col md:flex-row items-center gap-4 md:gap-2 bg-gray-100 rounded-lg border border-gray-300 p-4 flex-wrap mb-3">

            <div class="flex-1 w-full px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm flex justify-between items-center cursor-pointer hover:bg-gray-50 transition-all"
                wire:click="toggleTravelerDropdown">
                <span class="font-medium text-gray-700">{{ array_sum($travelers) }}
                    Traveler{{ array_sum($travelers) > 1 ? 's' : '' }}</span>
                <span class="capitalize text-gray-600">{{ ucfirst($cabin) }}</span>
            </div>
            @if ($showTravelerDropdown)
                <div
                    class="absolute z-20 mt-2 w-full max-w-md bg-white border border-gray-200 rounded-xl shadow-lg p-5 space-y-6">
                    <!-- Close Button for Mobile -->
                    <div class="flex justify-end mb-2 md:hidden">
                        <button wire:click="toggleTravelerDropdown" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Travelers -->
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3">Travelers</h3>
                        @php $disabled = $travelers['student'] > 0; @endphp
                        @foreach (['adult' => 'Adult', 'child' => 'Child', 'infant' => 'Infant', 'senior' => 'Senior Citizen', 'student' => 'Student'] as $type => $label)
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm text-gray-600">{{ $label }}</span>
                                <div class="flex items-center gap-3">
                                    <button wire:click="decrementTraveler('{{ $type }}')"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 border border-gray-200 hover:bg-gray-200 transition-all"
                                        @disabled($disabled && $type !== 'student')>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="w-8 text-center text-sm">{{ $travelers[$type] }}</span>
                                    <button wire:click="incrementTraveler('{{ $type }}')"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 border border-gray-200 hover:bg-gray-200 transition-all"
                                        @disabled($disabled && $type !== 'student')>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        @if ($travelers['student'] > 0)
                            <p class="text-xs text-blue-600 mt-2">Verification is required in the country.</p>
                        @endif
                        @if ($travelers['infant'] > 0 && $travelers['adult'] < 1 && $travelers['senior'] < 1)
                            <p class="text-xs text-red-600 mt-2">At least one adult or senior is required for
                                infants.</p>
                        @endif
                    </div>

                    <!-- Cabin Class -->
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3">Cabin Class</h3>
                        <div class="grid grid-cols-1 gap-2">
                            @foreach (['economy' => 'Economy', 'premium' => 'Premium Economy', 'business' => 'Business', 'first' => 'First Class'] as $key => $label)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" wire:model="cabin" value="{{ $key }}"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-200" />
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Add Segment Button (for multicity) -->
    @if ($view === 'multicity')
        <div class="flex justify-center">
            <button type="button" wire:click="addSegment"
                class="px-6 py-2.5 bg-blue-600 text-white rounded-full shadow-md hover:bg-blue-700 transition-all text-sm font-medium">
                + Add Another Flight
            </button>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:load', initFlatpickrs);
    document.addEventListener('livewire:navigated', initFlatpickrs);

    function initFlatpickrs() {
        document.querySelectorAll('.flatpickr-input').forEach(function(el) {
            if (!el._flatpickr) {
                flatpickr(el, {
                    minDate: "today",
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "F j, Y",
                    allowInput: true
                });
            }
        });
    }

    window.addEventListener('infant-error', event => {
        alert(event.detail.message);
    });
</script>
