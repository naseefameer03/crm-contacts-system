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

        if ($this->request['total_contacts'] < 50000) {
            $filename = 'exports/contacts_' . now()->format('Ymd_His') . '.xlsx';

            $ready = ExportReady::create([
                'filepath' => $filename
            ]);

            Excel::store(new ContactsExport($this->request), $filename, 'public');

            $ready->is_completed = 1;
            $ready->save();
        } else {

            $filename = 'exports/contacts_' . now()->format('Ymd_His') . '.zip';
            $ready = ExportReady::create([
                'filepath' => $filename
            ]);
            $zip = new \ZipArchive();
            if ($zip->open(storage_path('app/public/' . $filename), \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                $chunkSize = 50000;
                $totalContacts = $this->request['total_contacts'];
                $chunks = ceil($totalContacts / $chunkSize);
                for ($i = 0; $i < $chunks; $i++) {
                    $chunkFilename = 'exports/contacts_chunk_' . ($i + 1) . '_' . now()->format('Ymd_His') . '.xlsx';
                    Excel::store(new ContactsExport(array_merge($this->request, ['chunk' => $i, 'chunk_size' => $chunkSize])), $chunkFilename, 'public');
                    $zip->addFile(storage_path('app/public/' . $chunkFilename), basename($chunkFilename));
                }
                $zip->close();
                $ready->is_completed = 1;
                $ready->save();
            } else {
                // Handle error if zip file cannot be created
                throw new \Exception('Could not create zip file for contacts export.');
            }
        }
    }

    // failed method to handle job failure
    public function failed(\Exception $exception): void
    {
        ExportReady::whereIn('is_viewed', [0, 1])->delete();
    }
}
