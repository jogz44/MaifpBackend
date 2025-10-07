<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\deleted_activity_log;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ArchiveOldActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:archive-old-activities';

    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    protected $signature = 'activity:archive';
    protected $description = 'Archive activity logs older than 2 days';


    public function handle()
    {
        $cutoff = Carbon::now()->subDays(7);

        $oldActivities = Activity::where('created_at', '<', $cutoff)->get();

        foreach ($oldActivities as $activity) {
            deleted_activity_log::create([
                'log_name'   => $activity->log_name,
                'description' => $activity->description,
                'subject'    => $activity->subject ? json_encode($activity->subject) : null,
                'causer'     => $activity->causer ? json_encode($activity->causer) : null,
                'properties' => $activity->properties ? json_encode($activity->properties) : null,
                'created_at' => $activity->created_at,
                'deleted_at' => now(),
            ]);

            $activity->delete(); // delete from spatie_activity_log
        }

        $this->info("Archived and deleted " . $oldActivities->count() . " activity logs.");
    }


}
