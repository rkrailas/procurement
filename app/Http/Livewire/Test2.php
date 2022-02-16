<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;

class Test2 extends Component
{
    use WithFileUploads;

    public $testSelect2, $choice_dd;
    public $attachment_file, $maxSize;

    public function updatedAttachmentFile()
    {
        // $this->validate([
        //     'attachment_file.*' => 'max:320', // 5MB Max 5120
        // ]);
    }
    
    public function addAttachment()
    {
        $this->validate([
            'attachment_file.*' => 'max:5120', // 5MB Max 
        ]);
    }

    public function confirmDelete($index)
    {
        unset($this->attachment_file[$index]);
    }

    public function formatSizeUnits($fileSize)
    {
        //Call Golbal Function
        return formatSizeUnits($fileSize);
    }

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

    public function mount()
    {
        $this->maxSize = config('constants.maxAttachmentSize');
    }

    public function render()
    {
        $strsql = "SELECT choice FROM test_select2_choice ORDER BY id";
        $this->choice_dd = DB::select($strsql);

        return view('livewire.test2');
    }
}
