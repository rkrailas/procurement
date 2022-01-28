<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class Test1 extends Component
{
    public function myRandom()
    {
        //dd(Str::random(20));
        dd(Carbon::now());
    }

    public function dropzoneStore(Request $request)
    {
        dd('here');
        $file = $request->file('file');
        $fileName = time() . "." . $file->extension();
        $file->move(public_path('myfiles'), $fileName);
        return response()->json(['success'=>$fileName]);
    }

    public function render()
    {
        return view('livewire.test1');
    }
}
