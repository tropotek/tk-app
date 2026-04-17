<x-pages.main>

    <div class="alert-component row">
        <div class="col-12">
            <div class="alert alert-info" role="alert">
                A simple info alert—check it out!
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <h2>Bootstrap Examples</h2>
    </div>
    <div class="row">
        <div class="col-lg-9 col-md-8 col-sm-6 col-xs-12">
            <div class="row">
                <div class="col-md-8">

                    <div class="card mb-3">
                        <div class="card-body" x-data>
                            <h4>My Ideas</h4>
                            <p>...</p>
                            <div x-init="console.log('alpine alive')"></div>
                            <p><button
                                    x-confirm="Are you sure?"
                                    @confirmed-action="console.log('Action Executed!')"
                                >Delete</button></p>
                            <p><a href="{{ route('dashboard') }}"
                                  x-confirm="Are you sure?"
                                  @confirmed-action="location.href = $el.href"
                                >Lets Go!</a></p>
                            <p>...</p>
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
