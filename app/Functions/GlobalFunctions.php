<?php
    function checkSQLInjection($xCondition)
    {
        $xCondition = str_replace("--","",$xCondition);
        $xCondition = str_replace("or","",$xCondition);
        $xCondition = str_replace("OR","",$xCondition);
        return $xCondition;
    }

    function convertJsonToString($xJson)
    {
        $xJson = str_replace("[", "", $xJson);
        $xJson = str_replace("]", "", $xJson);
        $xJson = str_replace('"', "", $xJson);

        return $xJson;
    }

    function formatSizeUnits($bytes)
        {
            if ($bytes >= 1073741824)
            {
                $bytes = number_format($bytes / 1073741824, 2) . ' GB';
            }
            elseif ($bytes >= 1048576)
            {
                $bytes = number_format($bytes / 1048576, 2) . ' MB';
            }
            elseif ($bytes >= 1024)
            {
                $bytes = number_format($bytes / 1024, 2) . ' KB';
            }
            elseif ($bytes > 1)
            {
                $bytes = $bytes . ' bytes';
            }
            elseif ($bytes == 1)
            {
                $bytes = $bytes . ' byte';
            }
            else
            {
                $bytes = '0 bytes';
            }

            return $bytes;
    }

    function myWhereIn($xCondition)
    {
        $xWhere = "";
        if ($xCondition) {
            
            foreach ($xCondition as $index => $row)
            {
                $xWhere = $xWhere . "'" . $row . "'";

                if ($index < count($xCondition) - 1){
                    $xWhere = $xWhere . ",";
                }
            }
        }

        return($xWhere);
    }

    function myWhereInID($xCondition)
    {
        $xWhere = "";
        if ($xCondition) {
            
            foreach ($xCondition as $index => $row)
            {
                $xWhere = $xWhere . $row;

                if ($index < count($xCondition) - 1){
                    $xWhere = $xWhere . ",";
                }
            }
        }

        return($xWhere);
    }

    function radioToBit($xRadio)
    {
        if ($xRadio == "on"){
            return 1;
        } else {
            return 0;
        }
    }

    function bitToRedio($xBit)
    {
        if ($xBit == 1){
            return "on";
        } else {
            return "false";
        }
    }


    function tinyToBoolean($xTiny)
    {
        if ($xTiny == 1){
            return true;
        } else {
            return false;
        }
    }