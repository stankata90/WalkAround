<?php


namespace WalkAroundBundle\Service\Region;


use WalkAroundBundle\Entity\Region;
use WalkAroundBundle\Repository\RegionRepository;

class RegionService implements RegionServiceInterface
{
    private $regionRepository;
    public function __construct( RegionRepository $regionRepository)
    {
        $this->regionRepository = $regionRepository;
    }


    public function getAll()
    {
        return $this->regionRepository->findAllOrderByName();
    }

    /**
     * @param int $id
     * @return Region|null|object
     */
    public function getById(int $id): ?Region
    {
        return $this->regionRepository->find( $id );
    }

    /**
     * @param string $name
     * @return object|Region|null
     */
    public function getByName(string $name)
    {
        return $this->regionRepository->findOneBy(['name' => $name]);

    }

    /**
     * @param Region $region
     * @return Region|null|object
     */
    public function get(Region $region): ?Region
    {
        return $this->regionRepository->find( $region );
    }
}