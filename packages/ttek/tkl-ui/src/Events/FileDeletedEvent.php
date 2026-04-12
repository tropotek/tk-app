<?php

namespace Tk\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tk\Dto\FileUploadDto;

class FileDeletedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly FileUploadDto $file,
    ) {
        logger()->info('Event Triggered:', ['event' => get_class($this), 'data' => $file]);
    }
}
