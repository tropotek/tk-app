<?php

namespace App\Http\Controllers\Examples\Forms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tk\Support\Facades\Breadcrumbs;

class OneController extends Controller
{

    protected array $values = [
        'title' => 'mrs',
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john.doe@example.com',
        'gender' => 'male',
        'address' => '123 Main Street',
        'active' => '1',
        'groups' => ['1', '3'],
        'options' => ['option1', 'option3'],
        'options2' => 'option3',
        'description' => 'This is a test description'
    ];


    public function index(Request $request)
    {
        Breadcrumbs::push('form one|Form One View');
        return view('pages.examples.forms.one', [
            'mode' => 'view',
            'values' => $this->values,
        ]);
    }

    public function edit(Request $request)
    {
        Breadcrumbs::push('form one|Form One Edit');
        return view('pages.examples.forms.one', [
            'mode' => 'edit',
            'values' => $this->values,
        ]);
    }

    public function create(Request $request)
    {
        Breadcrumbs::push('form one|Form One Create');
        return view('pages.examples.forms.one', [
            'mode' => 'create'
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'firstName' => 'required|min:3|max:20',
            'lastName' => 'required|min:3|max:20',
            'email' => 'required|email',
            'gender' => 'required',
//            'address' => 'required',
//            'image' => 'image|max:2048',
        ]);

        vd($request->all());

        return redirect('/examples/formOne')->with('success', 'Form submitted successfully!');
    }

}
