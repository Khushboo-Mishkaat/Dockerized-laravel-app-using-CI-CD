<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\UserWorkflow;

class WorkFlowController extends Controller
{
    // Method to fetch and display all workflows
    public function index()
    {
        $workflows = UserWorkflow::all(); 
        
        return Inertia::render('WorkFlows/Index', [
            'workflows' => $workflows
        ]);
    }

    // Method to show edit form for a single workflow
    public function edit($id)
    {
        $record = UserWorkflow::findOrFail($id);
        return Inertia::render('WorkFlows/Edit', [
            'record' => $record
        ]);
    }
    

    // Handle the form submission to update a record
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_filter' => 'required|string|max:255',
            'recurring_duration' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);
    
        $record = UserWorkflow::findOrFail($id);
        $record->update($request->all());
    
        return redirect()->route('workflow.index')->with('success', 'Record updated successfully!');
    }
    
}
