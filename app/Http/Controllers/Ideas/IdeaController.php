<?php

namespace App\Http\Controllers\Ideas;

use App\Http\Controllers\Controller;
use App\Models\Idea;
use App\Models\User;
use App\Tables\IdeaTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Tk\Table\Cell;

class IdeaController extends Controller
{
    public function index()
    {
        $this->setPageTitle('ideas|Idea Manager');
        //Gate::authorize('admin-view');

        $table = new IdeaTable();

        // example of using the action params
        $tableParams = $table->getStateList();
        if (isset($tableParams['row_id'])) {
            vd($tableParams);


            // clear action params once done
            $table->setState([
                'row_id' => null,
                'row_id_all' => null,
            ]);

            return redirect(request()->fullUrl())->with('success', "Table Action Completed.");
        }

        //$ideas = Auth::user()->ideas()->orderBy('created_at', 'desc')->get();
        return view('ideas.index', [
            'ideas' => auth()->user()->ideas,
            'table' => $table,
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
        vd("delete idea {$idea->id}");
        Gate::authorize('update', $idea);
        $idea->delete();
        return redirect('/ideas');
    }

    public function deleteAll()
    {
        vd('Delete All Ideas');
        Auth::user()->ideas()->delete();
        return redirect('/ideas');
    }
}
