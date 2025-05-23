protected $signature = 'meetings:cleanup';

public function handle()
{
    Meeting::where('scheduled_end', '<', now())
        ->where('is_active', true)
        ->update(['is_active' => false]);
    
    $this->info('Expired meetings cleaned successfully');
}