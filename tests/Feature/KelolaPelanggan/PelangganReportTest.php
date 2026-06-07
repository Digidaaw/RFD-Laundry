<?php

namespace Tests\Feature\Pelanggan;

use App\Models\User;
use App\Models\Pelanggan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Maatwebsite\Excel\Facades\Excel;

class PelangganReportTest extends TestCase
{
    use RefreshDatabase;

    // TC-CUST-17
    public function test_customer_report_can_be_downloaded_as_pdf(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        $pelanggan = Pelanggan::create([
            'name' => 'David',
            'kontak' => '08111111111',
        ]);

        $response = $this->get(
            route('report.pelanggan.pdf', $pelanggan)
        );

        $response->assertStatus(200);

        $response->assertHeader(
            'content-disposition',
            'attachment; filename=Laporan-Pelanggan-David.pdf'
        );
    }

    // TC-CUST-18
    public function test_customer_report_can_be_downloaded_as_excel(): void
    {
        Excel::fake();

        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        $pelanggan = Pelanggan::create([
            'name' => 'David',
            'kontak' => '08111111111',
        ]);

        $this->get(
            route('report.pelanggan.excel', $pelanggan)
        );

        Excel::assertDownloaded(
            'Laporan-Pelanggan-' . now()->format('Ymd') . '.xlsx'
        );
    }
}
