<?php

namespace App\Controller;

use App\Entity\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BackendController extends EasyAdminController
{
     private $passwordEncoder;
     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }

    public function persistEntity($entity)
    {
        $this->encodePassword($entity);
        parent::persistEntity($entity);
    }

    public function updateEntity($entity)
    {
        $this->encodePassword($entity);
        parent::updateEntity($entity);
    }

    public function encodePassword($admin)
    {
        if (!$admin instanceof Admin) {
            return;
        }

        $admin->setPassword(
            $this->passwordEncoder->encodePassword($admin, $admin->getPassword())
        );
    }

}
