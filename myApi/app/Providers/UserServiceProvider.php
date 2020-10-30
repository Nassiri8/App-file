<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;

class UserServiceProvider
{
    public function getAll()
    {
        $users = DB::table('user')
        ->select('id', 'username', 'pseudo', 'mail') 
        ->get();
        return $users;
    }

    public function getAllByParam($param)
    {
        $user = DB::table('user')
        ->where('username', 'like', '%'.$param['username'].'%')
        ->take($param['perPage'])
        ->paginate($param['page']);
        return $user;
    }

    public function getById($id)
    {
        $user = DB::table('user')
        ->select('id', 'username', 'pseudo', 'mail') 
        ->where('id', $id)
        ->get();
        return $user;
    }

    public function update($user, $input)
    {
        $put = DB::table('user')
        ->where('id', '=', $user)
        ->update($input);
        return $put;
    }

    public function getByIdUpdate($id)
    {
        $user = DB::table('user')
        ->where('id', $id)
        ->get();
        return $user;
    }
}