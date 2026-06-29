<?php

namespace Tests\Unit\Layanan_Test;

use App\Models\Layanan;
use App\Models\LayananUnit;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class LayananModelTest extends TestCase
{
    // Test attributes fillable pada Model Layanan.
    public function test_layanan_has_expected_fillable_attributes()
    {
        $layanan = new Layanan();

        $expected = [
            'name',
            'gambar',
            'deskripsi',
            'is_active',
        ];

        $this->assertEquals($expected, $layanan->getFillable());
    }

    // Test cast tipe data pada Model Layanan.
    public function test_layanan_has_expected_casts()
    {
        $layanan = new Layanan();

        $this->assertEquals('array', $layanan->getCasts()['gambar']);
        $this->assertEquals('boolean', $layanan->getCasts()['is_active']);
    }

    // Test definisi relasi hasMany ke LayananUnit.
    public function test_layanan_has_many_units_relation_definition()
    {
        $layanan = new Layanan();
        $relation = $layanan->units();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertInstanceOf(LayananUnit::class, $relation->getRelated());
    }
}
