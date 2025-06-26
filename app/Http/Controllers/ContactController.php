<?php

namespace App\Http\Controllers;

use App\Jobs\ExportContactsJob;
use App\Models\Contact;
use App\Models\ExportReady;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the contacts.
     *
     * @param Request $request
     * @return \Illuminate\View\View
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

        if($request->dateRange) {
            $dateRange = explode(' - ', $request->dateRange);
            if (count($dateRange) === 2) {
                $query->whereBetween('created_at', [trim($dateRange[0]), trim($dateRange[1])]);
            }
        }

        // santize serach input
        $request->merge(['search' => strip_tags($request->search)]);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $contacts = $query->paginate(50);

        return view('contact.index', compact('contacts'));
    }

    /**
     * Export contacts based on the request parameters.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
        $is_completed = ExportReady::where('is_completed', 0)->where('is_viewed', 0)->exists();
        if ($is_completed) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An export is already in progress. Please wait until it is completed.'
            ]);
        }

        ExportContactsJob::dispatch($request->all());

        if ($request->total_contacts < 50000) {
            $message = 'Your export is being processed. You’ll be notified when it’s ready.';
        } else {
            $message = 'Your export is being processed. You’ll be notified when the zip file is ready.';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message
        ]);
    }

    public function check_export_ready()
    {
        $export = ExportReady::where('is_completed', 1)->where('is_viewed', 0)->latest('id')->first();

        if ($export) {
            $export->is_viewed = 1;
            $export->save();
        }

        return response()->json([
            "status"  => "success",
            "message" => "dataloaded",
            "data"    => $export
        ]);
    }
}
