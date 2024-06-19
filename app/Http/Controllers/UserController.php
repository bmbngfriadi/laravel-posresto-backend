<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //index
    public function index(Request $request)
    {
        //get all users with pagination
        $users = DB::table('users')
        ->when($request->input('name'), function($query, $name) {
            $query->where('name', 'like', '%' . $name .'%')
            ->orWhere('email', 'like', '%' . $name .'%');
        })
        ->paginate(10);
        return view('pages.users.index', compact('users'));
    }

    //create
    public function create()
    {
        return view('pages.users.create');
    }

    //store
    public function store(Request $request)
    {
        //validate
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,staff,user',
            ]);

        //store the request
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->role = $request->input('role');
        $user->save();

        return redirect()->route('users.index')->with('success', 'User Created Successfully');

    }

    //show
    public function show($id)
    {
        return view('pages.users.show');
    }

    //edit
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('pages.users.edit', compact('user'));
    }

    //update
    public function update(Request $request, $id)
    {
        //validate
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'role' => 'required|in:admin,staff,user',
            ]);

        //update the request
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        //if password is not empty
        if($request->password) {
            $user->password = Hash::make($request->password);
            $user->save();
            }

        return redirect()->route('users.index')->with('success', 'User Updated Successfully');

    }

    //destroy
    public function destroy($id)
    {
        //delete
        $user = User::find($id);
        $user->delete();
        //redirect
        return redirect()->route('users.index')->with('success', 'User Deleted Successfully');
    }

}
