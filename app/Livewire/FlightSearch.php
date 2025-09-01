<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class FlightSearch extends Component
{
    public $view = 'oneway'; // oneway, roundtrip, multicity
    public $segments = [];
    public $returnDate = '';
    public $airports = [];
    public $showDropdowns = [];

    // Traveler + Cabin
    public $travelers = [
        'adult' => 1,
        'child' => 0,
        'infant' => 0,
        'senior' => 0,
        'student' => 0,
    ];
    public $maxTravelers = 9;
    public $cabin = 'economy';
    public $showTravelerDropdown = false;

    public function mount()
    {
        $this->airports = [
            'CMB' => 'Bandaranaike International Airport',
            'JFK' => 'John F. Kennedy International Airport',
            'LHR' => 'London Heathrow Airport',
            'NRT' => 'Narita International Airport',
            'DXB' => 'Dubai International Airport',
            'HND' => 'Tokyo Haneda Airport',
            'SIN' => 'Singapore Changi Airport',
            'SYD' => 'Sydney Kingsford Smith Airport',
            'CDG' => 'Charles de Gaulle Airport',
            'FRA' => 'Frankfurt am Main Airport',
        ];

        // Default segment
        $this->segments[] = [
            'origin' => '',
            'destination' => '',
            'date' => Carbon::now()->format('Y-m-d'),
        ];
        $this->showDropdowns[0] = ['origin' => false, 'destination' => false];

        $this->returnDate = Carbon::now()->addDay()->format('Y-m-d');
    }

    // Change view
    public function setView($view)
    {
        $this->view = $view;
        if ($view === 'oneway') {
            $this->segments = [
                ['origin' => '', 'destination' => '', 'date' => Carbon::now()->format('Y-m-d')]
            ];
            $this->showDropdowns = [ ['origin'=>false,'destination'=>false] ];
        }
        if ($view === 'roundtrip') {
            $this->segments = [
                ['origin' => '', 'destination' => '', 'date' => Carbon::now()->format('Y-m-d')]
            ];
            $this->showDropdowns = [ ['origin'=>false,'destination'=>false] ];
            $this->returnDate = Carbon::now()->addDay()->format('Y-m-d');
        }
        if ($view === 'multicity') {
            $this->segments = [
                ['origin' => '', 'destination' => '', 'date' => Carbon::now()->format('Y-m-d')],
                ['origin' => '', 'destination' => '', 'date' => Carbon::now()->addDay()->format('Y-m-d')],
            ];
            $this->showDropdowns = [
                ['origin'=>false,'destination'=>false],
                ['origin'=>false,'destination'=>false]
            ];
        }
    }

    public function addSegment()
    {
        $this->segments[] = [
            'origin' => '',
            'destination' => '',
            'date' => Carbon::now()->format('Y-m-d'),
        ];
        $index = count($this->segments) - 1;
        $this->showDropdowns[$index] = ['origin' => false, 'destination' => false];
    }

    public function removeSegment($index)
    {
        unset($this->segments[$index], $this->showDropdowns[$index]);
        $this->segments = array_values($this->segments);
        $this->showDropdowns = array_values($this->showDropdowns);
    }

    public function swapLocations($index)
    {
        $temp = $this->segments[$index]['origin'];
        $this->segments[$index]['origin'] = $this->segments[$index]['destination'];
        $this->segments[$index]['destination'] = $temp;
    }

    public function showDropdown($index, $field)
    {
        $this->showDropdowns[$index][$field] = true;
    }

    public function hideDropdown($index, $field)
    {
        $this->showDropdowns[$index][$field] = false;
    }

    public function getFilteredAirports($index, $field)
    {
        $search = $this->segments[$index][$field] ?? '';
        if (strlen($search) < 2) return $this->airports;
        return collect($this->airports)
            ->filter(fn($name, $code) => str_contains(strtolower($name), strtolower($search)) || str_contains(strtolower($code), strtolower($search)))
            ->toArray();
    }

    public function selectAirport($code, $name, $index, $field)
    {
        $this->segments[$index][$field] = "$code - $name";
        $this->showDropdowns[$index][$field] = false;
    }

    // Traveler logic same as before
    public function incrementTraveler($type)
    {
        if ($type === 'student') {
            $this->travelers = ['adult'=>0,'child'=>0,'infant'=>0,'senior'=>0,'student'=>1];
            return;
        }
        if ($this->travelers['student']>0) return;
        if ($type==='infant' && ($this->travelers['adult']<1 && $this->travelers['senior']<1)) {
            $this->dispatchBrowserEvent('infant-error',['message'=>'At least one adult or senior is required for each infant.']);
            return;
        }
        if ($this->totalTravelers()<$this->maxTravelers) $this->travelers[$type]++;
    }

    public function decrementTraveler($type)
    {
        if ($this->travelers[$type]>0) $this->travelers[$type]--;
        if ($type==='adult' && $this->travelers['adult']<1 && $this->travelers['infant']>0) $this->travelers['adult']=1;
    }


    public function totalTravelers()
    {
        return array_sum($this->travelers);
    }

    public function toggleTravelerDropdown()
    {
        $this->showTravelerDropdown = !$this->showTravelerDropdown;
    }

    public function render()
    {
        return view('livewire.flight-search');
    }
}
