<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;
use App\Mail\VerifyEmail;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function register(Request $request)
    {
        $validate = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'dob' => 'required|date|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ]);

        if($validate) {
            $user = User::create([
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'phone_number' => $request['phone_number'],
                'dob' => $request['dob'],
                'email' => $request['email'],
                'password' => bcrypt($request['password']),
                'role' => 'user',
                'account_status' => 1,
                'is_verified' => 0
            ]);
            $token = $user->createToken('BucketToursToken')->plainTextToken;
            $link = "http://localhost:3000/verify/$user->email/token=$token";
            Mail::to($user->email)->send(new VerifyEmail($user, $link));
            DB::commit();
            return response()->json([
                "message" => "Account Created Successfully"
            ], 200);
        }
    }

    public function OauthRegistration(Request $request)
    {
        $validate = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ]);

        if($validate) {
            $user = User::where('email', $request['email'])->first();
            if($user == null) {
                try {
                    $user = User::create([
                        'first_name' => $request['first_name'],
                        'last_name' => $request['last_name'],
                        'email' => $request['email'],
                        'password' => bcrypt($this->generateRandomString()),
                        'role' => 'user',
                        'account_status' => 1,
                        'is_verified' => 1,
                        'email_verified_at' => now()
                    ]);
                    $token = $user->createToken('BucketToursToken')->plainTextToken;
                    DB::commit();
                    $response = [
                        'user' => $user,
                        'accessToken' => $token,
                    ];
                    return response()->json($response,  200);
                } catch (\Throwable $th) {
                    throw $th;
                }
            } else {
                $token = $user->createToken('BucketToursToken')->plainTextToken;
                $response = [
                    'user' => $user,
                    'accessToken' => $token,
                ];
                return response()->json($response,  200);
            }
        }
    }

    private function generateRandomString() {
        $length = 10;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString = $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function login(Request $request) {

        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response('Incorrect username or password.', 401);
        }

        $token = $user->createToken('BucketToursToken')->plainTextToken;
        if($user->is_verified == "1") {
            if($user->account_status == "1") {
                $response = [
                    'user' => $user,
                    'accessToken' => $token,
                ];
            } else {
                return response('Account deactivated. Please contact administrator.', 401);
            }
        } else {
            return response('Email is not verified.', 401);
        }
        return response($response);
    }

    public function logout()
    {
        if(isset(Auth::user()->id)){
            Auth::user()->tokens()->where('id', Auth::user()->currentAccessToken()->id)->delete();
            return response()->json([
                'status' => true
            ], 200);
        }else{
            return response()->json([
                'status' => false
            ], 401);
        }
    }

    public function verifyToken(Request $request)
    {
        if(isset(Auth::user()->id)){
            return response()->json([
                'status' => true
            ], 200);
        }else{
            return response()->json([
                'status' => false
            ], 401);
        }
    }

    public function verifyEmail(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
        ]);
        $user = User::where('email', $fields['email'])->first();
        if($user){
            $user->is_verified = true;
            $user->email_verified_at = now();
            $user->save();
            return response()->json("Email verified", 200);
        }else{
            return response()->json("Email not found", 404);
        }

    }

    public function resendVerifyEmail(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
        ]);
        // Check email
        $user = User::where('email', $fields['email'])->first();

        if($user){
            $token = $user->createToken('BucketToursToken')->plainTextToken;
            $link = "http://localhost:3000/verify/$user->email/token=$token";
            Mail::to($user->email)->send(new VerifyEmail($user, $link));
            return response()->json([
                'message' => "Verification email sent",
                'token' => $token
            ], 200);
        }else{
            return response()->json("Email not found", 404);
        }

    }

    public function changePassword(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        // Check email
        $user = User::where('email', $fields['email'])->first();
        if($user){
            // Check password
            if(!Hash::check($fields['current_password'], $user->password)) {
                return response('Incorrect current password.', 401);
            }

            $user->password = Hash::make($fields['new_password']);
            $user->save();

            return response()->json("Password updated successfully", 200);
        } else {
            return response()->json("Email not found", 404);
        }
    }

    public function forgotPassword(Request $request) {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return [
                'message' => __($status),
                'status' => 200
            ];
        }

        return [
            'message' => trans($status),
            'status' => 422
        ];
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'password_confirmation' => 'required'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response('Password reset successfully', 200);
        }

        return response([
            'message'=> __($status)
        ], 500);

    }
}
