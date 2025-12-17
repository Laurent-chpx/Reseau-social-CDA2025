<?php
namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    //Test que ROLE_USER est ajouté automatiquement
    public function testRoleUserAlwaysAdd(): void
    {
        $user = new User();
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    //Test méthode __toString
    public function testToStringReturnsFullName(): void{
        $user = new User();
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $this->assertEquals('John Doe', (string)$user);
    }
}
