<?php


namespace SoftUniBlogBundle\Service;


interface EncriptionInterface
{
    public function passwordHash( $plainPassword );
    public function passwordVerify( $plainPassword, $passwordHash );
}