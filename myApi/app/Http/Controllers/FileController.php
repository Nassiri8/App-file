<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\File;
use App\Providers\FileServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    //variable fileProvider
    public $fileProvider;
    
    public function __construct() {
        $this->fileProvider = new FileServiceProvider();
    }

    //create file
    public function create(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [ 
            'source' => 'required|mimes:jpeg,bmp,png,pdf,msword',
            'name' => 'required|string'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'message'=>$validator->errors(),
                'code' => 400,
                'data' => []
            ], 400);
        }

        $dest=public_path().'/file';
        $input = $request->all(); 
        $fileName = time().'.'.$input['source']->getClientOriginalExtension();
        $input['source']->move($dest, $fileName);
        $path = $dest."/".$fileName;

        $file = file::create([
            'name' => $request->get('name'),
            'user_id'=>$id,
            'path' => $path
        ]);

        
        if($file){
            return response()->json([
                'message' => 'OK',
                'data'=>$file
            ], 200);
        }
    
        return response()->json([
            'message' => 'Unauthorized',
        ], 401);
    }


        //List file by id
        public function getListById($id)
        {
            if($files = $this->fileProvider->getById($id))
            {
                return response()->json([
                    'data'=> $files,
                    'message' => "OK"
                ], 200);
            }
            return response()->json([
                'message'=> 'Not found'
            ], 404);
        }

        //Delete file
        public function delete($id)
        {
            $user = Auth::user();
            $file = $this->fileProvider->getFilebyId($id);

            if (!isset($file)) {
                return response()->json([
                    'message' => "Not found"
                ], 404);
            } elseif ($user->id !== $file['user_id']) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            shell_exec('rm ' . $file->source);
            
            if($this->fileProvider->deleteFile($id, $user->id))
            {
                return response()->json([
                    'message'=>'file deleted' 
                ], 204);
            }
        }

        //Upadate file
        public function update(Request $request, $id)
        {
            $user = Auth::user()->id;

            $validator = Validator::make($request->all(), [ 
                'name' => 'string',
                'source' => 'mimes:jpeg,bmp,png,pdf,msword'
            ]);
    
            if($validator->fails())
                {
                    return response()->json([
                        'error'=>$validator->errors()
                    ], 400);
                }
    
            //get body
            $input = $request->all();
            //dd($input);

            //move file to directory
            $dest=public_path().'/file'; 
            $fileName = time().'.'.$input['source']->getClientOriginalExtension();
            $input['source']->move($dest, $fileName);
            $path = $dest."/".$fileName;
            $data = array(
                'id'=>$id,
                'user_id'=>$user,
                'name'=>$input['name'],
                'path'=>$path);
            
            if($file = $this->fileProvider->updated($data))
            {
                return response()->json([
                    'message'=>'OK',
                    'data' => $this->fileProvider->getFilebyId($id)
                ], 200);;
            }
            return response()->json([
                'message'=>'Unauthorized',
                'status' => 401
            ], 401);;
        }
}
