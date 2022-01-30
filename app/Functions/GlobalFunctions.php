<?php

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

