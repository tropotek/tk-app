<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Tk\Dto\FileUploadDto;
use Tk\Events\FileDeletedEvent;
use Tk\Events\FileUploadedEvent;
use Tk\Models\File;
use Tk\Table\Column;
use Tk\Table\TableComponent;
use Tk\Utils\File as FileUtil;

/**
 * NOTE:
 *   To change the file upload size limit, edit the php.ini file and config/livewire.php:
 *   'temporary_file_upload' => [
 *       // ...
 *       'rules' => ['required', 'file', 'max:12288'],    // <== Change 'rules' config (12Mb is the default)
 *      // ...
 *   ];
 *
 */
new class extends TableComponent {
    use WithFileUploads, WithPagination;

    const DEFAULT_TYPES = ['txt', 'md', 'pdf', 'zip', 'tar', 'gz', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif'];

    #[Locked]
    public string $fkey = '';

    #[Locked]
    public int $fid = 0;

    #[Locked]
    public string $accept = '';

    //#[Rule('required|file|max:51200')]
    public $upload = null;


    public function boot(): void
    {
        $this->setDefaultLimit(15);
        $this->setDefaultSort('created_at', self::SORT_DESC);
        $this->setRowAttrs(fn($r) => ['style' => 'font-size: 0.9em;']);

        $this->appendColumn('original_name')
            ->setHeader('Filename')
            ->addClass('fw-bold align-middle')
            ->setSortable()
            ->setView(fn(File $file, Column $column) => view('tkl-ui::components.table.columns.a', [
                'href' => route('files.view', $file->id),
                'text' => $column->value($file),
                'target' => '_blank',
            ]));

        $this->appendColumn('size')
            ->setSortable()
            ->addClass('align-middle text-nowrap')
            ->setValue(fn(File $file) => FileUtil::bytes2String($file->size));

        $this->appendColumn('created_at')
            ->setHeader('Uploaded')
            ->addClass('align-middle text-nowrap')
            ->setValue([\Tk\Table\Formats::class, 'date'])
            ->setSortable();

        $this->appendColumn('_actions')
            ->setHeader('')
            ->setView(fn(File $file) => '
                <button wire:click="deleteFile(' . $file->id . ')"
                    wire:confirm="Delete this file?"
                    style="font-size: 0.8em;"
                    class="p-1 btn btn-sm btn-outline-danger" title="Delete">
                    <i class="fa fa-trash"></i>
                </button>
            ');
    }

    protected function rules(): array
    {
        $maxKb = (int)(FileUtil::getMaxUploadBytes() / 1024)-10;
        $exts = implode(',', $this->allowedExtensions);

        vd($exts, $maxKb);
        return [
            'upload' => "required|file|max:{$maxKb}|mimes:{$exts}",
        ];
    }

    /**
     * Extensions derived from the $accept prop (or defaults).
     * Handles the .ext tokens in an HTML accept string; MIME-type tokens are
     * passed through to the <input> but not used for server-side validation.
     */
    #[Computed]
    public function allowedExtensions(): array
    {
        if (empty($this->accept)) {
            return self::DEFAULT_TYPES;
        }

        $exts = array_values(array_filter(array_map(
            fn($token) => ltrim(trim($token), '.'),
            array_filter(
                explode(',', $this->accept),
                fn($token) => str_starts_with(trim($token), '.')
            )
        )));

        return !empty($exts) ? $exts : self::DEFAULT_TYPES;
    }

    /**
     * Value for the <input accept="..."> attribute.
     * Uses the raw $accept prop when provided, otherwise builds it from defaults.
     */
    #[Computed]
    public function acceptAttribute(): string
    {
        return !empty($this->accept)
            ? $this->accept
            : implode(',', array_map(fn($e) => '.' . $e, self::DEFAULT_TYPES));
    }

    public function updatedUpload(): void
    {
        $valid = $this->validateOnly('upload');
    }

    public function uploadFile(): void
    {
        $valid = $this->validate();

        $originalName = $this->upload->getClientOriginalName();
        $mimeType = $this->upload->getMimeType();

        $path = $this->upload->store('documents/' . $this->fid, 'local');
        $size = Storage::disk('local')->size($path);

        $file = File::create([
            'fkey' => $this->fkey,
            'fid' => $this->fid,
            'original_name' => $originalName,
            'filename' => basename($path),
            'path' => $path,
            'mime_type' => $mimeType,
            'size' => $size,
        ]);

        event(new FileUploadedEvent(FileUploadDto::fromArray($file->toArray())));

        $this->reset('upload');
        $this->clearPaginatedRows();
        $this->dispatch('upload-complete');
    }

    public function deleteFile(int $id): void
    {
        $file = File::where('id', $id)
            ->where('fkey', $this->fkey)
            ->where('fid', $this->fid)
            ->firstOrFail();

        Storage::disk('local')->delete($file->path);
        $file->delete();

        event(new FileDeletedEvent(FileUploadDto::fromArray($file->toArray())));
    }

    #[Computed]
    public function rows(): array|Builder
    {
        $q = File::query()
            ->where('fkey', $this->fkey)
            ->where('fid', $this->fid);

        return $q;
    }
};
?>
<div
    x-data="{
        modalOpen: false,
        uploading: false,
        uploaded: false,
        progress: 0,
        filename: null,
        uploadError: null,
        maxBytes: {{ \Tk\Utils\File::getMaxUploadBytes() }},

        openModal() {
            this.modalOpen = true;
            document.body.style.overflow = 'hidden';
            this.resetForm();
        },

        closeModal() {
            this.modalOpen = false;
            document.body.style.removeProperty('overflow');
            this.resetForm();
            $wire.set('upload', null);
        },

        resetForm() {
            this.uploading = false;
            this.uploaded = false;
            this.progress = 0;
            this.filename = null;
            this.uploadError = null;

            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },

        init() {
            this.$el.addEventListener('livewire-upload-start', () => {
                this.uploading = true;
                this.uploaded = false;
                this.progress = 0;
            });

            this.$el.addEventListener('livewire-upload-finish', () => {
                this.uploading = false;
                this.uploaded = true;
            });

            this.$el.addEventListener('livewire-upload-error', () => {
                this.uploading = false;
                this.uploaded = false;
                this.uploadError = 'Upload failed.';
            });

            this.$el.addEventListener('livewire-upload-progress', (e) => {
                this.progress = e.detail.progress ?? 0;
            });

            $wire.$on('upload-complete', () => {
                this.closeModal();
            });
        },

        handleChange(e) {
            const file = e.target.files[0];

            this.uploadError = null;
            this.uploaded = false;
            this.uploading = false;
            this.progress = 0;

            if (!file) {
                this.filename = null;
                return;
            }

            if (file.size > this.maxBytes) {
                this.uploadError = `File is too large. Maximum allowed size is ${Math.ceil(this.maxBytes / 1024 / 1024)} MB.`;
                this.filename = null;
                e.target.value = '';
                return;
            }

            this.filename = file.name;
            this.uploading = true;

            $wire.upload(
                'upload',
                file,
                () => {
                    this.uploading = false;
                    this.uploaded = true;
                    this.progress = 100;
                },
                () => {
                    this.uploading = false;
                    this.uploaded = false;
                    this.uploadError = 'Upload failed.';
                },
                (progress) => {
                    this.progress = typeof progress === 'number'
                        ? progress
                        : progress?.detail?.progress ?? 0;
                }
            );
        },
    }"
>
    {{-- Card --}}
    <div class="card mb-3 border-info">
        <div class="card-header text-bg-info d-flex align-items-center justify-content-between">
            <h6 class="mb-0">
                <a href="#collapse-myDocuments" id="heading-myDocuments" role="button"
                   class="d-block text-decoration-none text-white" data-bs-toggle="collapse">
                    <i class="fa fa-chevron-down text-white-50 me-1"></i>
                    My Documents
                </a>
            </h6>
            <button type="button" class="btn btn-sm btn-light" @click="openModal()">
                <i class="fa fa-upload me-1"></i> Upload File
            </button>
        </div>
        <div id="collapse-myDocuments" class="collapse show">
            <div class="card-body">
                <x-tkl-ui::table :table="$this" class="table table-sm table-hover" style="font-size: 0.8rem;"/>
            </div>
        </div>
    </div>

    <template x-if="modalOpen">
        <div>
            <div class="modal-backdrop fade show"></div>

            <div class="modal fade show d-block" tabindex="-1" @click.self="closeModal()">
                <div class="modal-dialog" @click.stop>
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fa fa-upload me-2"></i>Upload File
                            </h5>
                            <button type="button" class="btn-close" @click="closeModal()" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Select file</label>
                                <input
                                    x-ref="fileInput"
                                    type="file"
                                    class="form-control"
                                    accept="{{ $this->acceptAttribute }}"
                                    @click="progress = 0; uploaded = false; uploadError = null"
                                    @change="handleChange($event)"
                                >

                                <div class="form-text">
                                    Allowed: {{ implode(', ', $this->allowedExtensions) }}
                                    &mdash; Max: {{ \Tk\Utils\File::bytes2String(\Tk\Utils\File::getMaxUploadBytes()) }}
                                </div>

                                <template x-if="uploadError">
                                    <div class="invalid-feedback d-block">
                                        <i class="fa fa-exclamation-circle"></i>
                                        <span x-text="uploadError"></span>
                                    </div>
                                </template>

                                @error('upload')
                                <div class="invalid-feedback d-block">
                                    <i class="fa fa-exclamation-circle"></i> {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div x-show="uploading || uploaded" class="mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted"
                                           x-text="uploading ? 'Uploading...' : 'Ready to save'"></small>
                                    <small class="text-muted" x-show="uploading" x-text="progress + '%'"></small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div
                                        class="progress-bar progress-bar-striped"
                                        :class="uploading ? 'progress-bar-animated' : 'bg-success'"
                                        role="progressbar"
                                        :style="'width: ' + progress + '%'"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                    ></div>
                                </div>
                            </div>

                            <div wire:loading wire:target="uploadFile">
                                <small class="text-muted">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                    Saving...
                                </small>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeModal()">Cancel</button>
                            <button
                                type="button"
                                class="btn btn-primary"
                                wire:click="uploadFile"
                                wire:loading.attr="disabled"
                                wire:target="uploadFile"
                                :disabled="!uploaded || uploading"
                            >
                                <span wire:loading wire:target="uploadFile"
                                      class="spinner-border spinner-border-sm me-1" role="status"></span>
                                <i wire:loading.remove wire:target="uploadFile" class="fa fa-upload me-1"></i>
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
