<?php

namespace App\Services;

use ImranDevBd\AttendanceHub\Facades\AttendanceHub;
use ImranDevBd\AttendanceHub\Models\AttendanceDevice;
use ImranDevBd\AttendanceHub\Models\AttendanceLog;
use Tarikh\Tarikh;

class AttendanceBridge
{
    public function todayCount(): int
    {
        $start = Tarikh::today(config('denimforge.timezone'))->format('Y-m-d 00:00:00');

        return AttendanceLog::query()->where('punched_at', '>=', $start)->count();
    }

    public function sync(AttendanceDevice $device): int
    {
        return AttendanceHub::sync($device)->count();
    }

    public function plantClock(): string
    {
        return Tarikh::now(config('denimforge.timezone'))->format('d M Y H:i');
    }
}
