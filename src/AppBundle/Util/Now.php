<?php

namespace AppBundle\Util;

class Now
{
    public static function now()
    {
        $now = new \DateTime();
        return $now;
    }
}
