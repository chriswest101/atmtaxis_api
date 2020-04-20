<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use TestDatabaseSeeder;
use Tests\TestCase;

class PriceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(TestDatabaseSeeder::class);
    }
    
    /**
     * @dataProvider distanceProvider
     */
    public function testGivenADistanceThatNotAValidFloat_WhenGetEstimateIsCalled_Then400BadRequestIsReturned($dataProvider)
    {
        // Given
        $distance = $dataProvider;

        // When
        $response = $this->getJson('/api/prices/estimate?distance='.$distance);

        // Then
        $response->assertStatus(400);
    }

    public function testGivenADistanceThatIsLessThenTheMinimumSpecified_WhenGetEstimateIsCalled_Then200OkIsReturnedWithTheCorrectValues()
    {
        // Given
        $distance = '3';

        // When
        $response = $this->getJson('/api/prices/estimate?distance='.$distance);

        // Then
        $response
            ->assertStatus(200)
            ->assertJson([ 
                'estimates' => [ 
                    'upper' => '8.00',
                    'lower' => '5.00',
                    'price' => '5.80'
                 ]
             ]);
    }

    public function testGivenADistanceThatIsGreaterThenTheMinimumSpecified_WhenGetEstimateIsCalled_Then200OkIsReturnedWithTheCorrectValues()
    {
        // Given
        $distance = '8';

        // When
        $response = $this->getJson('/api/prices/estimate?distance='.$distance);

        // Then
        $response
            ->assertStatus(200)
            ->assertJson([ 
                'estimates' => [ 
                    'upper' => '16.00',
                    'lower' => '10.00',
                    'price' => '11.60'
                 ]
             ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function distanceProvider()
    {
        return [
            [null],
            [''],
            ['a'],
            ['-1']
        ];
    }
}
