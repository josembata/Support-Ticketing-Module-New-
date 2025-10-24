<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function index()
    {
        $agents = Agent::with('user')->get();
        return view('agents.index', compact('agents'));
    }

    public function create()
    {
        $users = User::all(); // show available users to assign
        return view('agents.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        Agent::create($validated);

        return redirect()->route('agents.index')->with('success', 'Agent created successfully.');
    }

    public function edit(Agent $agent)
    {
        $users = User::all();
        return view('agents.edit', compact('agent', 'users'));
    }

    public function update(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        $agent->update($validated);

        return redirect()->route('agents.index')->with('success', 'Agent updated successfully.');
    }

    public function destroy(Agent $agent)
    {
        $agent->delete();
        return redirect()->route('agents.index')->with('success', 'Agent deleted successfully.');
    }
}
