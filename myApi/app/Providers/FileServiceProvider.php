<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;

class FileServiceProvider
{
    public function getAll()
    {
        $file = DB::table('file')
        ->get();
        return $file;
    }

    public function getFilebyId($id) {
        $file = DB::table('files')
        ->where('id', $id)
        ->get();
        return $file;
    }

    public function getAllByParam($param)
    {
        $file = DB::table('file')
        ->where('name', 'like', '%'.$param['name'].'%')
        ->orWhere('user_id', (int)$param['user'])
        ->take($param['perPage'])
        ->paginate($param['page']);
        return $file;
    }

    public function getById($id)
    {
        $file = DB::table('files')
        ->where('user_id', $id)
        ->get();
        return $file;
    }

    public function getfileByIdAndUser($id, $user)
    {
        $file = DB::table('files')
        ->where([
            ['id','=', $id], 
            ['user_id', '=', $user]
            ])
        ->get();
        return $file;
    }

    public function deleteFile($id, $user) {
        $file = DB::table('files')->where([
            ['id', '=', $id],
            ['user_id', '=', $user]
            ])->delete();
        return $file;
    }

    public function insert($id, $name, $path)
    {
        $get = DB::table('files')->insert([
            'name'=>$name,
            'user_id'=>$id,
            'path' => $path
        ]);
        return $get;
    }

    public function updated($array) {
        $put = DB::table('files')->where([
            ['id', '=', $array['id']],
            ['user_id', '=', $array['user_id']]
            ])->update(array(
                'name'=>$array['name'],
                'path'=>$array['path'],
                'updated_At' => date("Y-m-d H:i:s") 
            ));

        return $put;
    }
}