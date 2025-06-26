<?php

namespace App\Jobs;

use App\Exports\ContactsExport;
use App\Models\ExportReady;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ExportContactsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    /**
     * Create a new job instance.
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ExportReady::whereIn('is_viewed', [0, 1])->delete();
        $filename = 'exports/contacts_' . now()->format('Ymd_His') . '.xlsx';

        $ready = ExportReady::create([
            'filepath' => $filename
        ]);

        Excel::store(new ContactsExport($this->request), $filename, 'public');

        $ready->is_completed = 1;
        $ready->save();
    }
}
