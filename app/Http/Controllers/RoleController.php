<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

//Importing laravel-permission models

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(
            [
                'auth',
                'XSS',
            ]
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage Roles'))
        {
            $roles = Role::where('created_by', '=', \Auth::user()->id)->get();

            return view('roles.index')->with('roles', $roles);
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
        if(\Auth::user()->can('Create Role'))
        {
            $user = \Auth::user();
            if($user->type == 'Super Admin')
            {
                $permissions = Permission::all()->pluck('name', 'id')->toArray();//Get all permissions
            }
            else
            {
                $permissions = new Collection();
                foreach($user->roles as $role)
                {
                    $permissions = $permissions->merge($role->permissions);
                }
                $permissions = $permissions->pluck('name', 'id')->toArray();
            }

            return view('roles.create', ['permissions' => $permissions]);
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
        if(\Auth::user()->can('Create Role'))
        {
            $role = Role::where('name','=', $request->name)->first();

            if(isset($role))
            {

                return redirect()->back()->with('error', __('The Role has Already Been Taken.'));
            }
            //Validate name and permissions field
            $this->validate(
                $request, [
                            //                            'name' => 'required|unique:roles|max:10',
                            'name' => 'required|max:100|unique:roles,name,NULL,id,created_by,' . \Auth::user()->ownerId(),
                            'permissions' => 'required',
                        ]
            );

            $name       = $request['name'];
            $role       = new Role();
            $role->name = $name;

            $user = \Auth::user();
            if($user->type == 'Super Admin' || $user->type == 'Owner')
            {
                $role->created_by = $user->id;
            }
            else
            {
                $role->created_by = $user->created_by;
            }

            $permissions = $request['permissions'];

            $role->save();
            //Looping thru selected permissions
            foreach($permissions as $permission)
            {
                $p = Permission::where('id', '=', $permission)->firstOrFail();
                //Fetch the newly created role and assign permission
                $role = Role::where('name', '=', $name)->first();
                $role->givePermissionTo($p);
            }

            return redirect()->route('roles.index')->with(
                'success', __('Role successfully created!')
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
        return redirect('roles');
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
        if(\Auth::user()->can('Edit Role'))
        {
            $user = \Auth::user();
            if($user->type == 'Super Admin')
            {
                $permissions = Permission::all()->pluck('name', 'id')->toArray();//Get all permissions
            }
            else
            {
                $permissions = new Collection();;
                foreach($user->roles as $role)
                {
                    $permissions = $permissions->merge($role->permissions);
                }
                $permissions = $permissions->pluck('name', 'id')->toArray();
            }
            $role = Role::findOrFail($id);

            //            $permissions = Permission::all();
            return view('roles.edit', compact('role', 'permissions'));
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
        if(\Auth::user()->can('Edit Role'))
        {
            $role = Role::findOrFail($id);//Get role with the given id
            //Validate name and permission fields
            $this->validate(
                $request, [
                            // 'name' => 'required|max:10|unique:roles,name,' . $id,
                            'name' => 'required|max:100|unique:roles,name,' . $id . ',id,created_by,' . \Auth::user()->ownerId(),
                            'permissions' => 'required',
                        ]
            );

            $input       = $request->except(['permissions']);
            $permissions = $request['permissions'];
            $role->fill($input)->save();

            $p_all = Permission::all();//Get all permissions

            foreach($p_all as $p)
            {
                $role->revokePermissionTo($p); //Remove all permissions associated with role
            }

            foreach($permissions as $permission)
            {
                $p = Permission::where('id', '=', $permission)->firstOrFail(); //Get corresponding form //permission in db
                $role->givePermissionTo($p);  //Assign permission to role
            }

            return redirect()->route('roles.index')->with(
                'success', __('Role successfully updated!')
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
        if(\Auth::user()->can('Delete Role'))
        {
            $role = Role::findOrFail($id);
            $role->delete();

            return redirect()->route('roles.index')->with(
                'success', __('Role successfully deleted!')
            );
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
