<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExcelTest extends TestCase
{
	use RefreshDatabase, WithFaker;

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');

    }

	/** @test */
	public function user_can_download_invoices_export() 
	{
	    Excel::fake();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
                         ->get('/qrank');

        $response->assertStatus(200);

	    Excel::assertDownloaded('quick_rank.xlsx');
	}
}
