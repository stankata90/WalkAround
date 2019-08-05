<?php


namespace WalkAroundBundle\Service\Encryption;


interface EncryptionServiceInterface
{
    public function hash( $plainPassword );
    public function verify( $plainPassword, $passwordHash );
}