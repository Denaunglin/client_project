<?php

namespace App\Http\Controllers\Frontend\Client\Auth;

use App\Helper\FontConvert;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRegisterRequest;
use App\Models\User;
use App\Models\UserNrcPicture;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Storage;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {

        if (!session()->has('url.intended')) {
            session(['url.intended' => url()->previous()]);
        }

        return view('frontend.client.auth.register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'unique:users,email'],
            'phone' => ['required', 'unique:users,phone'],
            'nrc_passport' => ['required'],
            'term_conditions' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        return $this->registered($request, $user)
        ?: redirect($this->redirectPath());
    }

    // protected function create(array $data)
    // {

    //     return User::create([
    //         'name' => $data['name'],
    //         'email' => $data['email'],
    //         'phone' => $data['phone'],
    //         'nrc_passport' => $data['nrc_passport'],
    //         'date_of_birth' => $data['date_of_birth'],
    //         'gender' => $data['gender'],
    //         'address' => $data['address'],
    //         'account_type' => $data['account_type'],
    //         'password' => Hash::make($data['password']),
    //     ]);
    // }

    public function clientRegister(ClientRegisterRequest $request)
    {

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

        auth()->login($user);

        return redirect()->route('/');

    }
}
