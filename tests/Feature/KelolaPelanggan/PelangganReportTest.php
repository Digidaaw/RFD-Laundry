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

    // TC-CUST-16
    public function test_customer_report_can_be_downloaded_as_pdf(): void
    {
        // Arrange
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        $pelanggan = Pelanggan::create([
            'name' => 'Raihan',
            'kontak' => '08111111111',
        ]);

        // Act
        $response = $this->get(
            route('report.pelanggan.pdf', $pelanggan)
        );

        // Assert
        $response->assertStatus(200);

        $response->assertHeader(
            'content-disposition',
            'attachment; filename=Laporan-Pelanggan-Raihan.pdf'
        );
    }

    // TC-CUST-17
    public function test_customer_report_can_be_downloaded_as_excel(): void
    {
        // Arrange
        Excel::fake();

        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        $pelanggan = Pelanggan::create([
            'name' => 'Raihan',
            'kontak' => '08111111111',
        ]);

        // Act
        $this->get(
            route('report.pelanggan.excel', $pelanggan)
        );

        // Assert
        Excel::assertDownloaded(
            'Laporan-Pelanggan-' . now()->format('Ymd') . '.xlsx'
        );
    }
}
