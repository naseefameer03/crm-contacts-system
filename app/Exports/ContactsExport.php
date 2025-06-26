<?php

namespace App\Exports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContactsExport implements FromQuery, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = Contact::query();

        if ($this->request->status) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->company) {
            $query->where('company', 'like', '%' . $this->request->company . '%');
        }

        if ($this->request->from_date && $this->request->to_date) {
            $query->whereBetween('created_at', [$this->request->from_date, $this->request->to_date]);
        }

        if ($this->request->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->request->search}%")
                    ->orWhere('email', 'like', "%{$this->request->search}%");
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Phone', 'Company', 'Status', 'Created At'];
    }
}
