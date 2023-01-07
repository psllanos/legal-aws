<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

//Importing laravel-permission models

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage Permissions'))
        {
            $permissions = Permission::all(); //Get all permissions

            return view('permissions.index')->with('permissions', $permissions);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->can('Create Permission'))
        {
            $roles = Role::where('created_by', '=', \Auth::user()->id)->get(); //Get all roles

            return view('permissions.create')->with('roles', $roles);
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->can('Create Permission'))
        {
            $this->validate(
                $request, [
                            'name' => 'required|max:40',
                        ]
            );

            $name             = $request['name'];
            $permission       = new Permission();
            $permission->name = $name;

            $roles = $request['roles'];

            $permission->save();

            if(!empty($request['roles']))
            { //If one or more role is selected
                foreach($roles as $role)
                {
                    $r = Role::where('id', '=', $role)->firstOrFail(); //Match input role to db record

                    $permission = Permission::where('name', '=', $name)->first(); //Match input //permission to db record
                    $r->givePermissionTo($permission);
                }
            }

            return redirect()->route('permissions.index')->with(
                'success', __('Permission successfully created!')
            );
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect('permissions');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(\Auth::user()->can('Edit Permission'))
        {
            $permission = Permission::findOrFail($id);

            return view('permissions.edit', compact('permission'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('Edit Permission'))
        {
            $permission = Permission::findOrFail($id);
            $this->validate(
                $request, [
                            'name' => 'required|max:40',
                        ]
            );
            $input = $request->all();
            $permission->fill($input)->save();

            return redirect()->route('permissions.index')->with(
                'success', __('Permission successfully updated!')
            );
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(\Auth::user()->can('Delete Permission'))
        {
            $permission = Permission::findOrFail($id);

            //Make it impossible to delete this specific permission
            if(in_array(
                $permission->name, [
                                     'Manage Permissions',
                                     'Create Permission',
                                     'Edit Permission',
                                     'Delete Permission',
                                 ]
            ))
            {
                return redirect()->route('permissions.index')->with(
                    'error', __('Cannot delete this Permission!')
                );
            }

            $permission->delete();

            return redirect()->route('permissions.index')->with(
                'success', __('Permission successfully deleted!')
            );
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
