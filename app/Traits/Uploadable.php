<?php

namespace App\Traits;

use App\Models\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait Uploadable
{
    /**
     * Store an upload and attach to this model.
     */
    public function storeUpload(UploadedFile $file, ?string $disk = null): Upload
    {
        $disk = $disk ?? $this->getDefaultDisk();

        if ($this->upload) {
            $this->deleteUpload();
        }

        $path = $file->store('', $disk);

        $upload = new Upload([
            'name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'path' => $path,
            'storage_in_kb' => round($file->getSize() / 1024, 2),
        ]);

        $this->upload()->save($upload);

        return $upload;
    }

    /**
     * Get URL of the upload or fallback.
     */
    public function getUploadUrl(): ?string
    {
        if ($this->upload) {
            $disk = $this->getDefaultDisk();
            return Storage::url($this->upload->path);
        }

        return $this->getDefaultImage();
    }

    /**
     * Stream the file (useful for preview).
     */
    public function streamUpload(): ?StreamedResponse
    {
        if (!$this->upload) {
            return null;
        }

        $disk = $this->getDefaultDisk();
        return Storage::response($this->upload->path);
    }

    /**
     * Download the file.
     */
    public function downloadUpload(): ?StreamedResponse
    {
        if (!$this->upload) {
            return null;
        }

        $disk = $this->getDefaultDisk();
        return Storage::download(
            $this->upload->path,
            $this->upload->name
        );
    }

    /**
     * Delete upload (from DB + storage).
     */
    public function deleteUpload(): bool
    {
        if (!$this->upload) {
            return false;
        }

        $disk = $this->getDefaultDisk();
        Storage::disk($disk)->delete($this->upload->path);

        return (bool) $this->upload->delete();
    }

    /**
     * Relation.
     */
    public function upload()
    {
        return $this->morphOne(Upload::class, 'uploadable');
    }

    /**
     * Pick default disk based on model type.
     */
    protected function getDefaultDisk(): string
    {
        return match (class_basename($this)) {
            'User' => 'user_photos',
            'Donor' => 'donor_photos',
            default => 'public',
        };
    }

    /**
     * Return fallback default image.
     */
    protected function getDefaultImage(): string
    {
        return match (class_basename($this)) {
            'User'  => asset('images/default-user.png'),
            'Donor' => asset('images/default-donor.png'),
            default => asset('images/default.png'),
        };
    }
}
