<?php

use Illuminate\Support\Facades\Schedule;

// Run the scheduler with `php artisan schedule:work` (development) or a cron entry
// `* * * * * php /path/to/artisan schedule:run` (production).
Schedule::command('orders:cancel-stale')->daily();
