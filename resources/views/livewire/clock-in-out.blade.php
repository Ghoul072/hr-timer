<div class="shadow-md rounded-lg p-6" wire:poll.1s="poll">
    <div class="text-center">
        @if($clockedIn)
            <p class="text-green-500 text-lg font-semibold">You are clocked in 👍</p>
            <div class="mt-4">
                <span class="inline-block w-3 h-3 bg-green-500 rounded-full"></span>
                <span class="ml-2 text-xl font-bold">{{ $totalWorkTime }}</span>
            </div>
            <p class="mt-2 text-gray-400">Today's hours</p>
            <div class="mt-4">
                <span class="inline-block w-3 h-3 bg-blue-500 rounded-full"></span>
                <span class="ml-2 text-xl font-bold">{{ $totalBreakTime }}</span>
            </div>
            <p class="mt-2 text-gray-400">Break hours</p>
            <div class="mt-6">
                @if($onBreak)
                    <button wire:click="endBreak" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">End Break</button>
                @else
                    <button wire:click="startBreak" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">Break</button>
                    <button wire:click="clockOut" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 ml-2">Clock out</button>
                @endif
            </div>
        @else
            <p class="text-red-500 text-lg font-semibold py-2">You are not clocked in</p>
            <button wire:click="clockIn" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">Clock in</button>
        @endif
    </div>
</div>
