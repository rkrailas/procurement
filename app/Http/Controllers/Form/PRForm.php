<?php

namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use PHPJasper\PHPJasper;

class PRForm extends Controller
{
    public function genForm($prno)
    {
        $input = storage_path("app/public/myforms/pr_form1.jasper");
        $name = "pr_form";
        $filename = $name . time();
        $output = base_path("public/reports/" . $filename);
        $jdbc_dir = 'C:\xampp\htdocs\procurement\vendor\geekcom\phpjasper\bin\jasperstarter\jdbc';
        $options = [
            'format' => ['pdf'],
            'locale' => 'en',
            'params' => ['prno' => $prno],
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