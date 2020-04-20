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

class BookingsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(TestDatabaseSeeder::class);
    }
    
    /**
     * @dataProvider bookingProvider
     */
    public function testGivenACreatedBookingRequestThatIsInvalid_WhenCreateBookingIsCalled_Then400BadRequestIsReturned($dataProvider)
    {
        // Given
        $bookingData = $dataProvider;

        // When
        $response = $this->postJson('/api/bookings', $bookingData);
        
        // Then
        $response->assertStatus(400);
    }
    
    public function testGivenACreatBookingRequestAndTheUserDoesNotExist_WhenCreateBookingIsCalled_Then201CreatedIsReturnedAndThatBookingIsCreatedAndATempUserIsCreated()
    {
        // Given
        $faker = Faker::create();
        $bookingData = [
            'from_destination' => $faker->sentence(),
            'from_latlong' => $faker->randomFloat().','.$faker->randomFloat(),
            'to_destination' => $faker->sentence(),
            'to_latlong' => $faker->randomFloat().','.$faker->randomFloat(),
            'date' => $faker->date($format = 'Y-m-d', $max = 'now'),
            'time' => $faker->time($format = 'H:i:s', $max = 'now'),
            'name' => $faker->name(),
            'email' => $faker->email(),
            'phone' => $faker->randomNumber(),
            'no_of_people' => $faker->randomDigit(),
            'distance' => $faker->randomFloat()
        ];

        // When
        $response = $this->postJson('/api/bookings', $bookingData);
        
        // Then
        $response->assertStatus(201);
        $this->assertDatabaseHas('bookings', [
            'id'       => 1,
            'user_id'  => 1,
            'from_destination' => $bookingData['from_destination'],
            'from_latlong' => $bookingData['from_latlong'],
            'to_destination' => $bookingData['to_destination'],
            'to_latlong' => $bookingData['to_latlong'],
            'date' => $bookingData['date'],
            'time' => $bookingData['time'],
            'no_of_people' => $bookingData['no_of_people'],
            'distance' => $bookingData['distance']
        ]);
        $this->assertDatabaseHas('users', [
            'id'  => 1,
            'name' => $bookingData['name'],
            'email' => $bookingData['email'],
            'phone' => $bookingData['phone'],
            'guest_account' => 1,
        ]);
    }
    
    public function testGivenACreatBookingRequestAndTheUserDoesExist_WhenCreateBookingIsCalled_Then201CreatedIsReturnedAndThatBookingIsCreatedAndLinkedToTheCorrectUser()
    {
        // Given
        $users = factory(User::class, 3)->make();
        foreach ($users as $key => $user) {
            $user->save();
        }
        $faker = Faker::create();
        $bookingData = [
            'from_destination' => $faker->sentence(),
            'from_latlong' => $faker->randomFloat().','.$faker->randomFloat(),
            'to_destination' => $faker->sentence(),
            'to_latlong' => $faker->randomFloat().','.$faker->randomFloat(),
            'date' => $faker->date($format = 'Y-m-d', $max = 'now'),
            'time' => $faker->time($format = 'H:i:s', $max = 'now'),
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $faker->randomNumber(),
            'no_of_people' => $faker->randomDigit(),
            'distance' => $faker->randomFloat()
        ];

        // When
        $response = $this->postJson('/api/bookings', $bookingData);
        
        // Then
        $response->assertStatus(201);
        $this->assertDatabaseHas('bookings', [
            'id'       => 1,
            'user_id'  => $user->id,
            'from_destination' => $bookingData['from_destination'],
            'from_latlong' => $bookingData['from_latlong'],
            'to_destination' => $bookingData['to_destination'],
            'to_latlong' => $bookingData['to_latlong'],
            'date' => $bookingData['date'],
            'time' => $bookingData['time'],
            'no_of_people' => $bookingData['no_of_people'],
            'distance' => $bookingData['distance']
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function bookingProvider()
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
                    'phone' => '',
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
                    'phone' => 'a',
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
                    'phone' => '0111',
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
                    'phone' => '0111',
                    'no_of_people' => '11',
                    'distance' => '1'
                ]
            ]
        ];
    }
}
