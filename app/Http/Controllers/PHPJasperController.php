<?php

namespace App\Http\Controllers;

use PHPJasper\PHPJasper;

class PHPJasperController extends Controller
{
    public function genForm($prno)
    {
        // $input = storage_path("app/public/myreports/testJasper.jasper");
        // $name = "testJasper";

        $input = storage_path("app/public/myreports/pr_form1.jasper");
        $name = "pr_form";
        $filename = $name . time();
        $output = base_path("public/reports/" . $filename);
        $jdbc_dir = 'C:\xampp\htdocs\PHPJasper\vendor\geekcom\phpjasper\bin\jasperstarter\jdbc';
        $options = [
            'format' => ['pdf'],
            'locale' => 'en',
            // 'params' => ['username' => 'user'],
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

        //$jasper->compile($input)->execute();

        $jasper->process(
                $input,
                $output,
                $options
            )->execute();
        
        //dd(response()->file($output . ".pdf"));
        return response()->file($output . ".pdf")->deleteFileAfterSend();
        // $pdf = PDF::loadView('whateveryourviewname', $data);
        // return $pdf->stream('whateveryourviewname.pdf');
    }

}
