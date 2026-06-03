<?php

namespace Tests\Feature\Layanan;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

abstract class LayananFeatureTestCase extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $imageDir = public_path('images/layanan');
        if (! is_dir($imageDir)) {
            mkdir($imageDir, 0755, true);
        }

        $this->user = User::create([
            'name' => 'Admin User',
            'username' => 'admin_layanan_' . uniqid(),
            'password' => 'password',
            'role' => 'admin',
        ]);
    }

    protected function fakeImage(string $name = 'image.jpg'): UploadedFile
    {
        return UploadedFile::fake()->image($name, 500, 500);
    }
}
