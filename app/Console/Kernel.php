<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $workflows = DB::table('user_workflows')
            ->where('is_active', 1)
            ->get();

        foreach ($workflows as $workflow) {
            $command = $workflow->name;
            $this->scheduleCommand($schedule, $command, $workflow->recurring_duration);
        }
    }

    protected function scheduleCommand(Schedule $schedule, string $command, string $recurringDuration): void
    {
        switch ($recurringDuration) {
            case 'Daily':
                $schedule->command($command)->daily();
                break;
            case 'Weekly':
                $schedule->command($command)->weekly();
                break;
            case 'Monthly':
                $schedule->command($command)->monthly();
                break;
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
