<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Specialty;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testFirstName()
    {
        $user = new User();
        $user->setFirstname('Test');
        $this->assertEquals('Test', $user->getFirstname());
    }

    public function testLastName()
    {
        $user = new User();
        $user->setLastname('Test');
        $this->assertEquals('Test', $user->getLastname());
    }

    public function testEmail()
    {
        $user = new User();
        $user->setEmail('test@test.test');
        $this->assertEquals('test@test.test', $user->getEmail());
    }

    public function testPassword()
    {
        $user = new User();
        $user->setPassword('testtesttest');
        $this->assertEquals('testtesttest', $user->getPassword());
    }

    public function testRoles()
    {
        $user = new User();
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testAddress()
    {
        $user = new User();
        $user->setAddress('000 test, 00000 test');
        $this->assertEquals('000 test, 00000 test', $user->getAddress());
    }

    public function testRegistrationNumber()
    {
        $user = new User();
        $user->setRegistrationNumber(123456789);
        $this->assertEquals(123456789, $user->getRegistrationNumber());
    }

    public function testSpecialty()
    {
        $user = new User();
        $specialty = new Specialty();
        $specialty->setName('Test');
        $user->setSpecialty($specialty);
        $this->assertEquals('Test', $user->getSpecialty()->getName());
    }
}