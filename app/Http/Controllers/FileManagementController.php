<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileManagementController extends Controller
{
    /**
     * Lấy danh sách File Mẫu
     */
    public function index()
    {
        try {
            $file_infor = [];
            foreach (File::allFiles(public_path('FileStudent')) as $value) {
                $file_infor[] = $value->getFilename();
            }
            return response()->json(['status' => "Success", 'ListFileName' => $file_infor]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
        // return response()->download(public_path('FileStudent/DSSV.xlsx'));
    }
    public function DowloadFile($id)
    {
        try {
            $path = public_path('FileStudent/' . $id);
            return response()->download($path);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
    /**
     * Thêm 1 file mẫu
     */
    public function store(Request $request)
    {
        try {
            $filename = $request->file->getClientOriginalName();
            foreach (File::allFiles(public_path('FileStudent')) as $value) {
                if ($value->getFilename() == $filename) {
                    return response()->json(['status' => "Failed", 'Err_Message' => "File đã tồn tại!"]);
                }
            }
            $request->file->move(public_path("FileStudent"), $filename);
            return response()->json(['status' => "Success"]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
