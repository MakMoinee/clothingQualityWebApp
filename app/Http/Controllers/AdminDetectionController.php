<?php

namespace App\Http\Controllers;

use App\Models\Detections;
use Illuminate\Http\Request;

class AdminDetectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (session()->exists('users')) {
            $mUser = session()->pull("users");
            session()->put("users", $mUser);

            $userType = $mUser['userType'];

            if ($userType != 1) {
                return redirect("/logout");
            }

            return view('admin.detections');
        }
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
        if (session()->exists('users')) {
            $mUser = session()->pull("users");
            session()->put("users", $mUser);

            $userType = $mUser['userType'];
            $userID = $mUser['userID'];

            if ($userType != 1) {
                return redirect("/logout");
            }

            if ($request->btnAdd) {
                $files = $request->file("files");
                $fileName = "";

                if ($files) {
                    $mimeType = $files->getMimeType();
                    if ($mimeType == "image/png" || $mimeType == "image/jpg" || $mimeType == "image/JPG" || $mimeType == "image/JPEG" || $mimeType == "image/jpeg" || $mimeType == "image/PNG") {
                        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/data/clothes';
                        $fileName = strtotime(now()) . "." . $files->getClientOriginalExtension();
                        $isFile = $files->move($destinationPath,  $fileName);
                        chmod($destinationPath, 0755);

                        if ($fileName) {
                            $newDetection = new Detections();
                            $newDetection->userID = $userID;
                            $newDetection->imagePath = '/data/clothes/' . $fileName;
                            $newDetection->status = 'Waiting For Confirmation';
                            $isSave = $newDetection->save();
                            if ($isSave) {
                                session()->put("successAddCloth", true);
                            } else {
                                session()->put("errorAddCloth", true);
                            }
                        }
                    } else {
                        session()->put("errorMimeTypeNotValid", true);
                    }
                }
            }

            return redirect("/admin_detections");
        }
        return redirect("/");
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
}
