<?php

namespace App\Http\Controllers;

use App\Exports\ContactsExport;
use App\Models\Contact;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Contact::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->company) {
            $query->where('company', 'like', '%' . $request->company . '%');
        }

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $contacts = $query->paginate(50);

        return view('contact.index', compact('contacts'));
    }

    public function export(Request $request)
    {
        return Excel::download(new ContactsExport($request), 'contacts.csv');
    }
}
