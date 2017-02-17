<?php
namespace Kata\Test;

use Kata\City;
use Kata\Map;
use Kata\Propagation;
use Kata\Virus;

/**
 * Class PropagationTest
 *
 * @package Kata
 **/
class PropagationTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @test
     */
    public function propagates_virus_to_linked_cities()
    {
        $mexico = new City();
        $miami = \Mockery::spy(City::class);
        $losAngeles = \Mockery::spy(City::class);

        $virus = new Virus(Virus::BLUE);

        $map = new Map;

        $map->linkCities($miami, $mexico);
        $map->linkCities($mexico, $losAngeles);

        $propagation = new Propagation($map);

        $propagation->propagateFrom($virus, $mexico, []);

        $miami->shouldHaveReceived('receivePropagation')->with($virus, [], $propagation)->once();
        $losAngeles->shouldHaveReceived('receivePropagation')->with($virus, [], $propagation)->once();
    }

    /**
     * @test
     */
    public function doesnt_propagate_to_a_city_not_linked()
    {
        $mexico = new City();
        $miami = \Mockery::spy(City::class);

        $virus = new Virus(Virus::BLUE);

        $map = new Map;

        $propagation = new Propagation($map);

        $propagation->propagateFrom($virus, $mexico, []);

        $miami->shouldNotHaveReceived('receivePropagation');
    }
}
