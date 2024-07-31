<?php

namespace App\Livewire;

use Livewire\Component;
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
            'start_location' => $this->getLocation(),
        ]);
        $this->clockedIn = true;
    }

    public function clockOut() {
        if (!$this->clockedIn) return;
        if ($this->onBreak) {
            $this->breakEntry->update([
                'end_time' => now(),
                'end_location' => $this->getLocation(),
            ]);
            $this->onBreak = false;
        }
        $this->timeEntry->update([
            'end_time' => now(),
            'end_location' => $this->getLocation(),
        ]);
        $this->clockedIn = false;
        $this->timeEntry = null;
    }

    public function startBreak() {
        if (!$this->clockedIn || $this->onBreak) return;
        $this->breakEntry = BreakEntry::create([
            'time_entry_id' => $this->timeEntry->id,
            'start_time' => now(),
            'start_location' => $this->getLocation(),
        ]);
        $this->onBreak = true;
    }

    public function endBreak() {
        if (!$this->onBreak) return;
        $this->breakEntry->update([
            'end_time' => now(),
            'end_location' => $this->getLocation(),
        ]);
        $this->onBreak = false;
        $this->breakEntry = null;
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
        $this->totalBreakTime = "{$breakHours}:{$breakMinutes}:{$breakSeconds}";
        
    }

    public function updateTotalBreakTime() {
        if (!$this->timeEntry || !$this->onBreak) return;
        
    }

    public function getLocation()
    {
        // ToDo: Implement this method
        return json_encode([
            "latitude" => 4.1753,
            "longitude" => 73.5091,
        ]);
    }

    public function poll() {
        $this->updateTotalTime();
    }

    public function render()
    {
        return view('livewire.clock-in-out');
    }
}
