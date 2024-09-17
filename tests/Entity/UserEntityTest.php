<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

// CORRECTION ECF - ACTIVITE TYPE 3 / COMPETENCE 6 : Préparer et executer les plans de tests d'une application

class UserEntityTest extends KernelTestCase
{
    private EntityManager $entityManager;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testCreateUser(): void
    {
        $user = new User();
        $user->setEmail('email@test.com');
        $user->setPassword('password');
        $user->setFirstname('firstname');
        $user->setLastname('lastname');

        $userRepository = $this->entityManager->getRepository(User::class);
        $userRepository->save($user, true);

        $fetchedUsers = $userRepository->findAll();
        $retrievedUser = null;

        foreach ($fetchedUsers as $key => $fetchedUser) {
            if ($fetchedUser->getEmail() == 'email@test.com') {
                $retrievedUser = $fetchedUser;
            }
        }

        $this->assertEquals($retrievedUser, $user);
    }

    public function testReadUser(): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);

        $fetchedUsers = $userRepository->findAll();
        $retrievedUser = null;

        foreach ($fetchedUsers as $key => $fetchedUser) {
            if ($fetchedUser->getEmail() == 'email@test.com') {
                $retrievedUser = $fetchedUser;
            }
        }

        $this->assertNotEquals($retrievedUser, null);
    }

    public function testUpdateUser(): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);

        $fetchedUsers = $userRepository->findAll();
        $retrievedUser = null;

        foreach ($fetchedUsers as $key => $fetchedUser) {
            if ($fetchedUser->getEmail() == 'email@test.com') {
                $retrievedUser = $fetchedUser;
            }
        }

        $retrievedUser->setEmail('newemail@test.com');
        $userRepository->save($retrievedUser, true);

        $fetchedUsers = $userRepository->findAll();
        $editedUser = null;

        foreach ($fetchedUsers as $key => $fetchedUser) {
            if ($fetchedUser->getEmail() == 'newemail@test.com') {
                $editedUser = $fetchedUser;
            }
        }

        $this->assertEquals($editedUser, $retrievedUser);
    }

    public function testDeleteUser(): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);

        $fetchedUsers = $userRepository->findAll();
        $retrievedUser = null;

        foreach ($fetchedUsers as $key => $fetchedUser) {
            if ($fetchedUser->getEmail() == 'newemail@test.com') {
                $retrievedUser = $fetchedUser;
            }
        }

        $userRepository->remove($retrievedUser, true);

        $fetchedUsers = $userRepository->findAll();
        $deletedUser = null;

        foreach ($fetchedUsers as $key => $fetchedUser) {
            if ($fetchedUser->getEmail() == 'newemail@test.com') {
                $deletedUser = $fetchedUser;
            }
        }

        $this->assertEquals($deletedUser, null);
    }
}

// Executer les tests en entrant la commande suivante dans la console :
// vendor/bin/phpunit tests/Entity/UserEntityTest.php
