<?php

namespace App\Http\Controllers\permission;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        //
        $q=request('query');
        $permissions=Permission::where('name', 'like', '%' .$q. '%')
        ->orderBy('name','ASC')
        ->with('roles:id,name','users:id,name')->paginate(env('PAR_PAGE'));

        $roles=Role::select('id','name')->orderBy('name','ASC')->get();
        $users=User::select('id','name')->orderBy('name','ASC')->get();

        return response()->json(['permissions'=>$permissions,'roles'=>$roles,'users'=>$users]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255','unique:permissions'],
        ]);
        $user_collection = User::WhereIn('id',  $request->users)->get();
        $role_collection = Role::WhereIn('id',  $request->roles)->get();
        $permission = Permission::create(['name' => $request->name]);
        $permission->syncRoles($role_collection);
        $permission->users()->attach($user_collection);
        // $permission->roles()->attach($roles_collection);
        return response()->json($permission);
    }

    public function update(Request $request)
    {
        //
        $request->validate([
            'name' => ['required', 'string', 'max:255','unique:permissions,name,'.$request->id],
        ]);

        $user_collection=User::WhereIn('id',  $request->users)->get();
        $role_collection=Role::WhereIn('id',  $request->roles)->get();
        $permission = Permission::find($request->id);
        $permission->name=$request->name;
        $permission->save();
        $permission->roles()->sync($role_collection);
        // $permission->syncRoles($role_collection);
        $permission->users()->sync($user_collection);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::destroy($id);
        return response()->json($permission);
        //
    }
}
