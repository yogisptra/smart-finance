<?php
namespace App\Services\Storage;

interface ReceiptStorageInterface
{
    public function store($file, string $path, string $filename): string;
    public function delete(string $path): bool;
    public function exists(string $path): bool;
    public function read(string $path);
    public function getAbsolutePath(string $path): string;
}
