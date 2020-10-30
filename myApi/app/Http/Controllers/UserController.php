<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\User;
use App\Providers\UserServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public $userProvider;
    
    public function __construct() {
        $this->userProvider = new UserServiceProvider();
    }

    //hello world test
    public function hello(Request $request) {
        return response()->json([
            "message" => "Hello World",
            "status" => "SUCCESS"
        ], 201);
    }
    
    //register method
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:user',
            'mail' => 'required|string|email|max:255|unique:user',
            'pseudo' => 'string|max:255|unique:user',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                    "message" => $validator->errors()->first(),
                    "code" => 400,
                    "data" => []
            ], 400);
        }

        $user = User::create([
            'username' => $request->get('username'),
            'mail' => $request->get('mail'),
            'pseudo' => $request->get('pseudo'),
            'password' => hash('sha256', $request->get('password')),
        ]);

        $token = $user->createToken('MyApp')->accessToken;

        return response()->json([
            "data" => [ 
                "token" => $token, 
                "user" => $user 
            ],
            "message" => "OK"
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'username' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json([
                "message" => $validator->errors()->first(),
                "code" => 400,
                "data" => []
            ], 400);
        }
        
        if($user = User::whereUsername(request('username'))
        ->wherePassword(hash('sha256', request('password')))
        ->first()){
            $result['token'] = $user->createToken('MyApp')->accessToken;
            return response()->json([
                "data"=>$result,
                "message"=>"OK"
            ], 201);
        }

        return response()->json([
            "message" => "Not found",
        ], 404);
    }
        
    //Logout
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
         return response()->json(['succes'=>true, 'status'=>200]);
    }

    //delete User
    public function delete($id)
    {
        $user = Auth::user();

        if ($user->id != $id) {
            return response()->json([
                "message" => 'Unauthorized',
            ], 401);
        }

        if($user->delete()) {
            return response()->json([
                'message'=>'User deleted', 
                'status'=>200
            ], 200);
        }

        return response()->json([
            'message'=>'Bad request', 
            'code'=>400,
            'data' => []
        ], 400); 
    }

    //list all users
    public function getListUser(Request $request)
    {
        if($users = $this->userProvider->getAll())
        {
            return response()->json([
                'data'=> $users,
                'status' => 200,
                'message' => "OK"
            ], 200);
        }
        return response()->json([
            'message'=> 'Users not found'
        ], 400);
    }

    //Update
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [ 
            'username' => 'nullable|string|unique:user',
            'mail' => 'nullable|email|unique:user',
            'pseudo' => 'nullable|string|unique:user',
            'password' => 'nullable|string'
        ]);

        if($validator->fails()) {
                return response()->json([
                    'error'=>$validator->errors(),
                    'code' => 400,
                    'data' => []
                ], 400);
            }

        $input = $request->all();
        if($input['password']) {
            $input['password'] = hash('sha256', $input['password']);
        }

        if ($user->id != $id) {
            return response()->json([
                "message" => 'Unauthorized',
            ], 401);
        }

        if($user->update($input))
        {
            return response()->json([
                'message'=>'OK',
                'data' => $user
            ], 200);;
        }
        return response()->json([
            'message'=>'Bad Request',
            'status' => 400
        ], 400);;
    }
}
