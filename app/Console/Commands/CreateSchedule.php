<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ScheduleController;

class CreateSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:generate_schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command that will generate CSV schedule for next 90 days of entered date for cleaning office.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startDate = $this->ask('Enter date [format: yyyy-mm-dd] ');
        $validator = \Validator::make(['date' => $startDate], ['date' => 'required|date|date_format:Y-m-d']);
        if ($validator->fails()) {
            $message = $validator->messages();
            $this->info($message);
            return 0;
        } else {
            $scheduleController = new ScheduleController();
            $scheduleController->generateSchedule($startDate);
            $this->info('Generate schedule from date : ' . $startDate);
            $this->info('CSV file created successfully!');
            
            return 0;
        }
    }
}
