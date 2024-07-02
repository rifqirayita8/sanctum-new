<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DosenController extends Controller
{
    public function register(Request $request){
        try {
            $validateUser = Validator::make($request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|min:6',
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Error.',
                    'errors' => $validateUser->errors(),
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(10),
                'role' => 'dosen',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User created successfully.',
                'token' => $user->createToken("API TOKEN")->plainTextToken,
            ], 200);

        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function login(Request $request){
        try {
            $validateUser = Validator::make($request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|min:6',
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Error',
                    'errors' => $validateUser->errors(),
                ], 422);
            }

            if(!Auth::attempt($request->only(['email','password']))){
                return response()->json([
                    'status' => false,
                    'message' => "Email or Password don't match with our record.",
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User logged in succesfully.',
                'token' => $user->createToken("API TOKEN")->plainTextToken,
            ], 200);

        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function logout(){
        try {
            $user = auth()->user();

            if ($user instanceof \App\Models\User) {
                $user->tokens()->delete();
            }

            return response()->json([
                'status' => true,
                'message' => 'User logged out succesfully',
                'data' => [],
            ], 200);
        }

        catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function delete($id){
        try {
            $user = User::find($id);
            if(!$user){
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.',
                ], 404);
            }      

            $user->tokens()->delete();
            $user->delete();
        
            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully.',
                'data' => [],
            ], 200);

        } catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function indexDosen(){
        try {
            $user = User::where('role', 'dosen')->get();

            return response()->json([
                'status' => true,
                'message' => 'List Dosen',
                'data' => $user,
            ], 200);

        } catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function indexMhs(){
        try {
            $user = User::where('role', 'mahasiswa')->get();

            return response()->json([
                'status' => true,
                'message' => 'List Mahasiswa',
                'data' => $user,
            ], 200);

        } catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
