<?php
namespace App\Tests\Unit\Filter;

use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;


class FilterTest extends TestCase{

    //Test que les filtres sont correctement extrait de la requête
    public function testFilterExtractedFromRequest(){
        $request = new Request([
            'city' => '42',
            'department' => '69',
            'category' => '3',
            'date_from' => '2025-01-01',
            'date_to' => '2025-12-31',
            'free_only' => '1',
            'search' => 'concert',
        ]);

        $this->assertEquals('42', $request->query->get('city'));
        $this->assertEquals('69', $request->query->get('department'));
        $this->assertEquals('3', $request->query->get('category'));
        $this->assertEquals('2025-01-01', $request->query->get('date_from'));
        $this->assertEquals('2025-12-31', $request->query->get('date_to'));
        $this->assertTrue($request->query->getBoolean('free_only'));
        $this->assertEquals('concert', $request->query->get('search'));
    }
}
