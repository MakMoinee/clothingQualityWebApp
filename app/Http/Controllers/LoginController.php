<?php

namespace App\Http\Controllers;

use App\Models\SystemUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->btnLogin) {
            $email = $request->email;
            $password = $request->password;

            $queryResult = DB::table('system_users')->where(['email' => $email])->get();
            $data = json_decode($queryResult, true);
            $users = array();
            $userType = 0;

            foreach ($data as $d) {
                if (password_verify($password, $d['password'])) {
                    $userType = $d['userType'];
                    array_push($users, $d);
                    break;
                }
            }

            if (count($users) > 0) {
                if ($userType == 1) {
                    session()->put("users", $users[0]);
                    session()->put("successLogin", true);
                    return redirect("/admin_dashboard");
                } else {
                    session()->put("users", $users[0]);
                    session()->put("successLogin", true);
                    return redirect("/userdashboard");
                }
            } else {
                session()->put("errorLogin", true);
                return redirect("/");
            }
        } else {
            return redirect("/");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function signup(Request $request)
    {
        if (isset($request->btnSignup)) {

            $email = $request->email;
            $queryResult = DB::table('system_users')->where('email', '=', $email)->get();
            $data = json_decode($queryResult, true);

            if (count($data) > 0) {
                session()->put("emailExist", true);
            } else {
                $newUser = new SystemUsers();
                $newUser->firstName = $request->firstName;
                $newUser->middleName = $request->middleName;
                $newUser->lastName = $request->lastName;
                $newUser->password = $request->password;
                $newUser->birthDate = $request->birthDate;
                $newUser->address = $request->address;
                $newUser->phoneNumber = $request->phoneNumber;
                $newUser->email = $email;
                $newUser->userType = 2;
                $isSave = $newUser->save();
                if ($isSave) {
                    session()->put("successAddUser", true);
                } else {
                    session()->put("errorAddUser", true);
                }
            }



            return redirect("/");
        } else {
            return redirect("/");
        }
    }

    public function logout()
    {
        session()->flush();
        session()->put("successLogout", true);
        return redirect("/");
    }
}
