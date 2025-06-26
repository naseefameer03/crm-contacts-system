<?php

namespace App\Exports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContactsExport implements FromQuery, WithHeadings, WithMapping, WithEvents, WithChunkReading, ShouldAutoSize, WithStyles, WithTitle
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = Contact::query();

        if ($this->request['status']) {
            $query->where('status', $this->request['status']);
        }

        if ($this->request['company']) {
            $query->where('company', 'like', '%' . $this->request['company'] . '%');
        }

        if ($this->request['dateRange']) {
            $dateRange = explode(' - ', $this->request['dateRange']);
            if (count($dateRange) === 2) {
                $query->whereBetween('created_at', [trim($dateRange[0]), trim($dateRange[1])]);
            }
        }

        if ($this->request['search']) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->request['search']}%")
                    ->orWhere('email', 'like', "%{$this->request['search']}%");
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Name', 'Email', 'Phone', 'Company', 'Status', 'Created At'];
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->email,
            $row->phone,
            $row->company,
            $row->status,
            $row->created_at->format('Y-m-d')
        ];
    }

    public function chunkSize(): int
    {
        return 5000;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getFont()->setSize(10)->setName('Liberation Sans');
            },
        ];
    }

    public function ShouldAutoSize()
    {
        return true;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 10, 'name' => 'Liberation Sans']], // Style the first row as bold text.
        ];
    }

    public function title(): string
    {
        return 'Leads';
    }
}
