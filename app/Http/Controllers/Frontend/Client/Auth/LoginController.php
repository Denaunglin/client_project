<?php

namespace App\Http\Controllers\Frontend\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\OneSignalSubscriber;
use App\Models\OTP_Code;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    protected $redirectTo = '/clientprofile';
    protected $maxAttempts = 3; // Default is 5
    protected $decayMinutes = 2; // Default is 1

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        $opttest = OTP_Code::all();
        $otp = $opttest->last();

        session(['link' => url()->previous()]);
        return view('frontend.client.auth.login', compact('otp'));
    }

    public function username()
    {
        return 'email';
    }

    public function logout(Request $request)
    {

        $user = Auth::user();
        $signal_id = OneSignalSubscriber::where('user_id', $user->id)->where('signal_id', $request->signal_id)->first();
        if ($signal_id) {
            $delete = $signal_id->delete();
        }
        $this->guard()->logout();
        $request->session()->invalidate();
        return $this->loggedOut($request) ?: redirect('/');
    }

    protected function authenticated(Request $request, $user)
    {
        return redirect(session('link'));
    }
}
