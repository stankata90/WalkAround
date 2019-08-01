<?php


namespace SoftUniBlogBundle\Service;


class BcryptEncription implements EncriptionInterface
{
    public function passwordHash($plainPassword)
    {
        return password_hash( $plainPassword,  PASSWORD_BCRYPT );
    }

    public function passwordVerify( $plainPassword, $passwordHash )
    {
        return password_verify( $plainPassword, $plainPassword );
    }
}