<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class Test1 extends Component
{
    public $requestor_dd, $requestorfor_dd, $requestor, $requestorfor, $requestor2;

    public function test()
    {
        dd($this->requestor2);
    }

    public function getdataforselect2(Request $request){
        if ($request) {

            $term = trim($request->term);
            $posts = DB::table('users')->selectRaw("id, name + ' ' + lastname as text")
                ->where('name', 'LIKE',  '%' . $term. '%')
                ->orderBy('name', 'asc')->simplePaginate(10);
         
            $morePages=true;
            $pagination_obj= json_encode($posts);
            if (empty($posts->nextPageUrl())){
                $morePages=false;
            }
                $results = array(
                "results" => $posts->items(),
                "pagination" => array(
                    "more" => $morePages
                )
                );
        
            return response()->json($results);
        }
    }

    public function loadDropdownList()
    {
        $strsql = "SELECT id, name + ' ' + ISNULL(lastname, '') as fullname, username FROM users 
                    WHERE company='" . auth()->user()->company 
                    . "' ORDER BY users.name";
        $this->requestor_dd = DB::select($strsql);
        $this->requestorfor_dd = DB::select($strsql);
    }

    public function render()
    {
        $this->loadDropdownList();

        return view('livewire.test1');
    }
}
