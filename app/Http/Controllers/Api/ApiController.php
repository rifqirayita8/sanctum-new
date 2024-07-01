<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function Register(Request $request){

        //iki validator
        try{
            $validateUser= Validator::make($request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|min:6',
            ]);

            if($validateUser->fails()){
                return response() -> json([
                    'status' => false,
                    'message' => 'Validation Error.',
                    'errors' => $validateUser->errors()
                ],422);
            }

            //iki create model user e
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => $request->password,
                'remember_token' => Str::random(10),
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
                ],422);
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



    public function update(Request $request){
        $user= Auth::user();

        $validateUser= Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => 'required|min:6',
        ]);

        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Validation Error.',
                'errors' => $validateUser->errors(),
            ], 422);
        }

        $data= $request->only('name','email');
        if($request->filled('password')){
            $data['password']=Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully.',
            'data' => $user,
        ], 200);
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


    public function delete(Request $request){
        try {
            $user= auth()->user();

            if($user instanceof \App\Models\User){
                $user->tokens()->delete();
                $user->delete();
            }

            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully.',
                'data' => [],
            ], 200);
        } catch(\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
