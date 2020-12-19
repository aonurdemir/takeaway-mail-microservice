<?php

namespace Tests\Utils;

class Helper
{
    public function createStringWithLength(int $length)
    {
        $string = "";
        for ($i = 0; $i < $length; $i++) {
            $string .= "a";
        }

        return $string;
    }
}