


Interesting Livewire links:
- [3 Things Laravel Livewire Is NOT](https://youtu.be/s-jEx98GB7s?si=wrq7XMN74HGn40lH)
- [Livewire 4 main updates](https://youtu.be/jwc95oFuTio?si=-3C3A5HveTQuDVYX)

Tutorials:
- [Livewire 4 laracast](https://laracasts.com/series/everything-new-in-livewire-4)
- [Livewire 3 Tutorial](https://youtu.be/bkoJyn8hg5k?si=ZfTzOR-k6GaPj-xn)


------------


Absolutely — here’s the tighter version with:

- **one Alpine component**
- **one Livewire component**
- **one route for the modal fragment**
- **no Alpine store**
- **markup removed on close**

## 1) Button anywhere

```blade
<button type="button" class="btn btn-primary" x-data="markupDialog()" x-on:click="open()">
    Add markup
</button>
```


## 2) Alpine component

Add this to `resources/js/app.js`:

```javascript
document.addEventListener('alpine:init', () => {
    Alpine.data('markupDialog', () => ({
        host: null,
        modal: null,
        loaded: false,

        async open() {
            this.host ??= document.getElementById('markup-dialog-host') || this.createHost();

            if (!this.loaded) {
                const response = await fetch('/markup-dialog');
                this.host.innerHTML = await response.text();
                this.loaded = true;
                this.modal = this.host.querySelector('#markupDialog');
            }

            this.modal.addEventListener('hidden.bs.modal', () => this.cleanup(), { once: true });

            window.dispatchEvent(new CustomEvent('markup-dialog-open'));
            bootstrap.Modal.getOrCreateInstance(this.modal).show();
        },

        createHost() {
            const host = document.createElement('div');
            host.id = 'markup-dialog-host';
            document.body.appendChild(host);
            return host;
        },

        cleanup() {
            window.dispatchEvent(new CustomEvent('markup-dialog-close'));
            this.host.innerHTML = '';
            this.modal = null;
            this.loaded = false;
        },
    }));
});
```


## 3) Route

```php
use Illuminate\Support\Facades\Route;

Route::get('/markup-dialog', fn () => view('partials.markup-dialog-host'));
```


## 4) Partial returned by the route

```blade
<livewire:markup-dialog />
```


## 5) Livewire component

```php
<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class MarkupDialog extends Component
{
    public bool $open = false;
    public string $markup = '';

    #[On('markup-dialog-open')]
    public function openDialog(): void
    {
        $this->open = true;
    }

    #[On('markup-dialog-close')]
    public function closeDialog(): void
    {
        $this->reset(['open', 'markup']);
    }

    public function save(): void
    {
        $this->dispatch('markup-dialog-close');
    }

    public function render()
    {
        return view('livewire.markup-dialog');
    }
}
```


## 6) Livewire view

```blade
<div wire:ignore.self>
    @if ($open)
        <div id="markupDialog" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add markup</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <textarea class="form-control" rows="5" wire:model="markup"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="save">Save</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
```


## What this does

- click button
- Alpine fetches `/markup-dialog`
- Livewire component is loaded into the DOM
- Bootstrap opens the modal
- close button uses Bootstrap’s built-in dismiss behavior
- when the modal finishes closing, Alpine:
    - tells Livewire to reset
    - removes the fetched markup completely

If you want, I can make this **even smaller** by inlining the route into a tiny controller method instead of a closure.
