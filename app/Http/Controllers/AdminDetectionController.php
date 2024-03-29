<?php

namespace App\Http\Controllers;

use App\Models\Detections;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Spatie\FlareClient\Http\Client;

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


            $data = json_decode(DB::table('vwcloths')->get(), true);

            return view('admin.detections', ['detections' => $data]);
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
        if (session()->exists('users')) {
            $mUser = session()->pull("users");
            session()->put("users", $mUser);

            $userType = $mUser['userType'];

            if ($userType != 1) {
                return redirect("/logout");
            }

            if ($request->btnConfirm) {
                $queryResult = DB::table("detections")->where('detectionID', $id)->get();
                $data = json_decode($queryResult, true);
                dd($data);
                $this->callApi($id, $data[0]['imagePath']);
            }

            return redirect("/admin_detections");
        }
        return redirect("/");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        if (session()->exists('users')) {
            $mUser = session()->pull("users");
            session()->put("users", $mUser);

            $userType = $mUser['userType'];

            if ($userType != 1) {
                return redirect("/logout");
            }

            if ($request->btnDelete) {
                try {
                    $originalDirectoryPath = $request->imagePath;
                    if ($originalDirectoryPath) {
                        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . $originalDirectoryPath;
                        File::delete($destinationPath);
                    }
                } catch (Exception $e1) {
                }

                $deleteCount = DB::table('detections')->where('detectionID', '=', $id)->delete();
                if ($deleteCount > 0) {
                    session()->put("successDelete", true);
                } else {
                    session()->put("errorDelete", true);
                }
            }
            return redirect("/admin_detections");
        }
        return redirect("/");
    }

    private function callApi(string $id, string $imagePath): void
    {
        // dd($_SERVER['DOCUMENT_ROOT'] . '\data\results');
        $client = new Client();
        try {
            $response = $client->post('http://localhost:5000/detect', [
                'multipart' => [
                    [
                        'name' => 'id',
                        'contents' => $id
                    ],
                    [
                        'name' => 'image_url',
                        'contents' => $imagePath
                    ],
                    [
                        'name' => 'storagePath',
                        'contents' => $_SERVER['DOCUMENT_ROOT'] . '\data\results'
                    ]
                ]
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }
}
