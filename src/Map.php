<?php

namespace Kata;

/**
 * Class Map
 *
 * @package Kata
 **/
class Map
{
    private $links = [];

    /**
     * @param \Kata\City $aCity
     *
     * @return array|mixed
     */
    public function citiesLinkedTo(City $aCity)
    {
       if(array_key_exists(spl_object_hash($aCity), $this->links)) {
           return $this->links[spl_object_hash($aCity)];
       }

       return [];
    }

    /**
     * @param $city
     * @param $linkedCity
     */
    public function linkCities($city, $linkedCity)
    {
        $this->links[spl_object_hash($city)][] = $linkedCity;
        $this->links[spl_object_hash($linkedCity)][] = $city;
    }
}
