<?php

namespace App\Http\Controllers;

use App\Models\SystemUsers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WelcomeController extends Controller
{
    public function index()
    {
        $query = DB::table('system_users')->where('userType', '=', 1)->get();
        $data = json_decode($query, true);
        if (count($data) <= 0) {
            $this->createAdminDefault();
        }
        if (session()->exists('users')) {
            $mUser = session()->pull("users");
            session()->put("users", $mUser);

            $userType = $mUser['userType'];

            if ($userType == 1) {
                return redirect("/admin_dashboard");
            }
        }
        return view('welcome');
    }

    private function createAdminDefault()
    {
        try {
            $newUser = new SystemUsers();
            $newUser->firstname = "Administrator";
            $newUser->middlename = "X";
            $newUser->lastname = "Administrator";
            $newUser->email = "admin@clothy.com";
            $newUser->password = Hash::make("admin");
            $newUser->address = "default";
            $newUser->birthDate = "1998-01-01";
            $newUser->phoneNumber = "";
            $newUser->userType = 1;
            $isSave = $newUser->save();
        } catch (Exception $e) {
            error_log($e);
        }
    }
}
