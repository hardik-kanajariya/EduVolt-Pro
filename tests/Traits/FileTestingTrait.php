<?php

namespace Tests\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait FileTestingTrait
{
    /**
     * Create a fake uploaded file for testing.
     */
    protected function createFakeFile(string $name = 'test.txt', int $size = 100, string $mimeType = 'text/plain'): UploadedFile
    {
        return UploadedFile::fake()->create($name, $size, $mimeType);
    }

    /**
     * Create a fake image file for testing.
     */
    protected function createFakeImage(string $name = 'test.jpg', int $width = 100, int $height = 100): UploadedFile
    {
        return UploadedFile::fake()->image($name, $width, $height);
    }

    /**
     * Create a fake PDF file for testing.
     */
    protected function createFakePdf(string $name = 'test.pdf', int $size = 100): UploadedFile
    {
        return UploadedFile::fake()->create($name, $size, 'application/pdf');
    }

    /**
     * Assert that a file exists in storage.
     */
    protected function assertFileExistsInStorage(string $disk, string $path): void
    {
        $this->assertTrue(
            Storage::disk($disk)->exists($path),
            "File [{$path}] does not exist in storage disk [{$disk}]"
        );
    }

    /**
     * Assert that a file does not exist in storage.
     */
    protected function assertFileNotExistsInStorage(string $disk, string $path): void
    {
        $this->assertFalse(
            Storage::disk($disk)->exists($path),
            "File [{$path}] exists in storage disk [{$disk}] but shouldn't"
        );
    }

    /**
     * Clean up test files from storage.
     */
    protected function cleanupTestFiles(string $disk = 'local', string $directory = 'test-files'): void
    {
        if (Storage::disk($disk)->exists($directory)) {
            Storage::disk($disk)->deleteDirectory($directory);
        }
    }
}
