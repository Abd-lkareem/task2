<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\models\User;    
use Illuminate\Support\Str; 
use App\Events\UserRegistered;
use Carbon\Carbon;


class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|unique:users|email',
            'phone_number' => 'required|numeric',
            'password' => 'required|min:7|confirmed',
            'username' => 'required|string' ,
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
            'certificate' => 'required|mimes:pdf|max:2048', 
        ]);
        if ($validator->fails()) 
            return $validator->errors();
    
        $validated = $validator->validated();
        
        $imagePath =  $request->file('profile_photo')->store('images', 'public');
        $pdfPath = $request->file('certificate')->store('pdfs', 'public');
        $code = $this->generate_code();

        
    
        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'] , 
            'profile_photo' => $imagePath , 
            'certificate' => $pdfPath ,
            'code' => $code , 
            'code_sent_at' => now()
        ]);
    
        event(new UserRegistered($user));

    
        return response()->json([
            'user' => $user , 
            'message'=>'a messaeg sent to your email box to verify your eamil'], 201);
    }
    
    public function verify(Request $request , User $user)
    {
        $validator = Validator::make($request->all(),[
            'code' => 'required|string|size:6'
        ]);

        if ($validator->fails()) 
            return $validator->errors();
    
        $validated = $validator->validated();
        $code = $validated['code'];

        $message = "the code that you entered is incorrect";

        if($user->code == $code)
        {
            $timestamp1 = Carbon::parse($user->code_sent_at);
            $timestamp2 = Carbon::now(); 
            $minuteDiff = $timestamp1->diffInMinutes($timestamp2);

            if($minuteDiff <= 3)
            {

                $accessToken = $user->createToken('access_token', ['access-api'], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
                $refreshToken = $user->createToken('refresh_token', ['issue-access-token'], Carbon::now()->addMinutes(config('sanctum.rt_expiration')));
        

                $user->update([
                    'email_verified_at' => now()
                ]);

                return  response()->json([
                    'token' => $accessToken->plainTextToken,
                    'refresh_token' => $refreshToken->plainTextToken,
                    "messgae" => "your verification done succesufully" ]   , 200);
            }

            $message = "the code is not valid now";
                
        }

        return response()->json([
            'message'=>$message], 403);
        
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|exists:users,email',
            'password' => 'required|min:7',
            // 'phone_number' => 'required|exists:users,phone_number' ,
        ]);
        if ($validator->fails()) 
            return $validator->errors();

        $validated = $validator->validated();

        $user = User::where('email' , $validated['email' ])->first();

        if($user && is_null($user->email_verified_at))
        {
            event(new UserRegistered($user));
                
            return response()->json([
                'user' => $user , 
                'message'=>'a messaeg sent to your email box to verify your eamil'], 201);
        }

        if($user && Hash::check($validated['password'] , $user->password ) )
        {
            $user->tokens()->delete();
 
            return response()->json([
                'token' => $user->createToken('authToken')->plainTextToken
            ]);

        }

        return response()->json([
            'message' => "password or email are invalid"
        ], 403);
        
    }

    
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(
        [
                'message' => 'User logged out successfully'
        ] , 200);


    }

    public function refreshToken(Request $request)
    {
        $accessToken = $request->user()->createToken('access_token', ['access-api'], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
        return response(['message' => "Token refreshed succesufully ", 'token' => $accessToken->plainTextToken]);
    }

    private function generate_code()
    {
        $code = Str::random(6);
        
        if (!preg_match('~[0-9]+~', $code)) 
        {
            $index = rand(0,5);
            $code[$index] = rand(0,9);
        }

        return $code;
        
    }

}
