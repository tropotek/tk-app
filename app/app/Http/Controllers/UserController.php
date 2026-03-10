<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Tables\UserTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $this->setPageName('User Manager');

        $table = new UserTable();

        // example of using the action params
        if ($request->has('tbl_delete')) {
            vd($table->getParams(), $request->all());

            // perform required action (delete, csv, etc...)

            // reset the url removing the action params
            $url = $request->fullUrlWithQuery([
                'tbl_delete' => null,
                'row_id' => null,
                'row_id_all' => null,
            ]);

            return redirect(trim($url, '?'))->with('success', "Table Action Completed.");
        }

        return view('pages.users.index', [
            'table' => $table,
        ]);
    }

    public function create()
    {
        $this->setPageName('user.edit|User Create');

        return view('pages.users.edit');
    }

    public function show(User $user)
    {
        $this->setPageName('user.edit|User View');
        //Gate::authorize('update', $idea);

        return view('pages.users.edit',[
            'mode' => 'view',
            'user' => $user,
        ]);
    }

    public function edit(User $user)
    {
        $this->setPageName('user.edit|User Creat');
        //Gate::authorize('update', $idea);

        return view('pages.users.edit',[
            'mode' => 'edit',
            'user' => $user,
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
}
