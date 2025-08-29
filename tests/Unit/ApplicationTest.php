<?php

namespace Tests\Unit;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function it_combines_address_fields_into_formatted_full_address()
    {
        $app = new Application([
            'address_1' => '180 St Kilda Rd',
            'address_2' => null,
            'city' => 'Melbourne',
            'state' => 'VIC',
            'postcode' => '3006',
        ]);
        
        $this->assertEquals(
            '180 St Kilda Rd, Melbourne VIC 3006',
            $app->full_address
        );
    }
}