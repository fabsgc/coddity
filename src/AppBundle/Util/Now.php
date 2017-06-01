<?php

namespace AppBundle\Util;

class Now
{
    public static function now()
    {
        //return new \DateTime('2017-04-01');
        $now = new \DateTime();
        //return $now->add(new \DateInterval('P2D'));
        return $now;
    }
}