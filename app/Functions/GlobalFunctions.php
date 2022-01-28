<?php

    use Illuminate\Support\Facades\DB;

    function first_function()
    {
        // DB::statement("INSERT INTO dec_val_workflow (approval_type, approver, status, ref_doc_type, ref_doc_no, ref_doc_id, create_by, create_on)
        //     VALUES(?,?,?,?,?,?,?,?)"
        //     ,['VALIDATOR', $this->validator, 'DRAFT', '10', $this->prHeader['prno'], $this->prHeader['id'], auth()->user()->id, Carbon::now()]);
    }
