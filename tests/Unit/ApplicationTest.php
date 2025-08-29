<?php

namespace Tests\Unit;

use App\Models\Application;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function test_full_address_combines_fields_correctly()
    {
        $app = new Application([
            'address_1' => '4 Quality Dr',
            'address_2' => null,
            'city' => 'Dandenong South',
            'state' => 'VIC',
            'postcode' => '3175',
        ]);
        $this->assertEquals(
            '4 Quality Dr, Dandenong South, VIC, 3175',
            $app->full_address
        );
    }
}