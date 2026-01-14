<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Event;
use App\Entity\Category;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    //Test de la méthode toString
    public function testToStringReturnsTitle(): void
    {
        $event = new Event();
        $event->setTitle('Concert de jazz');

        $this->assertEquals('Concert de jazz', (string) $event);
    }
}
