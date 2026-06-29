<?php

namespace Tests\Unit\Layanan_Test;

use App\Http\Requests\LayananStoreRequest;
use Tests\TestCase;

class LayananStoreRequestTest extends TestCase
{
    // Test otorisasi default dari request ini.
    public function test_authorize_returns_true()
    {
        $request = new LayananStoreRequest();

        $this->assertTrue($request->authorize());
    }

    // Test aturan validasi yang wajib ada di dalam request.
    public function test_rules_are_correct()
    {
        $request = new LayananStoreRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertStringContainsString('required', $rules['name']);

        $this->assertArrayHasKey('deskripsi', $rules);
        $this->assertStringContainsString('min:5', $rules['deskripsi']);

        $this->assertArrayHasKey('units', $rules);
        $this->assertStringContainsString('required|array', $rules['units']);

        $this->assertArrayHasKey('units.*.unit_satuan', $rules);
        $this->assertStringContainsString('in:kg,pcs,meter', $rules['units.*.unit_satuan']);
    }
    
    // Test custom validation messages.
    public function test_messages_are_defined()
    {
        $request = new LayananStoreRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('name.required', $messages);
        $this->assertEquals('Nama layanan harus diisi.', $messages['name.required']);

        $this->assertArrayHasKey('units.*.unit_satuan.in', $messages);
        $this->assertEquals('Satuan hanya boleh: kg, pcs, atau meter.', $messages['units.*.unit_satuan.in']);
    }
}
