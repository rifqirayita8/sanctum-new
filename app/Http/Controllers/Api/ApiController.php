<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function Register(Request $request){

        //iki validator
        try{
            $validateUser= Validator::make($request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
            ]);

            if($validateUser->fails()){
                return response() -> json([
                    'status' => false,
                    'message' => 'Validation Error.',
                    'errors' => $validateUser->errors()
                ],401);
            }

            //iki create model user e
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            //iki token e
            return response()->json([
                'status' => true,
                'message' => 'User created successfully.',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ],200);

        } catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ],500);
        }
    }


    public function Login(Request $request){
        try{
            //iki validator 
            $validateUser= Validator::make($request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            if($validateUser->fails()){
                return response() -> json([
                    'status' => false,
                    'message' => 'Validation Error.',
                    'errors' => $validateUser->errors()
                ],401);
            }

            //iki auth e
            if(!Auth::attempt($request->only(['email','password']))){
                return response()->json([
                    'status' => false,
                    'message' => "Email & Password don't match with our record.",
                ],401);
            }

            //iki lek onok email dobel, dianu sing paling disek keindeks
            $user = User::where('email',$request->email)->first();

            //iki ngekei token user
            return response()->json([
                'status' => true,
                'message' => 'User logged successfully.',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ],200);


        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ],500);          
        }
    }

    public function profile(){
        $userData = auth()->user();

        if($userData instanceof \App\Models\User){
            return response()->json([
                'status' => true,
                'message' => 'Profile Information.',
                'data' => $userData
            ],200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found.',
            ],404);
        }
    }

    public function logout(){
        try {
            $user= auth()->user();

            if ($user instanceof \App\Models\User) {
                $user->tokens()->delete();
            }

            return response()->json([
                'status' => true,
                'message' => 'User logged out successfully.',
                'data' => []
            ], 200);
        } catch(\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
