<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class Test1 extends Component
{
    public function myRandom()
    {
        //dd(Str::random(20));
        //dd(Carbon::now());
    }

    public function dropzoneStore(Request $request)
    {
        dd('here');
        $file = $request->file('file');
        $fileName = time() . "." . $file->extension();
        $file->move(public_path('files_test'), $fileName);
        return response()->json(['success'=>$fileName]);
    }

    public function render()
    {
        return view('livewire.test1');
    }
}
