<?php


namespace SoftUniBlogBundle\Service;


class ArgonEncription implements EncriptionInterface
{
    public function passwordHash($plainPassword)
    {
        return password_hash( $plainPassword,  PASSWORD_ARGON2I );
    }

    public function passwordVerify( $plainPassword, $passwordHash )
    {
        return password_verify( $plainPassword, $plainPassword );
    }
}