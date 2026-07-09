<x-pages.main>

    <div class="row mb-2">
        <h2>Livewire Examples</h2>
    </div>
    <div class="row">
        <div class="col-lg-9 col-md-8 col-sm-6 col-xs-12">
            <div class="row">
                <div class="col-md-8">

                    <div class="card mb-3 border-primary">
                        <div class="card-body" x-data>
                            <h4>Examples</h4>

                            <p><strong>Confirm dialog:</strong></p>
                            <div x-init="console.log('alpine alive')"></div>
                            <p><button
                                    x-confirm="'Are you sure?'"
                                    @confirmed-action="console.log('Action Executed!')"
                                    {{-- @confirmed-action="$el.form.submit()" --}}
                                >Delete</button></p>
                            <p><a href="{{ route('dashboard') }}"
                                  x-confirm="'Are you sure?'"
                                  @confirmed-action="location.href = $el.href"
                                >Lets Go!</a></p>
                            <p>&nbsp;</p>

                            <p><strong>Test flash message alerts:</strong></p>
                            <ul class="list-unstyled">&nbsp;
                                <a class="btn btn-primary" href="{{ request()->url() }}">Primary</a>
                                <a class="btn btn-success" href="{{ request()->fullUrlWithQuery(['alert' => 'success']) }}">Success</a>
                                <a class="btn btn-info" href="{{ request()->fullUrlWithQuery(['alert' => 'info']) }}">Info</a>
                                <a class="btn btn-warning" href="{{ request()->fullUrlWithQuery(['alert' => 'warning']) }}">Warning</a>
                                <a class="btn btn-danger" href="{{ request()->fullUrlWithQuery(['alert' => 'danger']) }}">Danger</a>
                                <a class="btn btn-danger" href="{{ request()->fullUrlWithQuery(['alert' => 'error']) }}">Error</a>
                            </ul>

                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                        </div>
                    </div>

                </div>
                <div class="col-md-4">

                    <livewire:side-panel />

                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10">

            <livewire:tkl-com::file.upload fkey="App\Models\User" :fid="auth()->id()" />

        </div>

    </div>
</x-pages.main>
