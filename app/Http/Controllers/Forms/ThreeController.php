<?php

namespace App\Http\Controllers\Forms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ThreeController extends Controller
{

    protected array $values = [
        'testId' => 22,
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

    //protected string $view = 'forms.three';
    protected string $view = 'forms.threedetail';

    public function index(Request $request)
    {
        $this->setPageTitle('form three|Form Three View');
        return view($this->view, [
            'mode' => 'view',
            'values' => $this->values,
        ]);
    }

    public function edit(Request $request)
    {
        $this->setPageTitle('form three|Form Three Edit');
        return view($this->view, [
            'mode' => 'edit',
            'values' => $this->values,
        ]);
    }

    public function create(Request $request)
    {
        $this->setPageTitle('form three|Form Three create');
        return view($this->view, [
            'mode' => 'create'
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'firstName' => 'required|min:3|max:20',
            'lastName' => 'required|min:3|max:20',
//            'email' => 'required|email',
//            'gender' => 'required',
//            'address' => 'required',
//            'image' => 'image|max:2048',
        ]);

        vd($request->all());

        return redirect('/formThree')->with('success', 'Form submitted successfully!');
    }

}
