<?php

use Tk\Events\FileDeletedEvent;
use Tk\Events\FileUploadedEvent;
use Tk\Models\File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Tk\Dto\FileUploadDto;
use Tk\Table\Cell;
use Tk\Table\IsLivewireTable;
use Tk\Utils\File as TkFile;

new class extends Component {
    use WithFileUploads, WithPagination, IsLivewireTable;

    #[Locked]
    public string $fkey = '';

    #[Locked]
    public int $fid = 0;

    public $upload = null;

    public function boot(): void
    {
        $this->setDefaultLimit(15);
        $this->setDefaultSort('created_at', self::SORT_DESC);
        $this->setRowAttrs(fn($r) => ['style' => 'font-size: 0.9em;']);

        $this->appendCell('original_name')
            ->setHeader('Filename')
            ->addClass('fw-bold align-middle')
            ->setSortable()
            ->setView(fn(File $file, Cell $cell) => view('tkl-ui::components.table.cells.a', [
                'href' => route('files.view', $file->id),
                'text' => $cell->value($file),
                'target' => '_blank',
            ]));

        $this->appendCell('size')
            ->setSortable()
            ->addClass('align-middle')
            ->setValue(fn(File $file) => TkFile::bytes2String($file->size));

        $this->appendCell('created_at')
            ->setHeader('Uploaded')
            ->addClass('align-middle')
            ->setSortable();

        $this->appendCell('_actions')
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
        $maxKb = (int)(TkFile::getMaxUploadBytes() / 1024);
        return [
            'upload' => "required|file|max:{$maxKb}|mimes:txt,md,pdf,zip,tar,gz,doc,docx,xls,xlsx,jpg,jpeg,png,gif",
        ];
    }

    public function updatedUpload(): void
    {
        $this->validateOnly('upload');
    }

    public function uploadFile(): void
    {
        $this->validate();

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
        vd($q->count());
        return $q;
    }
};
?>
<div class="card mb-3 border-info">
    <div class="card-header text-bg-info">
        <h6 class="mb-0">
            <a href="#collapse-myDocuments" id="heading-myDocuments" role="button"
               class="d-block text-decoration-none text-white" data-bs-toggle="collapse">
                <i class="fa fa-chevron-down text-white-50 float-end"></i>
                My Documents
            </a>
        </h6>
    </div>
    <div id="collapse-myDocuments" class="collapse show">
        <div class="card-body">

            {{-- Upload Zone --}}
            <div
                x-data="{
                    dragOver: false,
                    uploading: false,
                    progress: 0,
                    previewSrc: null,
                    previewName: null,
                    isImage: false,
                    init() {
                        this.$el.addEventListener('livewire-upload-start', () => {
                            this.uploading = true;
                            this.progress = 0;
                        });
                        this.$el.addEventListener('livewire-upload-finish', () => {
                            this.uploading = false;
                            $wire.uploadFile();
                        });
                        this.$el.addEventListener('livewire-upload-error', () => {
                            this.uploading = false;
                            this.reset();
                        });
                        this.$el.addEventListener('livewire-upload-progress', (e) => {
                            this.progress = e.detail.progress;
                        });
                        $wire.$watch('upload', (value) => {
                            if (value === null) this.reset();
                        });
                    },
                    reset() {
                        this.$refs.fileInput.value = '';
                        this.previewSrc = null;
                        this.previewName = null;
                        this.isImage = false;
                    },
                    handleDrop(e) {
                        this.dragOver = false;
                        const file = e.dataTransfer.files[0];
                        if (!file) return;
                        const input = this.$refs.fileInput;
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        input.files = dt.files;
                        input.dispatchEvent(new Event('change'));
                        this.showPreview(file);
                    },
                    handleChange(e) {
                        const file = e.target.files[0];
                        if (file) this.showPreview(file);
                    },
                    showPreview(file) {
                        this.previewName = file.name;
                        this.isImage = file.type.startsWith('image/');
                        if (this.isImage) {
                            const reader = new FileReader();
                            reader.onload = (ev) => { this.previewSrc = ev.target.result; };
                            reader.readAsDataURL(file);
                        } else {
                            this.previewSrc = null;
                        }
                    },
                }"
                @dragover.prevent="dragOver = true"
                @dragleave.prevent="dragOver = false"
                @drop.prevent="handleDrop($event)"
            >
                {{-- Drop Zone --}}
                <div
                    class="border rounded p-4 text-center mb-2"
                    :class="dragOver ? 'border-primary bg-primary bg-opacity-10' : 'border-dashed'"
                    style="border-style: dashed !important; cursor: pointer;"
                    @click="$refs.fileInput.click()"
                >
                    <template x-if="!previewName">
                        <div>
                            <i class="fa fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                            <p class="mb-1 text-muted">Drag &amp; drop a file here or <span
                                    class="text-primary">browse</span></p>
                            <small class="text-muted">
                                Allowed: txt, md, pdf, zip, tar, gz, doc, docx, xls, xlsx, jpg, jpeg, png, gif
                                &mdash; Max: {{ \Tk\Utils\File::bytes2String(\Tk\Utils\File::getMaxUploadBytes()) }}
                            </small>
                        </div>
                    </template>
                    <template x-if="previewName">
                        <div>
                            <template x-if="isImage && previewSrc">
                                <img :src="previewSrc" class="img-thumbnail mb-2"
                                     style="max-height: 150px; max-width: 100%;" alt="preview">
                            </template>
                            <template x-if="!isImage">
                                <div class="mb-2">
                                    <i class="fa fa-file fa-3x text-muted"></i>
                                </div>
                            </template>
                            <p class="mb-1 fw-semibold" x-text="previewName"></p>
                            <small class="text-muted"><i class="fa fa-spinner fa-spin"></i> Uploading...</small>

                            {{-- Progress Bar --}}
                            <div x-show="uploading" x-cloak class="mb-2">
                                <div class="progress" style="height: 6px;">
                                    <div
                                        class="progress-bar progress-bar-striped progress-bar-animated"
                                        role="progressbar"
                                        :style="'width: ' + progress + '%'"
                                        :aria-valuenow="progress"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                    ></div>
                                </div>
                                <small class="text-muted">Uploading... <span x-text="progress"></span>%</small>
                            </div>
                        </div>
                    </template>
                </div>

                <input
                    x-ref="fileInput"
                    type="file"
                    class="d-none"
                    wire:model="upload"
                    accept=".txt,.md,.pdf,.zip,.tar,.gz,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif"
                    @change="handleChange($event)"
                >

                {{-- Validation Error --}}
                @error('upload')
                <div class="alert alert-danger py-2 mb-2">
                    <i class="fa fa-exclamation-circle"></i> {{ $message }}
                </div>
                @enderror

                {{-- Saving indicator --}}
                <div wire:loading wire:target="uploadFile" class="mb-2">
                    <small class="text-muted">
                        <span class="spinner-border spinner-border-sm" role="status"></span>
                        Saving...
                    </small>
                </div>

            </div>

            {{-- Files Table --}}
            <div class="mt-3">
                <x-tkl-ui::table :table="$this"/>
            </div>

        </div>
    </div>
</div>
