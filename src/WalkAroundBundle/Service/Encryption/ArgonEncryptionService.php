<?php


namespace WalkAroundBundle\Service\Encryption;


class ArgonEncryptionService implements EncryptionServiceInterface
{
    public function hash($plainPassword)
    {
        return password_hash( $plainPassword,  PASSWORD_ARGON2I );
    }

    public function verify( $plainPassword, $passwordHash )
    {
        return password_verify( $plainPassword, $plainPassword );
    }
}