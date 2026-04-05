<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use App\Models\Idea;
use App\Tables\IdeaTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Tk\Support\Facades\Breadcrumbs;

class IdeaController extends Controller
{

    public function create()
    {
        Breadcrumbs::push('idea|Idea Create');
        return view('pages.examples.ideas.create');
    }

    public function show(Idea $idea)
    {
        Breadcrumbs::push('idea|Idea View');
        //Gate::authorize('update', $idea);

        return view('pages.examples.ideas.edit',[
            'mode' => 'view',
            'idea' => $idea,
        ]);
    }

    public function edit(Idea $idea)
    {
        Breadcrumbs::push('idea|Idea Edit');

        return view('pages.examples.ideas.edit',[
            'mode' => 'edit',
            'idea' => $idea,
        ]);
    }

    public function update(Idea $idea, Request $request)
    {

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

        return redirect('/examples/ideas/' . $idea->id);
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

        return redirect('/examples/ideas');
    }

    public function destroy(Idea $idea)
    {
        $idea->delete();
        return redirect('/examples/ideas');
    }

    public function deleteAll()
    {
        Auth::user()->ideas()->delete();
        return redirect('/examples/ideas');
    }
}
