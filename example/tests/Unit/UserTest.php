<?php


use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testThatWeCanGetFirstName(): void
    {
        $user = new App\Models\User;

        $user->setFirstName('John');
        $this->assertEquals('John', $user->getFirstName());
    }

    public function testThatWeCanGetLastName(): void
    {
        $user = new App\Models\User;

        $user->setLastName('dev');
        $this->assertEquals('dev', $user->getLastName());
    }

    public function testThatWeCanGetFullName(): void
    {
        $user = new App\Models\User;

        $user->setFirstName('John');
        $user->setLastName('dev');
        $this->assertEquals('John dev', $user->getFullName());
    }
    public function testThatWeCanGetTrimmedFullName(): void
    {
        $user = new App\Models\User;

        $user->setFirstName('John');
        $user->setLastName('dev');
        $this->assertEquals('John dev', $user->getFullName());
    }
}