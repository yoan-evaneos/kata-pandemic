<?php

namespace Kata;

/**
 * Class City
 *
 * @package Kata
 **/
class City
{

    const CRITICAL_LEVEL = 3;

    private $contaminations = [];

    /**
     * @param \Kata\Virus $virus
     * @param \Kata\Propagation $propagation
     */
    public function beContaminatedBy(Virus $virus, Propagation $propagation)
    {
        if ($this->contaminationLevel($virus) < self::CRITICAL_LEVEL) {
            $this->contaminations[] = $virus;
        } else {
            $hatchedCities = [$this];
            $propagation->propagateFrom($virus, $this, $hatchedCities);
        }
    }

    /**
     * @param \Kata\Virus $virus
     *
     * @return bool
     */
    public function isContaminatedBy(Virus $virus)
    {
        $contaminationsByVirus = array_filter($this->contaminations, function ($contamination) use ($virus) {
            return $contamination === $virus;
        });

        return !empty($contaminationsByVirus);
    }

    /**
     * @param \Kata\Virus $virusToCompare
     *
     * @return int
     */
    public function contaminationLevel(Virus $virusToCompare)
    {
        return count(array_filter($this->contaminations, function (Virus $virus) use ($virusToCompare) {
            return $virus == $virusToCompare;
        }));
    }

    /**
     * @param \Kata\Virus $virus
     */
    public function treatAContamination(Virus $virus)
    {
        foreach ($this->contaminations as $i => $contamination) {
            if ($contamination === $virus) {
                unset($this->contaminations[$i]);
                break;
            }
        }
    }

    /**
     * @param \Kata\Virus $virus
     * @param array $hatchedCities
     * @param \Kata\Propagation $propagation
     */
    public function receivePropagation(Virus $virus, array $hatchedCities, Propagation $propagation)
    {
        if (!in_array($this, $hatchedCities)) {
            if (count($this->contaminations) < self::CRITICAL_LEVEL) {
                $this->contaminations[] = $virus;
            } else {
                $hatchedCities[] = $this;
                $propagation->propagateFrom($virus, $this, $hatchedCities);
            }
        }
    }
}
