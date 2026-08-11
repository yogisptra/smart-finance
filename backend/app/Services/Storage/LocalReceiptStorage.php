<?php
namespace App\Services\Storage;

use Illuminate\Support\Facades\Storage;

class LocalReceiptStorage implements ReceiptStorageInterface
{
    public function store($file, string $path, string $filename): string
    {
        return $file->storeAs($path, $filename, 'local');
    }

    public function delete(string $path): bool
    {
        if ($this->exists($path)) {
            return Storage::disk('local')->delete($path);
        }
        return false;
    }

    public function exists(string $path): bool
    {
        return Storage::disk('local')->exists($path);
    }

    public function read(string $path)
    {
        return Storage::disk('local')->get($path);
    }

    public function getAbsolutePath(string $path): string
    {
        return storage_path('app/' . $path);
    }
}
