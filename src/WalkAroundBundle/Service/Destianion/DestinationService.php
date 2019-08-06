<?php


namespace WalkAroundBundle\Service\Destianion;


use Symfony\Component\Security\Core\Security;
use WalkAroundBundle\Entity\Destination;
use WalkAroundBundle\Entity\Region;
use WalkAroundBundle\Repository\DestinationRepository;
use WalkAroundBundle\Repository\RegionRepository;

class DestinationService implements DestinationServerInterface
{
    private $destinationRepository;
    private $regionRepository;
    private $security;
    function __construct(
        DestinationRepository $destinationRepository,
        RegionRepository $regionRepository,
        Security $security
    )
    {
        $this->destinationRepository = $destinationRepository;
        $this->regionRepository = $regionRepository;
        $this->security = $security;
    }

    public function save(Destination $destination): bool
    {
        /** @var Region $regionEntity */
        $regionEntity = $this->regionRepository->find( $destination->getRegion() );
        $destination
            ->setCountSeen(0)
            ->setCountVisited(0)
            ->setCountLiked(0)
            ->setAddedOn( new \DateTime('now'))
            ->setAddedUser( $this->security->getUser() )
            ->setRegion( $regionEntity );

        return $this->destinationRepository->insert( $destination );
    }

    public function update(Destination $destination): bool
    {
        return $this->destinationRepository->update( $destination);
    }

    public function remove(Destination $destination): bool
    {
       return  $this->destinationRepository->delete($destination);
    }

    /**
     * @param Destination $destination
     * @return Destination|null|object
     */
    public function findOne(Destination $destination): ?Destination
    {
        return $this->destinationRepository->find( $destination );
    }

    /**
     * @param int $id
     * @return Destination|null|object
     */
    public function findOneById(int $id): ?Destination
    {
        return $this->destinationRepository->findOneBy( [ 'id'=> $id ] );
    }

    public function findAll()
    {
        return $this->destinationRepository->findAll();
    }
}