<?php
namespace Kata\Test;

use Kata\City;
use Kata\Map;

/**
 * Class MapTest
 *
 * @package Kata\Test
 **/
class MapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function can_provide_list_of_linked_city()
    {
        $mexico = new City();
        $miami = new City();
        $losAngeles = new City();

        $map = new Map;

        $map->linkCities($miami, $mexico);
        $map->linkCities($mexico, $losAngeles);

        $this->assertEquals([$losAngeles, $miami], $map->citiesLinkedTo($mexico));
        $this->assertEquals([$miami, $losAngeles], $map->citiesLinkedTo($mexico));
        $this->assertEquals([$mexico], $map->citiesLinkedTo($miami));
    }
}
