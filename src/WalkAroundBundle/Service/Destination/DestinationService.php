<?php


namespace WalkAroundBundle\Service\Destination;

use DateTime;
use Exception;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use WalkAroundBundle\Controller\DestinationController;
use WalkAroundBundle\Entity\Destination;
use WalkAroundBundle\Entity\DestinationLiked;
use WalkAroundBundle\Entity\Event;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Form\Destination\DestinationCreateType;
use WalkAroundBundle\Form\Destination\DestinationEditType;
use WalkAroundBundle\Repository\DestinationRepository;
use WalkAroundBundle\Repository\RegionRepository;
use WalkAroundBundle\Service\CommentDestination\CommentDestinationServiceInterface;
use WalkAroundBundle\Service\DestinationLiked\DestinationLikedServiceInterface;
use WalkAroundBundle\Service\Event\EventServiceInterface;
use WalkAroundBundle\Service\Region\RegionServiceInterface;
use WalkAroundBundle\Service\User\UserServiceInterface;

class DestinationService implements DestinationServerInterface
{
    const ERROR_NAME = 'Insert Name !';
    const ERROR_DESC = 'Insert Description !';
    const ERROR_IMAGE = 'Select image !';
    const ERROR_REGION = 'Not exits this region !';

    private $security;
    private $userService;
    private $eventService;
    private $commentsService;
    private $likedService;
    private $regionService;


    private $regionRepo;
    private $destinationRepo;

    function __construct(
        Security $security,
        UserServiceInterface $userService,
        EventServiceInterface $eventService,
        CommentDestinationServiceInterface $commentDestinationService,
        DestinationLikedServiceInterface $destinationLikedService,
        RegionServiceInterface $regionService,

        RegionRepository $regionRepository,
        DestinationRepository $destinationRepository
    )
    {
        $this->security = $security;
        $this->userService = $userService;
        $this->eventService = $eventService;
        $this->commentsService = $commentDestinationService;
        $this->likedService = $destinationLikedService;
        $this->regionService = $regionService;

        $this->destinationRepo = $destinationRepository;
        $this->regionRepo = $regionRepository;
    }

    /**
     * @param DestinationController $controller
     * @param Request $request
     * @param Destination $destEntity
     * @return bool
     * @throws Exception
     */
    public function createProcess($controller, $request, &$destEntity ): bool
    {

        $destEntity = new Destination();
        /** @var FormInterface $form */
        $form = $controller->createForm( DestinationCreateType::class, $destEntity );
        $form->handleRequest( $request );

        if( !$destEntity->getName() )
            throw new Exception(self::ERROR_NAME);

        if( !$destEntity->getDescription() )
            throw new Exception( self::ERROR_DESC);

        if( !$request->files->get('destination')['image'] )
            throw new Exception(self::ERROR_IMAGE);

        $regionEntity = $this->regionService->getById($destEntity->getRegionId());
        if ( !$regionEntity )
            throw new Exception(self::ERROR_REGION);

        /** @var UploadedFile $image */
        $image = $form['image']->getData();

        if( !$image->getError() == 0 )
            throw new Exception('Image max size ' . ( (UploadedFile::getMaxFilesize() / 1024 ) /1024 ) ."mb" );

        $fileName = $this->createImage( $image, $controller );

        $destEntity
            ->setCountSeen(0)
            ->setCountVisited(0)
            ->setCountLiked(0)
            ->setAddedOn( new DateTime('now'))
            ->setAddedUser( $this->security->getUser() )
            ->setRegion( $regionEntity )
            ->setImage( $fileName )
        ;

        return $this->destinationRepo->insert( $destEntity );
    }

    /**
     * @param Destination $destEntity
     * @param Controller $contr
     * @param Request $request
     * @return bool
     * @throws Exception
     */
    public function updateProcess($destEntity, Controller $contr, $request): bool
    {
        $oldImage = new Destination();
        $oldImage->setImage( $destEntity->getImage() );
        /** @var FormInterface $form */
        $form = $contr->createForm( DestinationEditType::class, $destEntity);
        $form->handleRequest(  $request );

        if( !$destEntity->getName() )
            throw new Exception(self::ERROR_NAME);

        if( !$destEntity->getDescription() )
            throw new Exception( self::ERROR_DESC);

        $regionEntity = $this->regionService->getById($destEntity->getRegionId());
        if ( !$regionEntity )
            throw new Exception(self::ERROR_REGION);

        /** @var UploadedFile $image */
        $image = $form['image']->getData();

        if( $request->files->get('destination')['image'] and !$image->getError() == 0 )
            throw new Exception('Image max size ' . ( (UploadedFile::getMaxFilesize() / 1024 ) /1024 ) ."mb" );

        if( $request->files->get('destination')['image'] != null  ) {
            $fileName = $this->createImage( $image, $contr );
            $this->deleteImage( $oldImage, $contr );
            $destEntity->setImage( $fileName );
        } else {
            $destEntity->setImage( $oldImage->getImage() );
        }

        return $this->update( $destEntity );
    }

    public function removeProcess(Destination $destination, $controller ): bool
    {

        $this->commentsService->removeCommentsByDestination( $destination );
        $this->likedService->removeLikesByDestination( $destination );

        $this->deleteImage( $destination, $controller );

        $arrAllEvents = $this->findDestinationEvents( $destination );

        foreach ($arrAllEvents as $event) {
            $this->eventService->dropProcess( $event );
        }

        return  $this->destinationRepo->delete($destination);
    }
    /**
     * @return array
     */
    public function findAll()
    {
        return $this->destinationRepo->findAll();
    }

    /**
     * @param Destination $destination
     * @return Destination|null|object
     */
    public function findOne(Destination $destination): ?Destination
    {

        return $this->destinationRepo->find( $destination );
    }

    /**
     * @param int $id
     * @return Destination|null|object
     */
    public function findOneById(int $id): ?Destination
    {
        return $this->destinationRepo->findOneBy( [ 'id'=> $id ] );
    }

    /**
     * @param Event $event
     * @return object|null
     */
    public function findOneByEvent( Event $event) {

        return $this->destinationRepo->find($event->getDestinationId() );
    }
    /**
     * @param Destination $destination
     * @return mixed
     */
    public function findDestinationEvents(Destination $destination) {

        return $this->eventService->findByDestination( $destination);
    }


    /**
     * @param int $page
     * @return User[]|null|
     */
    public function listAll( int $page, &$findPages ) :?array {
        $perPage = 6;

        $countResult = count( $this->destinationRepo->findAll() );
        $findPages = ceil( $countResult / $perPage );
        if( $findPages < $page ) {
            $page = $countResult / $perPage;
        }
        $offset = $page*$perPage-$perPage;

        if( $offset < 0 ) {
            $offset = 0;
        }

        return  $this->destinationRepo->findBy([],[], $perPage, $offset );
    }

    public function update(Destination $destination) {
        return $this->destinationRepo->update( $destination );
    }


    public function addSeenCount( Destination $destination ) {
        $destination->setCountSeen($destination->getCountSeen() + 1 );

        $this->destinationRepo->update( $destination );
    }

    public function viewDependence(Destination $destination)
    {
        $this->addSeenCount( $destination );

        /** @var User $currentUser */
        $currentUser = $this->security->getUser();
        /** @var DestinationLiked $destinationLikedEntity */

        $destinationLikedEntity = NULL;

        if( $currentUser !== NULL )
            $destinationLikedEntity = $this->likedService->findLike( $destination, $currentUser );

        return [
            'destinationLikedEntity' => $destinationLikedEntity
        ];
    }

    /**
     * @param Destination $destination
     * @param DestinationController|Controller $controller
     * @return bool
     */
    public function deleteImage(Destination $destination, $controller ) {
        $image = $controller->getParameter('destination_directory').'/'.$destination->getImage();

        if(file_exists( $image ))
            if( unlink( $image ))
                return true;

            return false;
    }

    /**
     * @param UploadedFile $image
     * @param DestinationController|Controller $controller
     * @return bool
     * @throws Exception
     */
    public function createImage( UploadedFile $image, $controller ) {

        $fileName = md5( uniqid() ) . ".". $image->guessExtension();
        try {
            $image->move(
                $controller->getParameter('destination_directory'),
                $fileName
            );

            return $fileName;
        } catch ( FileException $e ) {

            throw new Exception( $e->getMessage() );
        }
    }
}