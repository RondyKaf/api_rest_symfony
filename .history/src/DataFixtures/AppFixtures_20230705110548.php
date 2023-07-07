<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
        $this->userPasswordHasher = $userPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {


        $user = new User();
        $user->setEmail("rondykfm@gmail.com");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user,"password"));
        $manager->persist($user);

        $userAdmin = new User();
        $userAdmin->setEmail("rondykfmAdmin@gmail.com");
        $userAdmin->setRoles(["ROLE_USER"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin,"password"));
        $manager->persist($userAdmin);
        
        // $product = new Product();
        // $manager->persist($product);

        for($i = 0; $i < 40 ; $i++){
            $user
        }

        $manager->flush();
    }
}
