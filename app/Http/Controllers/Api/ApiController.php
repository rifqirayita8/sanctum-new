<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function Register(Request $request){

        //iki validator
        try{
            $rules = [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => [
                    'required',
                    'min:8',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                ],
            ]; 
            
            $messages= [
                'name.required' => 'Name is required.',
                'email.required' => 'Email is required',
                'email.email' => 'Email is not valid',
                'email.unique' => 'Email is already taken',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 8 characters',
                'password.regex' => 'Password must contain at least one uppercase, one lowercase, and one number',
            ];

            $validateUser= Validator::make($request->all(), $rules, $messages);

            if($validateUser->fails()){
                return response() -> json([
                    'status' => false,
                    'message' => implode(', ',$validateUser->errors()->all()),
                    'errors' => $validateUser->errors()
                ],422);
            }

            //iki create model user e
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(10),
                'role' => 'mahasiswa',
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
                'password' => 'required',
            ], 
            [
                'email.required' => 'Email cannot be empty.',
                'email.email' => 'Email is not valid',
                'password.required' => 'Password cannot be empty.',
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

        try {
            $user= Auth::user();

            $validateUser= Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|max:255|unique:users,email,'.$user->id,
                'password' => 'sometimes|min:6',
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

        } catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'Failed to update user.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    
    public function index(){
        try {
            $users= User::all();

            return response()->json([
                'status' => true,
                'message' => 'User retrieve succesfully',
                'data' => $users,
            ], 200);

        } catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve users.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }



    public function profile(){
        try{
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

        } catch(\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve user profile',
                'error' => $th->getMessage(),
            ], 500);
        }

    }



    public function logout(){
        try {
            $user= auth()->user();

            if ($user instanceof \App\Models\User) {
                $user->currentAccessToken()->delete();
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


    public function delete(){
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

    public function getCurrentUser(Request $request) {
        $user = Auth::user();

        return response()->json([
            'name' => $user->name,
        ]);
    }
}
