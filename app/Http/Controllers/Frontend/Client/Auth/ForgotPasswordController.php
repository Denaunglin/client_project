<?php

namespace App\Http\Controllers\Frontend\Client\Auth;

use App\Helper\OTP;
use App\Http\Controllers\Controller;
use App\Http\Requests\forgotPasswordApiRequest;
use App\Mail\PasswordResetMail;
use App\Mail\PasswordResetSuccessMail;
use App\Models\OTP_Code;
use App\Models\passwordReset;
use App\Models\Rooms;
use App\Models\SliderUpload;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Validator;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
     */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm()
    {
        return view('frontend.client.auth.passwords.email');
    }

    // send email

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

    //send success mail

    private function sendSuccessEmail($email)
    {
        $toEmail = $email;

        try {

            Mail::to($toEmail)->send(new PasswordResetSuccessMail());

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    //send otp email

    public function passwordReset(Request $request)
    {

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->back()->withErrors(['email' => trans('User does not exit')]);
        }

        $email = $user->email;

        if ($this->sendResetEmail($email)) {
            $errors = null;
            $otp_interval_time = 60; // seconds

            return view('frontend.client.auth.passwords.reset', compact("email", "errors", 'otp_interval_time'));

        } else {
            return redirect()->back()->withErrors(['error' => trans('A Network Error occurred. Please try again.')]);
        }
    }

    //update password

    public function resetPassword(forgotPasswordApiRequest $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed',

        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors(['error' => 'Please complete the form']);
        }

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
                        $slider = SliderUpload::where('trash', 0)->get();
                        $rooms = Rooms::with('discount_types')->orderBy('id', 'desc')->where('trash', 0)->paginate(3);
                        $room_limit = 0;

                        if (Auth::user()) {
                            if (Auth::user()->accounttype->booking_limit = 1) {
                                $room_limit = 1;
                            }
                        }
                        return view('frontend.index', compact('rooms', 'room_limit', 'slider'));

                    } else {

                        return redirect()->back()->withErrors(['email' => trans('A Network Error occurred. Please try again.')]);
                    }

                }
            }
        }

        $errors = 'Your OTP code is invalid.';
        $email = $request->email;

        return view('frontend.client.auth.passwords.reset', compact("email", "errors"));

    }

    // resend otp

    public function resendOTP(Request $request)
    {

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
                $otp_code->expire_at = Carbon::now()->addMinutes(1)->timestamp;
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
            return view('frontend.client.auth.passwords.reset', compact("email", "errors", "otp_interval_time"));

        } else {

            $errors = "OTP cann't resend right now , please wait for a minute ! ";
            $email = $request->email;
            $otp_interval_time = 60; //second
            return view('frontend.client.auth.passwords.reset', compact("email", "errors", "otp_interval_time"));

        }

    }

}
