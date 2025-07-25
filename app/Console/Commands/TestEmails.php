<?php

namespace App\Console\Commands;

use App\Mail\HolidayPending;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::find(1);
        $dataToSend = [
            'name' => $user->name,
            'email' => $user->email,
            'type' => 'Vacation',
            'start_date' => '01-01-2000',
            'end_date' => '01-01-2000',
        ];

        Mail::to($user)->send(new HolidayPending($dataToSend));
    }
}
