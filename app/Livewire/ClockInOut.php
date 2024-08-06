<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On; 
use App\Models\TimeEntry;
use App\Models\BreakEntry;
use Carbon\Carbon;

class ClockInOut extends Component
{
    public $timeEntry;
    public $breakEntry;
    public $clockedIn = false;
    public $onBreak = false;
    public $totalWorkTime = "00:00:00";
    public $totalBreakTime = "00:00:00";
    public $location;

    public function mount() {
        $this->timeEntry = TimeEntry::where('user_id', auth()->id())
            ->whereNull('end_time')
            ->first();
        if ($this->timeEntry) {
            $this->clockedIn = true;
            $this->breakEntry = BreakEntry::where('time_entry_id', $this->timeEntry->id)
                ->whereNull('end_time')
                ->first();
            if ($this->breakEntry) {
                $this->onBreak = true;
            }
            $this->updateTotalTime();
        }
    }

    public function clockIn() {
        if ($this->clockedIn) return;
        $this->timeEntry = TimeEntry::create([
            'user_id' => auth()->id(),
            'start_time' => now(),
            'start_location' => $this->location,
        ]);
        $this->clockedIn = true;
    }

    public function clockOut() {
        if (!$this->clockedIn) return;
        if ($this->onBreak) {
            $this->breakEntry->update([
                'end_time' => now(),
                'end_location' => $this->location,
            ]);
            $this->onBreak = false;
        }
        $this->timeEntry->update([
            'end_time' => now(),
            'end_location' => $this->location,
        ]);
        $this->clockedIn = false;
        $this->timeEntry = null;
    }

    public function startBreak() {
        if (!$this->clockedIn || $this->onBreak) return;
        $this->breakEntry = BreakEntry::create([
            'time_entry_id' => $this->timeEntry->id,
            'start_time' => now(),
            'start_location' => $this->location,
        ]);
        $this->onBreak = true;
    }

    public function endBreak() {
        if (!$this->onBreak) return;
        $this->breakEntry->update([
            'end_time' => now(),
            'end_location' => $this->location,
        ]);
        $this->onBreak = false;
        $this->breakEntry = null;
        $this->totalBreakTime = "00:00:00";
    }

    public function updateTotalTime() {
        if (!$this->timeEntry) return;
        $startTime = Carbon::parse($this->timeEntry->start_time);
        $currentTime = Carbon::now();
        $totalSeconds = $startTime->diffInSeconds($currentTime);

        $breaks = $this->timeEntry->breaks()->get();
        $totalBreakSeconds = 0;
        foreach ($breaks as $break) {
            $breakStart = Carbon::parse($break->start_time);
            $breakEnd = Carbon::parse($break->end_time) ?? $currentTime;
            $totalBreakSeconds += $breakStart->diffInSeconds($breakEnd);
        }
        $totalSeconds -= $totalBreakSeconds;

        $hours = str_pad(floor($totalSeconds / 3600), 2, '0', STR_PAD_LEFT);
        $minutes = str_pad(floor(($totalSeconds % 3600) / 60), 2, '0', STR_PAD_LEFT);
        $seconds = str_pad($totalSeconds % 60, 2, '0', STR_PAD_LEFT);
        $breakHours = str_pad(floor($totalBreakSeconds / 3600), 2, '0', STR_PAD_LEFT);
        $breakMinutes = str_pad(floor(($totalBreakSeconds % 3600) / 60), 2, '0', STR_PAD_LEFT);
        $breakSeconds = str_pad($totalBreakSeconds % 60, 2, '0', STR_PAD_LEFT);
        
        $this->totalWorkTime = "{$hours}:{$minutes}:{$seconds}";
    }

    public function updateCurrentBreakTime() {
        if (!$this->breakEntry) return;
        $startTime = Carbon::parse($this->breakEntry->start_time);
        $currentTime = Carbon::now();
        $totalSeconds = $startTime->diffInSeconds($currentTime);

        $hours = str_pad(floor($totalSeconds / 3600), 2, '0', STR_PAD_LEFT);
        $minutes = str_pad(floor(($totalSeconds % 3600) / 60), 2, '0', STR_PAD_LEFT);
        $seconds = str_pad($totalSeconds % 60, 2, '0', STR_PAD_LEFT);
        
        $this->totalBreakTime = "{$hours}:{$minutes}:{$seconds}";
    }

    #[On('update-location')] 
    public function updateLocation($latitude, $longitude) {
        $this->location = [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];
    }

    public function poll() {
        $this->updateTotalTime();
        $this->updateCurrentBreakTime();
    }

    public function render()
    {
        return view('livewire.clock-in-out');
    }
}
