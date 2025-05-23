protected function schedule(Schedule $schedule)
{
    $schedule->command('meetings:cleanup')->everyMinute();
}