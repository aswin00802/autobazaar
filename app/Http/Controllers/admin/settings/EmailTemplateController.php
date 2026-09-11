<?php

namespace App\Http\Controllers\admin\settings;

use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Http\Controllers\Controller;

class EmailTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:emailtemplate_setting'])->only(['index','update']);
    }

    public function index()
    {
        $templates = EmailTemplate::all();
        return view('admin.settings.email-setting.index',compact('templates'));
    }

    public function create()
    {
        return view('admin.settings.email-setting.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required',
            'subject' => 'required',
            'body'    => 'required',
        ]);
        if(EmailTemplate::where('title',$request->title)->exists()){
            return back()->with('error', 'Email template already Created.');
        } else {
            EmailTemplate::create($request->all());
        }

        return redirect()->route('settings.email-template-settings')
                         ->with('success', 'Email template created successfully.');
    }

    public function edit($id)
    {
        $template = EmailTemplate::findOrFail($id);
        return view('admin.settings.email-setting.edit',compact('template'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title'   => 'required',
            'subject' => 'required',
            'body'    => 'required',
        ]);
        $template           = EmailTemplate::findOrFail($request->id);
        $template->title    = $request->title;
        $template->subject  = $request->subject;
        $template->body     = $request->body;
        $template->save();

        return redirect()->route('settings.email-template-settings')
                         ->with('success', 'Email template updated successfully.');
    }

    public function delete(Request $request)
    {
        $template = EmailTemplate::find($request->id);
        if ($template) {
            $template->delete();
            return response()->json(array('success' => true, 'message' => 'Email Template Deleted Successfully'));
        } else {
            return response()->json(array('success' => false, 'message' => 'Email Template Not Found?...'));
        }
        
    }
}
