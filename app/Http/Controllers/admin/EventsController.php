<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Models\Masters\AutoAreas;
use App\Models\User;
use App\Services\CommonFirebaseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:events_announce'])->only('index');
        $this->middleware(['permission:add_events_announce'])->only(['create','store']);
        $this->middleware(['permission:edit_events_announce'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_events_announce'])->only(['destroy']);
    }

    public function index()
    {
        $events = Events::with('location:id,name')->where('status_id','!=',3)->get();
        return view('admin.events.index',compact('events'));
    }

    public function create()
    {
        $areas = AutoAreas::select('id','name')->get();
        return view('admin.events.create',compact('areas'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title'         => 'required',
            'location'      => 'required',
            'description'   => 'nullable|string',
            'event_image'   => 'nullable|file',
            'map_link'      => 'nullable|string',
            'audio_file'    => 'nullable|file',
            'end_date'      => 'required|date',
        ]);
        
        $events = new Events();
        $events->created_by     = Auth::user()->id;
        $events->title          = $request->title;
        $events->location_id    = $request->location;
        $events->description    = $request->description;
        $events->map_link       = $request->map_link;
        $events->end_date       = $request->end_date;
        if ($request->hasFile('event_image')) {
            $events->image = uploadedAsset($request, 'event_image', 'event_image'.time(), 'events');
        }
        if ($request->hasFile('audio_file')) {
            $events->audio_file = uploadedAsset($request, 'audio_file', 'audio_file'.time(), 'events');
        }
        $events->ip_address     = $request->ip();
        $events->save();
        // Send via Firebase
        // $users = User::where('role_id', 1000)
        //     ->whereNotNull('device_token')
        //     ->get();
        $users = User::where('role_id', 1000)
            ->whereNotNull('device_token')
            ->where('id','1')
            ->get();
        $tokens   = $users->pluck('device_token')->toArray();
        $userIds  = $users->pluck('id')->toArray();
        $title    = $events->title;
        $body     = $request->description;
        $firebase = new CommonFirebaseNotification();
        $firebase->sendCommonNotification($tokens, $title, $body, [], $userIds);
        
        return redirect()->route('events')->with('success','Events Successfully Created.');
    }

    public function edit($id)
    {
        $event = Events::findOrFail($id);
        $areas = AutoAreas::select('id','name')->get();
        return view('admin.events.edit',compact('areas','event'));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'title'         => 'required',
            'location'      => 'required',
            'description'   => 'nullable|string',
            'event_image'   => 'nullable|file',
            'map_link'      => 'nullable|string',
            'audio_file'    => 'nullable|file',
            'end_date'      => 'required|date',
        ]);
        
        $events = Events::findOrFail($request->id);
        $events->created_by     = Auth::user()->id;
        $events->title          = $request->title;
        $events->location_id    = $request->location;
        $events->description    = $request->description;
        $events->map_link       = $request->map_link;
        $events->end_date       = $request->end_date;
        if ($request->hasFile('event_image')) {
            if(isset($events->image)){
                if(file_exists(public_path($events->image))){
                    unlink(public_path($events->image));
                }
            }
            $events->image = uploadedAsset($request, 'event_image', 'event_image'.time(), 'events');
        }
        if ($request->hasFile('audio_file')) {
            if(isset($events->audio_file)){
                if(file_exists(public_path($events->audio_file))){
                    unlink(public_path($events->audio_file));
                }
            }
            $events->audio_file = uploadedAsset($request, 'audio_file', 'audio_file'.time(), 'events');
        }
        $events->ip_address     = $request->ip();
        $events->save();
        return redirect()->route('events')->with('success','Events Successfully Updated.');
    }

    public function destroy(Request $request)
    {
        $event = Events::find($request->id);
        if ($event) {
            $event->status_id = 3;
            $event->save();
            return response()->json(array('success' => true, 'message' => 'Events Deleted Successfully'));
        } else {
            return response()->json(array('success' => false, 'message' => 'Events Not Found?...'));
        }
    }
}
