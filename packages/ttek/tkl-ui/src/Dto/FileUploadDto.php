<?php

namespace Tk\Dto;

final class FileUploadDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $fkey,
        public readonly int $fid,
        public readonly string $originalName,
        public readonly string $filename,
        public readonly string $path,
        public readonly string $mimeType,
        public readonly int $size,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            fkey: (string) $data['fkey'],
            fid: (int) $data['fid'],
            originalName: (string) $data['original_name'],
            filename: (string) $data['filename'],
            path: (string) $data['path'],
            mimeType: (string) $data['mime_type'],
            size: (int) $data['size'],
        );
    }
}
