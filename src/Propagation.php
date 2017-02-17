<?php

namespace Kata;

/**
 * Class Propagation
 *
 * @package Kata
 **/
class Propagation
{
    private $map;

    /**
     * Propagation constructor.
     *
     * @param \Kata\Map $map
     */
    public function __construct(Map $map)
    {
        $this->map = $map;
    }

    /**
     * @param \Kata\Virus $virus
     * @param \Kata\City $city
     * @param array $hatchedCities
     */
    public function propagateFrom(Virus $virus, City $city, array $hatchedCities)
    {
        $linkedCities = $this->map->citiesLinkedTo($city);
        foreach ($linkedCities as $linkedCity)
        {
            $linkedCity->receivePropagation($virus, $hatchedCities, $this);
        }
    }
}
