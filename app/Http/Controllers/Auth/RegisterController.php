<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Jrean\UserVerification\Traits\VerifiesUsers;
use Jrean\UserVerification\Facades\UserVerification;
use App\Http\Requests\Front\UserFrontRegisterFormRequest;
use Illuminate\Auth\Events\Registered;
use App\Events\UserRegistered;

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
    use VerifiesUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('guest', ['except' => ['getVerification', 'getVerificationError']]);
    }

    public function register(UserFrontRegisterFormRequest $request)
    {
        $user = new User();
        $user->first_name = $request->input('first_name');
        $user->middle_name = $request->input('middle_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->is_active = 0;
        $user->verified = 0;
        $user->save();
        /*         * *********************** */
        $user->name = $user->getName();
        $user->update();
        /*         * *********************** */
        event(new Registered($user));
        event(new UserRegistered($user));
        $this->guard()->login($user);
        UserVerification::generate($user);
        UserVerification::send($user, 'User Verification', config('mail.recieve_to.address'), config('mail.recieve_to.name'));

        //assign package
        add_candidate_package();

        //send mail to admin
        $mail_res = send_mail(
            'no-reply@radstarstaffing.com',
            'admin@radstarstaffing.com',
            'New Candidate Registration',
            ($user->name ?? '') . ' has registered as candidate. Head on to admin panel to activate/deactivate account.',
        );

        //send mail to user
        $mail_res = send_mail(
            'no-reply@radstarstaffing.com',
            $user->email,
            'Radstar Staffing - Candidate Registration',
            'Welcome to Radstar Staffing. We appreciate your patience please wait for the admin approval.',
        );

        return $this->registered($request, $user) ?: redirect($this->redirectPath());
    }

}
