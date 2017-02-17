<?php
namespace Kata\Test;

use Kata\City;
use Kata\Map;
use Kata\Propagation;
use Kata\Virus;

/**
 * Class VilleTest
 *
 * @package Kata\Test
 **/
class CityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Propagation|\Mockery\Mock
     */
    private $propagation;

    /**
     * Set up the Unit Test
     */
    public function setUp()
    {
        $this->propagation = \Mockery::spy(Propagation::class);
    }

    /**
     *
     */
    public function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @test
     */
    public function a_new_city_is_not_contaminated()
    {
        $city = new City();
        $virus = new Virus(Virus::BLUE);

        $this->assertFalse($city->isContaminatedBy($virus));
        $this->assertEquals(0, $city->contaminationLevel($virus));
    }

    /**
     * @test
     */
    public function it_can_be_contaminated_by_virus()
    {
        $city = new City();
        $virus = new Virus(Virus::BLUE);

        $city->beContaminatedBy($virus, $this->propagation);

        $this->assertTrue($city->isContaminatedBy($virus));
    }

    /**
     * @test
     */
    public function contamination_level_is_increased_for_each_new_contamination()
    {
        $city = new City();
        $virus = new Virus(Virus::BLUE);
        $virus2 = new Virus(Virus::YELLOW);

        $city->beContaminatedBy($virus, $this->propagation);
        $city->beContaminatedBy($virus2, $this->propagation);

        $this->assertEquals(1, $city->contaminationLevel(new Virus(Virus::BLUE)));
    }

    /**
     * @test
     */
    public function contamination_level_can_not_exceed_critical_level()
    {
        $city = new City();
        $virus = new Virus(Virus::BLUE);

        $city->beContaminatedBy($virus, $this->propagation);
        $city->beContaminatedBy($virus, $this->propagation);
        $city->beContaminatedBy($virus, $this->propagation);
        $city->beContaminatedBy($virus, $this->propagation);

        $this->assertEquals(City::CRITICAL_LEVEL, $city->contaminationLevel($virus));
    }

    /**
     * @test
     */
    public function contamination_level_is_decreased_when_a_contamination_is_treated()
    {
        $city = new City();
        $virus = new Virus(Virus::BLUE);
        $virus2 = new Virus(Virus::YELLOW);
        $city->beContaminatedBy($virus, $this->propagation);
        $city->beContaminatedBy($virus2, $this->propagation);

        $city->treatAContamination($virus);

        $this->assertEquals(0, $city->contaminationLevel($virus));
    }

    /**
     * @test
     */
    public function propagate_the_virus_when_critical_level_is_exceeded()
    {
        $city = new City();
        $virus = new Virus(Virus::BLUE);

        $city->beContaminatedBy($virus, $this->propagation);
        $city->beContaminatedBy($virus, $this->propagation);
        $city->beContaminatedBy($virus, $this->propagation);
        $city->beContaminatedBy($virus, $this->propagation);

        $this->propagation->shouldHaveReceived('propagateFrom')->with($virus, $city, [$city])->once();
    }

    /**
     * @test
     */
    public function virus_is_not_propagated_when_contamination_level_is_not_critical()
    {
        $city = new City();
        $virus = new Virus(Virus::BLUE);

        $city->beContaminatedBy($virus, $this->propagation);
        $city->beContaminatedBy($virus, $this->propagation);
        $city->beContaminatedBy($virus, $this->propagation);

        $this->propagation->shouldNotHaveReceived('propagateFrom');
    }

    /**
     * @test
     */
    public function contamination_level_is_increased_for_each_new_propagation()
    {
        $mexico = new City();
        $virus = new Virus(Virus::BLUE);
        $propagation = \Mockery::mock(Propagation::class);

        $mexico->receivePropagation($virus, [], $propagation);

        $this->assertEquals(1, $mexico->contaminationLevel($virus));
    }

    /**
     * @test
     */
    public function contamination_is_not_increased_if_source_is_the_same_as_the_current_city()
    {
        $mexico = new City();
        $virus = new Virus(Virus::BLUE);
        $propagation = \Mockery::mock(Propagation::class);

        $mexico->receivePropagation($virus, [$mexico], $propagation);

        $this->assertEquals(0, $mexico->contaminationLevel($virus));
    }
    
    /**
     * @test
     */
    public function a_chain_propagation_occurs_when_the_critical_level_is_exceeded_during_a_propagation()
    {
        $mexico = new City();
        $virus = new Virus(Virus::BLUE);
        $propagation = \Mockery::spy(Propagation::class);

        $mexico->beContaminatedBy($virus, $propagation);
        $mexico->beContaminatedBy($virus, $propagation);
        $mexico->beContaminatedBy($virus, $propagation);

        $mexico->receivePropagation($virus, [], $propagation);

        $propagation->shouldHaveReceived('propagateFrom')->with($virus, $mexico, [$mexico])->once();
    }
    
    /**
     * @test
     */
    public function a_chain_propagation_must_not_be_propagate_on_the_source_city_twice()
    {
        $mexico = new City();
        $miami = new City();
        $losAngeles = new City();
        $virus = new Virus(Virus::BLUE);

        $map = new Map();

        $map->linkCities($miami, $mexico);
        $map->linkCities($mexico, $losAngeles);

        $propagation = new Propagation($map);

        $mexico->beContaminatedBy($virus, $propagation);
        $mexico->beContaminatedBy($virus, $propagation);
        $mexico->beContaminatedBy($virus, $propagation);

        $miami->beContaminatedBy($virus, $propagation);
        $miami->beContaminatedBy($virus, $propagation);
        $miami->beContaminatedBy($virus, $propagation);

        $mexico->beContaminatedBy($virus, $propagation);

        $this->assertEquals(1, $losAngeles->contaminationLevel($virus));
    }
}
