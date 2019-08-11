<?php


namespace WalkAroundBundle\Service\Region;


use WalkAroundBundle\Entity\Region;

interface RegionServiceInterface
{
    public function getAll();
    public function getById( int $id ) :?Region;
    public function getByName(string $name) ;
    public function get(Region $region) :?Region;

}