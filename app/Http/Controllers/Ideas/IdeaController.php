<?php

namespace App\Http\Controllers\Ideas;

use App\Http\Controllers\Controller;
use App\Models\Idea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class IdeaController extends Controller
{
    public function index()
    {
        $this->setPageTitle('ideas|Idea Manager');
        //Gate::authorize('admin-view');

        //$ideas = Auth::user()->ideas()->orderBy('created_at', 'desc')->get();
        return view('ideas.index', [
            'ideas' => Auth::user()->ideas,
        ]);
    }
    public function create()
    {
        $this->setPageTitle('idea|Idea Create');
        return view('ideas.create');
    }

    public function show(Idea $idea)
    {
        $this->setPageTitle('idea|Idea View');
        //Gate::authorize('update', $idea);

        return view('ideas.edit',[
            'mode' => 'view',
            'idea' => $idea,
        ]);
    }

    public function edit(Idea $idea)
    {
        $this->setPageTitle('idea|Idea Edit');
        Gate::authorize('update', $idea);

        return view('ideas.edit',[
            'mode' => 'edit',
            'idea' => $idea,
        ]);
    }

    public function update(Idea $idea, Request $request)
    {
        Gate::authorize('update', $idea);

        $request->validate([
            'title' => ['required', 'min:3'],
            'status' => 'required',
            'description' => ['required', 'min:3'],
        ]);

        $idea->update([
            'title' => request('title'),
            'status' => request('status'),
            'description' => request('description'),
        ]);

        return redirect('/ideas/' . $idea->id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'min:3'],
            'status' => 'required',
            'description' => ['required', 'min:3'],
        ]);

        $idea = Auth::user()->ideas()->create([
            'title' => request('title'),
            'status' => request('status'),
            'description' => request('description'),
        ]);

        Auth::user()->notify(new \App\Notifications\IdeaPublished($idea->first()));

        return redirect('/ideas');
    }

    public function destroy(Idea $idea)
    {
        Gate::authorize('update', $idea);
        $idea->delete();
        return redirect('/ideas');
    }

    public function deleteAll()
    {
        Auth::user()->ideas()->delete();
        return redirect('/ideas');
    }
}
