<?php

namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use PHPJasper\PHPJasper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PRForm extends Controller
{
    public function genForm($prno)
    {
        $signature_img = "C:/xampp/htdocs/procurement/public/images/signature/";
        $strsql = "SELECT approver FROM dec_val_workflow WHERE approval_type='DECIDER' AND status='30' AND ref_doc_type='10' 
                AND ref_doc_no='" . $prno . "'";
        $data = DB::select($strsql);
        if ($data) {
            $signature_img = $signature_img . $data[0]->approver . ".png";
            
            //ตรวจสอบว่ามีไฟล์หรือไม่ ถ้าไม่มีจะใช้รูปพื้นสีขาว
            if(!File::exists($signature_img)){
                $signature_img = "C:/xampp/htdocs/procurement/public/images/signature/no_signature.png";
            }
        } else {
            $signature_img = "C:/xampp/htdocs/procurement/public/images/signature/no_signature.png";
        }

        //.jrxml extension source code / .jasper extension compiled
        $input = storage_path("app/public/myforms/pr_form1.jasper"); 
        $name = "pr_form";
        $filename = $name . time();
        $output = base_path("public/reports/" . $filename);
        $jdbc_dir = 'C:\xampp\htdocs\procurement\vendor\geekcom\phpjasper\bin\jasperstarter\jdbc';
        $options = [
            'format' => ['pdf'],
            'locale' => 'en',
            'params' => ['prno' => $prno, 'signature_img' => $signature_img],
            'db_connection' => [
                'driver'    => 'generic',
                'host'      => env('DB_HOST'),
                'port'      => env('DB_PORT'),
                'username'  => env('DB_USERNAME'),
                'password'  => env('DB_PASSWORD'),
                'database'  => env('DB_DATABASE'),
                'jdbc_driver' => 'com.microsoft.sqlserver.jdbc.SQLServerDriver',
                'jdbc_url'  => 'jdbc:sqlserver://localhost:1433;databaseName='.env('DB_DATABASE'),
                'jdbc_dir'  => $jdbc_dir 
            ]
        ];

        $jasper = new PHPJasper;

        //For First Complie (jrxml Only)
        //$jasper->compile($input)->execute();

        $jasper->process(
                $input,
                $output,
                $options
            )->execute();
        
        return response()->file($output . ".pdf")->deleteFileAfterSend();
    }
}