<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mail;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\User;
use App\Models\UserVerify;
use Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function Register(){
        return view('register');
    }

    public function postRegister(Request  $request){
        $request->validate(
            [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
            ]
        );
        $data=$request->all();
        $createUser=$this->create($data);

        $token = Str::random(64);

        UserVerify::create([
            'user_id' => $createUser->id,
            'token' => $token,
        ]);

        Mail::send('emails.verifymail', ['token'=>$token], function ($message) use($request){
            $message->from('influjtech@gmail.com', 'Influj Technologies');
            $message->to($request->email);
            $message->subject('Verify Email');
        });

        session()->flash('alert-success');
        return redirect()->route('register');
    }

    public function create(array $data){
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function emailVerify($token){
        $verifyUser = UserVerify::where('token', $token)->first();

        if(!is_null($verifyUser)){
            $verifyUser->user->email_verified=true;
            $verifyUser->user->save();
        }
        session()->flash('alert-success');
        return redirect()->route('login');
    }

    public function Login(){
        return view('login');
    }

    public function postLogin(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials= $request->only('email', 'password');

        if(Auth::attempt($credentials)){
            session()->flash('alert-success');
            return redirect()->intended('dashboard');
        }
        if(!Auth::attempt($credentials)){
            session()->flash('alert-danger');
            return redirect()->route('login');
        }
    }

    public function Dashboard(){
        if(Auth::check()){
            $user=Auth::user();
            $name=$user->name;
            $data=compact('name');
            return view('dashboard')->with($data);
        }
        session()->flash('alert-danger');
        return redirect()->route('login');
    }

    public function LogOut(){
        Session::flush();
        Auth::logout();
        session()->flash('alert-success');
        return redirect()->route('login');
    }
}
