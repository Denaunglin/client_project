@component('mail::message')

# Forgot your password ?

### Hi {{$data['name']}} <br> 

We received a request to reset your password. <br>
Please use the OTP code below to reset your user account password .

###  OTP code  - {{$data['OTP']}} .

{{-- @component('mail::button', ['url' => $link , 'color' => 'success'])
Reset Password
@endcomponent --}}

If you didn't request a password reset, you can ignore this email. <br> Your password will not be changed.

#### Have any questions ?? 

< Contact us >                                                
Email : assisant.apexhotel@gmail.com <br>
phone : 09-256328604



Best Regards ,<br>
Apex Hotel
@endcomponent