<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Test2 extends Component
{
    public $testSelect2, $choice_dd;

    public function getVar()
    {
        $strsql = "SELECT name FROM test_select2 WHERE id=2";
        $data = DB::select($strsql);
        if ($data) {
            //dd(json_decode($data[0]->name));
            $this->testSelect2 = json_decode($data[0]->name);
            // dd($this->testSelect2);

            $newOption = '';
            $xchoice_dd = json_decode(json_encode($this->choice_dd), true);
            foreach ($xchoice_dd as $row) {
                $newOption = $newOption . "<option value='" . $row['choice'] . "' ";

                for ($i=0; $i < count($this->testSelect2); $i++){
                    if ($row['choice'] == $this->testSelect2[$i]) {
                        $newOption = $newOption . "selected='selected'";
                    }                    
                }
                $newOption = $newOption . ">" . $row['choice'] . "</option>";
            }
            //dd($newOption);
            $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#select2']);
        }
    }
    
    public function InsertVar()
    {
        DB::statement(
            "INSERT INTO test_select2(name)
        VALUES(?)",
            [
                json_encode($this->testSelect2),
            ]
        );
    }

    public function render()
    {
        $strsql = "SELECT choice FROM test_select2_choice ORDER BY id";
        $this->choice_dd = DB::select($strsql);

        return view('livewire.test2');
    }
}
