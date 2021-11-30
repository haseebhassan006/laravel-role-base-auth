<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function userCreateOrUpdate($request,$type="create"){

        if($type == 'create'){
            return User::create($request);
        }
        else {
            return User::where('id',$request['id'])->update($request);
        }
    }


    public function userInfoCreateOrUpdate($user,$request){
        $isuser=User::where('id',$user->id)->first();

        $request_input=[
            'user_id'=>$user->id,
            'city'=>$request->city,
            'country'=>1,
        ];
        if(!empty($isuser)){
            UserInfo::create($request_input);
        }
        else {
            UserInfo::where('user_id',$user->id)->update($request_input);
        }
    }
}
