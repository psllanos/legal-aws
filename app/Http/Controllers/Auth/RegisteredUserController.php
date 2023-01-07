<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Utility;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function create()
    {
        if(Utility::getValByName('signup_button')=='on')
        {
          return view('auth.register');
        }
        else
        {
            return abort('404', 'Page not found');
        }
    }


    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

         if(env('RECAPTCHA_MODULE') == 'yes')
        {
            $validation['g-recaptcha-response'] = 'required|captcha';
        }
         else
        {
            $validation=[];
        }
        $this->validate($request, $validation);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 'Owner',
            'lang' => Utility::getValByName('default_language'),
            'created_by' => 1,
        ]);

        $adminRole = Role::findByName('Owner');

        $user->assignRole($adminRole);

        $user->assignPlan(1, 'annual');

        $user->userDefaultData();
        // $user->userDefaultDataRegister($user_id);


        $user->makeEmployeeRole();

        event(new Registered($user));

        Auth::login($user);


        return redirect(RouteServiceProvider::HOME);

    }

    public function showRegistrationForm($lang = '')
    {
        if(empty($lang))
        {
            $lang = Utility::getValByName('default_language');
        }

        \App::setLocale($lang);

        if(Utility::getValByName('signup_button')=='on')
        {
            return view('auth.register', compact('lang'));
        }
        else
        {
            return abort('404', 'Page not found');
        }

    }
}
