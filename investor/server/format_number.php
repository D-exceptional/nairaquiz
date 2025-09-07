
<?php

    function format_number($number, $precision = 1) {
        if ($number < 1000) {
            $number_count = number_format($number, $precision);
            $suffix = '';
        } else if($number < 1000000) {
            $number_count = number_format($number / 1000, $precision);
            $suffix = 'K';
        }
        else if($number < 1000000000) {
            $number_count = number_format($number / 1000000, $precision);
            $suffix = 'M';
        }
        else if($number < 1000000000000) {
            $number_count = number_format($number / 1000000000, $precision);
            $suffix = 'B';
        }
        else {
            $number_count = number_format($number / 1000000000000, $precision);
            $suffix = 'T';
        }
        //Remove unnecessary zeros after decimal
        if($precision > 0) {
            $dotzero = '.'.str_repeat('0', $precision);
            $number_count = str_replace($dotzero, '', $number_count);
        }
        return $number_count.$suffix;
    }

?>