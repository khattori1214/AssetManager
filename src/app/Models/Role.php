<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $primaryKey = 'role_id';
    protected $fillable = [
        'role_id',
        'role_name',
    ];

    public static function role(){
         return User::join(
            'roles',
            'roles.role_id',
            '=',
            'users.role_id'
        )
            ->select(
                'users.user_id',
                'users.user_name',
                'users.email',
                'roles.role_name',
            )
            ->get();
    }
}
