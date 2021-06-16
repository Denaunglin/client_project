<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\FontConvert;
use App\Helper\OTP;
use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiLoginRequest;
use App\Http\Requests\ApiRegisterRequest;
use App\Http\Requests\forgotPasswordApiRequest;
use App\Mail\PasswordResetMail;
use App\Mail\PasswordResetSuccessMail;
use App\Models\OneSignalSubscriber;
use App\Models\OTP_Code;
use App\Models\User;
use App\Models\UserNrcPicture;
use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Storage;

class AuthController extends Controller
{
    protected $guard_name = 'web';
    use SendsPasswordResetEmails;

    public function Register(ApiRegisterRequest $request)
    {
        try {
            if ($request->hasFile('front_pic')) {
                $image_file_front = $request->file('front_pic');
                $image_name_front = time() . '_' . uniqid() . '.' . $image_file_front->getClientOriginalExtension();
                Storage::put(
                    'uploads/gallery/' . $image_name_front,
                    file_get_contents($image_file_front->getRealPath())
                );
                $file_path = public_path('storage/uploads/gallery/' . $image_name_front);
                $optimizerChain = OptimizerChainFactory::create();
                $optimizerChain->setTimeout(10)->optimize($file_path);
            }

            if ($request->hasFile('back_pic')) {
                $image_file_back = $request->file('back_pic');
                $image_name_back = time() . '_' . uniqid() . '.' . $image_file_back->getClientOriginalExtension();
                Storage::put(
                    'uploads/gallery/' . $image_name_back,
                    file_get_contents($image_file_back->getRealPath())
                );
                $file_path = public_path('storage/uploads/gallery/' . $image_name_back);
                $optimizerChain = OptimizerChainFactory::create();
                $optimizerChain->setTimeout(10)->optimize($file_path);
            }

            $user = new User();
            $user->name = FontConvert::zg2uni($request->name);
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->nrc_passport = $request->nrc_passport;
            $user->date_of_birth = $request->date_of_birth;
            $user->gender = $request->gender;
            $user->address = FontConvert::zg2uni($request->address);
            $user->account_type = '1';
            $user->password = Hash::make($request->password);
            $user->save();

            $usernrcimage = new UserNrcPicture();
            $usernrcimage->user_id = $user->id;
            $usernrcimage->front_pic = $image_name_front;
            $usernrcimage->back_pic = $image_name_back;
            $usernrcimage->save();

            $token = $user->createToken('ApexHotel')->accessToken;

            return ResponseHelper::success($token);
        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Somethind wrong. ' . $e->getMessage());
        }
    }

    public function Login(ApiLoginRequest $request)
    {
        try {
            $data = $request->only('email', 'password');
            if (Auth::attempt($data)) {
                $user = Auth::user();
                $token = $user->createToken('ApexHotel')->accessToken;
                return ResponseHelper::success($token);
            }

            return ResponseHelper::failedMessage('These credentials do not match our records.');
        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something wrong. ' . $e->getMessage());
        }
    }

    public function forgotPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ResponseHelper::failedMessage('Couldn\'t find your account.');
        }

        try {
            $email = $user->email;

            if ($this->sendResetEmail($email)) {
                $email = $request->email ? $request->email : null;
                $otp_interval_time = 60; //seconds
                $data = ['email' => $email, 'otp_interval_time' => $otp_interval_time];

                return ResponseHelper::success($data, 'The reset link has been sent to your email address');

            } else {
                return ResponseHelper::failedMessage('Network Error occurred. Please try again.');
            }
        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something wrong. ' . $e->getMessage());
        }
    }

    //send reset mail

    private function sendResetEmail($email)
    {
        $user = User::where('email', $email)->select('name', 'email')->first();

        $toEmail = $user->email;
        $name = $user->name;
        $link = null;

        $OTP = OTP::generateOtP();
        if ($OTP) {
            $otp_code = new OTP_Code();
            $otp_code->email = $user->email;
            $otp_code->otp = $OTP;
            $otp_code->expire_at = Carbon::now()->addMinutes(5)->timestamp;
            $otp_code->save();
        }

        try {

            $data = [
                "name" => $name,
                "OTP" => $OTP,
            ];

            Mail::to($toEmail)->send(new PasswordResetMail($link, $data));

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    //sendsuccess mail

    private function sendSuccessEmail($email)
    {
        $user = User::where('email', $email)->select('name', 'email')->first();
        $toEmail = $email;

        try {
            Mail::to($toEmail)->send(new PasswordResetSuccessMail());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    //password update

    public function resetPassword(forgotPasswordApiRequest $request)
    {
        $password = $request->password;
        $otp_code = OTP_Code::where('email', $request->email)->where('otp', $request->otp_code)->first();
        if ($otp_code) {

            if ($otp_code->isExpired()) {

                $user = User::where('email', $request->email)->first();
                $password = $request->password;

                if ($user) {
                    $user->password = \Hash::make($password);
                    $user->update();

                    Auth::login($user);

                    if ($this->sendSuccessEmail($user->email)) {
                        $token = $user->createToken('ApexHotel')->accessToken;
                        return ResponseHelper::success(['token' => $token], 'Successfully reset your password.');

                    } else {

                        return response([
                            'result' => '0',
                            'message' => 'A Network Error occurred. Please try again.',
                        ]);

                    }

                }
            }

        }

        return response([
            'result' => '0',
            'message' => 'Your OTP code is invalid.',
        ]);

    }

    //get guard

    public function guard()
    {
        return Auth::guard($this->guard_name);
    }

    //logout

    public function logout(Request $request)
    {

        try {
            $this->guard()->logout();
            $user = Auth::user();

            if ($request->signal_id) {
                $signal_id = OneSignalSubscriber::where('user_id', $user->id)->where('signal_id', $request->signal_id)->first();
                if ($signal_id) {
                    $signal_id->delete();
                } else {
                    return ResponseHelper::failedMessage('Signal_id does not match our records !');
                }
            }

            Auth::user()->token()->revoke();

            return ResponseHelper::successMessage('Logout Successsully !');

        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong' . $e->getMessage());
        }
    }

    //resend otp

    public function resendOTP(Request $request)
    {
        try {

            $get_otp = OTP_Code::where('email', $request->email)->get();
            $getotp = $get_otp->last();

            if (time() > $getotp->created_at->addMinutes(1)->timestamp) {

                $email = $request->email ? $request->email : null;
                $user = User::where('email', $email)->first();

                $toEmail = $user->email;
                $name = $user->name;
                $link = null;

                $OTP = OTP::generateOtP();
                if ($OTP) {
                    $otp_code = new OTP_Code();
                    $otp_code->email = $user->email;
                    $otp_code->otp = $OTP;
                    $otp_code->expire_at = Carbon::now()->addMinutes(5)->timestamp;
                    $otp_code->save();
                }
                $data = [
                    "name" => $name,
                    "OTP" => $OTP,
                ];
                Mail::to($toEmail)->send(new PasswordResetMail($link, $data));
                $errors = null;
                $email = $request->email;
                $otp_interval_time = 60; //second

                $data = ["email" => $email, "otp_interval_time" => $otp_interval_time];

                return ResponseHelper::success($data, 'OTP (One Time Passcode) code has been sent to ' . $email . '. It will expire in 5 mins.Please enter the OTP in the field below to verify.');

            } else {

                $errors = "OTP cann't resend right now , please wait for a minute ! ";
                $email = $request->email;
                $otp_interval_time = 60; //second
                $data = ["email" => $email, "otp_interval_time" => $otp_interval_time];

                return ResponseHelper::fail($data, "OTP can'nt resend right now , please wait for a minute ! ");

            }

        } catch (\Exception $e) {
            return ResponseHelper::failedMessage("Something Wrong!" . $e->getMessage());
        }

    }

}
