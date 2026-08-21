<?php

namespace App\Http\Controllers;

use CURLFile;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'age' => 'required|string',
            'dist_country' => 'required|string',
            'gender' => 'required|string',
            'phone_num' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'age' => $request->age,
            'dist_country' => $request->dist_country,
            'gender' => $request->gender,
            'role' => 'user',
            'phone_num' => $request->phone_num,
        ]);

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'User registered successfully. Please verify your email before logging in.',
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
        'email'=>['required','string','email'],
        'password'=>['required','string'], 
       ]);
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'error' => 'Invalid email or password'
        ], 401);
    }

    if (is_null($user->email_verified_at)) {
        return response()->json([
            'message' => 'Please verify your email before logging in.'
        ], 403);
    }

    $credentials = $request->only('email', 'password');

    if (!$token = auth('api')->attempt($credentials)) {
        return response()->json([
            'error' => 'Invalid email or password'
        ], 401);
    }


        return response()->json([
          'message'=>'User logged in successfully',
          'token'=>$token,
          'token_type'=>'Bearer',
          'expires_in'=>JWTAuth::factory()->getTTL()*60
        ]);
    }

    public function me()
    {
        $user = Auth::user();

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'age' => $user->age,
            'dist_country' => $user->dist_country,
            'gender' => $user->gender,
            'phone_num' => $user->phone_num,
        ]);
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        $token=auth('api')->refresh();
        return response()->json([
            'message'=>'Token refreshed successfully',
            'token'=>$token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
    public function forgetPassword(Request $request){
    $request->validate([
        'email'=>['required','email','exists:users,email']
    ]);
    $status=Password::sendResetLink($request->only('email'));
    if($status==Password::RESET_LINK_SENT){
        return response()->json([
          'message'=>$status]);
    }
    return response()->json(["message"=>$status],422);
  }

  public function resetPassword(Request $request){
        $request->validate([
            'email'=>'required|email',
            'token'=>'required',
            'password' =>'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password','password_confirmation','token'),function(User $user,string $password) {
                $user->update([
                    'password' => Hash::make($password)
                ]); }
        );

        return response()->json(["message" => $status], $status===Password::PASSWORD_RESET ?200:422);
    }
        public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['error' => 'Invalid verification link'], 403);
        }

        if (!URL::hasValidSignature($request)) {
            return response()->json(['error' => 'Verification link expired or invalid'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified']);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['message' => 'Email verified successfully']);
    }

    public function resendVerification(Request $request)
    {
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $user = User::where('email', $request->email)->first();

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified'], 400);
    }

    $user->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification link resent']);

    }

    public function verificationStatus(Request $request)
    {
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $user = User::where('email', $request->email)->first();

    return response()->json([
        'verified' => $user->hasVerifiedEmail(),
    ]);
    }
}