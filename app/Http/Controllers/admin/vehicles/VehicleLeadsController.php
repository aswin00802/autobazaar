<?php

namespace App\Http\Controllers\admin\vehicles;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle\VehicleEnquiry;
use App\Models\Vehicle\VehicleModel;
use Illuminate\Http\Request;

/** Leads from the vehicle detail page (enquiry / quotation / test drive / loan). */
class VehicleLeadsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:vehicle_leads']);
    }

    public function index(Request $request)
    {
        $filters = [
            'source'      => $request->get('source', ''),
            'lead_status' => $request->get('lead_status', ''),
            'from'        => $request->get('from', ''),
            'to'          => $request->get('to', ''),
            'q'           => trim((string) $request->get('q', '')),
            'model_id'    => $request->get('model_id', ''),
        ];

        $leads = VehicleEnquiry::with(['model:id,name', 'variant:id,name', 'assignee:id,name'])
            ->where('status_id', '!=', 3)
            ->when($filters['source'], fn ($q, $v) => $q->where('source', $v))
            ->when($filters['lead_status'], fn ($q, $v) => $q->where('lead_status', $v))
            ->when($filters['model_id'], fn ($q, $v) => $q->where('vehicle_model_id', $v))
            ->when($filters['from'], fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['to'], fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($filters['q'], function ($q, $v) {
                $q->where(function ($w) use ($v) {
                    $w->where('mobile', 'like', "%$v%")
                        ->orWhere('name', 'like', "%$v%")
                        ->orWhere('enquiry_no', 'like', "%$v%");
                });
            })
            ->latest('id')
            ->get();

        return view('admin.vehicles.leads.index', [
            'leads'    => $leads,
            'filters'  => $filters,
            'admins'   => $this->admins(),
            'models'   => VehicleModel::orderBy('name')->get(['id', 'name']),
            'sources'  => VehicleEnquiry::SOURCES,
            'pipeline' => VehicleEnquiry::PIPELINE,
        ]);
    }

    public function show($id)
    {
        $lead = VehicleEnquiry::with(['model.brand', 'variant', 'user', 'assignee'])->findOrFail($id);

        return view('admin.vehicles.leads.view', [
            'lead'     => $lead,
            'admins'   => $this->admins(),
            'sources'  => VehicleEnquiry::SOURCES,
            'pipeline' => VehicleEnquiry::PIPELINE,
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id'          => 'required|integer',
            'lead_status' => 'required|in:' . implode(',', VehicleEnquiry::PIPELINE),
        ]);

        $lead = VehicleEnquiry::findOrFail($request->id);
        $lead->lead_status = $request->lead_status;
        $lead->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'lead_status' => $lead->lead_status]);
        }

        return redirect()->back()->with('success', 'Lead status updated');
    }

    public function assign(Request $request)
    {
        $request->validate([
            'id'          => 'required|integer',
            'assigned_to' => 'nullable|integer',
        ]);

        $lead = VehicleEnquiry::findOrFail($request->id);
        $lead->assigned_to = $request->assigned_to ?: null;
        $lead->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'assigned_to' => $lead->assigned_to]);
        }

        return redirect()->back()->with('success', 'Lead assigned');
    }

    public function note(Request $request)
    {
        $request->validate([
            'id'         => 'required|integer',
            'admin_note' => 'nullable|string|max:5000',
        ]);

        $lead = VehicleEnquiry::findOrFail($request->id);
        $lead->admin_note = $request->admin_note;
        $lead->save();

        return redirect()->back()->with('success', 'Note saved');
    }

    /** Users who can own a lead: anyone holding an admin-side role. */
    private function admins()
    {
        return User::whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'Super Admin']))
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
