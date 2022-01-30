<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PDO;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function index(){
        return view('test_dropzone');
    }

    public function uploadFile(Request $request){
        $data = array();

        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:png,jpg,jpeg,pdf|max:2048'
        ]);

        if($validator->fails()){
            $data['success'] = 0;
            $data['error'] = $validator->errors()->first('file');
        }else{
            $file = $request->file('file');
            $filename = time().'_'.$file->getClientOriginalName();

            $location = 'files_test';

            $file->move($location, $filename);

            $data['success'] = 1;
            $data['message'] = 'Uploaded Successfully';

            DB::statement(
                "INSERT INTO test_upload(filename, ori_filename)
                
                VALUES(?,?)",
                    [
                        $filename, $file->getClientOriginalName()
                    ]
            );
        }

        return response()->json($data);
    }


}
