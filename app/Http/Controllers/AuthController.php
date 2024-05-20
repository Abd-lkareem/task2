<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\models\User;    
use App\Http\Requests;
use Illuminate\Support\Str; 
use App\Events\UserRegistered;
use Carbon\Carbon;
use App\Traits\FileOperations;
use App\Traits\Responses;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignUpRequest;
use App\Http\Requests\VerifyRequest;



class AuthController extends Controller
{
    use FileOperations , Responses;

    public function signup(SignUpRequest $request)
    {
        $imagePath = $this->upload($request->file('profile_photo'), "test_name", 'images');
        $pdfPath = $this->upload($request->file('certificate'), null, 'pdfs');
        $code = $this->generate_code();
            
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number , 
            'profile_photo' => $imagePath , 
            'certificate' => $pdfPath ,
            'code' => $code , 
            'code_sent_at' => now()
        ]);
    
        event(new UserRegistered($user));

        $message = 'a message sent to your email box to verify your eamil';

        return $this->apiSuccess(['user' => $user] , $message , 201);
    
    }
    
    public function verify(VerifyRequest $request , User $user)
    {
        $code = $request->code;
        $message = "the code that you entered is incorrect";

        if($user->code == $code)
        {
            $timestamp1 = Carbon::parse($user->code_sent_at);
            $timestamp2 = Carbon::now(); 
            $minuteDiff = $timestamp1->diffInMinutes($timestamp2);

            if($minuteDiff <= 3)
            {
                $data = ['token'=> $user->createToken('access_token', ['access-api'], Carbon::now()->addMinutes(config('sanctum.ac_expiration'))) ,
                'refresh_token '=>$user->createToken('refresh_token', ['issue-access-token'], Carbon::now()->addMinutes(config('sanctum.rt_expiration')))];

                $message = "your verification done succesufully";
                $user->update([
                    'email_verified_at' => now()
                ]);

                
                return $this->apiSuccess($data , $message , 200);

            }

            $message = "the code is not valid now";
                
        }
            return $this->apiError(message:$message);

        
    }

    public function login(LoginRequest $request)
    {
        $credentials = [
            'email' => request('email'),
            'password' => request('password'),
        ];
        $message =  "password or email are invalid";

        if (auth::attempt($credentials)) {
            if(auth()->user()->email_verified_at)
                return $this->apiSuccess(data:['token' => $user->createToken('authToken')->plainTextToken]);

            
            event(new UserRegistered($user));
            $message =  "a messaeg sent to your email box to verify your eamil";
            return $this->apiSuccess(message:$message);

        }

        return $this->apiError(message:$message);
            
        
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        $message = 'User logged out successfully';

        return $this->apiSuccess(message: $message );

    }

    public function refreshToken(Request $request)
    {
        $accessToken = $request->user()->createToken('access_token', ['access-api'], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
        $message = "Token refreshed succesufully ";
        $data= ['token' => $accessToken->plainTextToken];

        return $this->apiSuccess($data , $message );
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
