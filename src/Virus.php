<?php

namespace Kata;

/**
 * Class Virus
 *
 * @package Kata
 **/
class Virus
{
    const BLUE = 'blue';
    const YELLOW = 'yellow';

    private $color;

    /**
     * Virus constructor.
     *
     * @param $color
     */
    public function __construct($color)
    {
        $this->color = $color;
    }
}
