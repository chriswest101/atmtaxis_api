<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use TestDatabaseSeeder;
use Tests\TestCase;
use Faker\Factory as Faker;

class QuotesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(TestDatabaseSeeder::class);
    }
    
    /**
     * @dataProvider quoteProvider
     */
    public function testGivenACreatedBookingRequestThatIsInvalid_WhenCreateBookingIsCalled_Then400BadRequestIsReturned($dataProvider)
    {
        // Given
        $quoteData = $dataProvider;

        // When
        $response = $this->postJson('/api/quotes', $quoteData);
        
        // Then
        $response->assertStatus(400);
    }
    
    public function testGivenACreatedBookingRequestAndTheUserDoesNotExist_WhenCreateBookingIsCalled_Then201CreatedIsReturnedAndThatQuoteIsCreatedAndATempUserIsCreated()
    {
        // Given
        $faker = Faker::create();
        $quoteData = [
            'from_destination' => $faker->sentence(),
            'from_latlong' => $faker->randomFloat().','.$faker->randomFloat(),
            'to_destination' => $faker->sentence(),
            'to_latlong' => $faker->randomFloat().','.$faker->randomFloat(),
            'date' => $faker->date($format = 'Y-m-d', $max = 'now'),
            'time' => $faker->time($format = 'H:i:s', $max = 'now'),
            'name' => $faker->name(),
            'email' => $faker->email(),
            'no_of_people' => $faker->randomDigit(),
            'distance' => $faker->randomFloat()
        ];

        // When
        $response = $this->postJson('/api/quotes', $quoteData);
        
        // Then
        $response->assertStatus(201);
        $this->assertDatabaseHas('quotes', [
            'id'       => 1,
            'user_id'  => 1,
            'from_destination' => $quoteData['from_destination'],
            'from_latlong' => $quoteData['from_latlong'],
            'to_destination' => $quoteData['to_destination'],
            'to_latlong' => $quoteData['to_latlong'],
            'date' => $quoteData['date'],
            'time' => $quoteData['time'],
            'no_of_people' => $quoteData['no_of_people'],
            'distance' => $quoteData['distance']
        ]);
        $this->assertDatabaseHas('users', [
            'id'  => 1,
            'name' => $quoteData['name'],
            'email' => $quoteData['email'],
            'guest_account' => 1,
        ]);
    }
    
    public function testGivenACreatedBookingRequestAndTheUserDoesExist_WhenCreateBookingIsCalled_Then201CreatedIsReturnedAndThatQuoteIsCreatedAndLinkedToTheCorrectUser()
    {
        // Given
        $users = factory(User::class, 3)->make();
        foreach ($users as $key => $user) {
            $user->save();
        }
        $faker = Faker::create();
        $quoteData = [
            'from_destination' => $faker->sentence(),
            'from_latlong' => $faker->randomFloat().','.$faker->randomFloat(),
            'to_destination' => $faker->sentence(),
            'to_latlong' => $faker->randomFloat().','.$faker->randomFloat(),
            'date' => $faker->date($format = 'Y-m-d', $max = 'now'),
            'time' => $faker->time($format = 'H:i:s', $max = 'now'),
            'name' => $user->name,
            'email' => $user->email,
            'no_of_people' => $faker->randomDigit(),
            'distance' => $faker->randomFloat()
        ];

        // When
        $response = $this->postJson('/api/quotes', $quoteData);
        
        // Then
        $response->assertStatus(201);
        $this->assertDatabaseHas('quotes', [
            'id'       => 1,
            'user_id'  => $user->id,
            'from_destination' => $quoteData['from_destination'],
            'from_latlong' => $quoteData['from_latlong'],
            'to_destination' => $quoteData['to_destination'],
            'to_latlong' => $quoteData['to_latlong'],
            'date' => $quoteData['date'],
            'time' => $quoteData['time'],
            'no_of_people' => $quoteData['no_of_people'],
            'distance' => $quoteData['distance']
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function quoteProvider()
    {
        return [
            [
                [
                    'from_destination' => '',
                    'from_latlong' => '',
                    'to_destination' => '',
                    'to_latlong' => '',
                    'date' => '',
                    'time' => '',
                    'name' => '',
                    'email' => '',
                    'no_of_people' => '',
                    'distance' => ''
                ]
            ],
            [
                [
                    'from_destination' => 'a',
                    'from_latlong' => 'a',
                    'to_destination' => 'a',
                    'to_latlong' => 'a',
                    'date' => 'a',
                    'time' => 'a',
                    'name' => 'a',
                    'email' => 'a',
                    'no_of_people' => 'a',
                    'distance' => 'a'
                ]
            ],
            [
                [
                    'from_destination' => 'a',
                    'from_latlong' => 'a',
                    'to_destination' => 'a',
                    'to_latlong' => 'a',
                    'date' => '2010-01-01',
                    'time' => 'a',
                    'name' => 'a',
                    'email' => 'a@a.com',
                    'no_of_people' => '0',
                    'distance' => '0'
                ]
            ],
            [
                [
                    'from_destination' => 'a',
                    'from_latlong' => 'a',
                    'to_destination' => 'a',
                    'to_latlong' => 'a',
                    'date' => '2010-01-01',
                    'time' => 'a',
                    'name' => 'a',
                    'email' => 'a@a.com',
                    'no_of_people' => '11',
                    'distance' => '1'
                ]
            ]
        ];
    }
}
