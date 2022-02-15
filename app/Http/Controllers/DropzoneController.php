<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DropzoneController extends Controller
{
    public function dropzone()
    {
        return view('dropzone');
    }

    public function dropzoneStore(Request $request)
    {
        $request->validate([ 'file' => 'required' ]); // Good idea to validate
        $image = $request->file('file');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('test_dropzone'),$imageName);
        return response()->json(['success' => $imageName]);
    }
}
