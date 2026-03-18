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
                        <div class="card-body">
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                                <h4>My Ideas</h4>
                                <x-tkl-ui::table :$table />
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

            <div class="page-sidebar">

                <div class="card bg-light">
                    <div class="card-body">
                        <div class=" " id="client-details">
                            <a href="javascript:;">
                                <strong>Greencross Vet Hospital - Werribee</strong>
                            </a>
                            <p class="small text-muted text-truncate mb-0">
                                <span class="d-block">
                                    <i class="ri-map-pin-line align-bottom me-1"></i>
                                    <span>250 Princes Highway, Werribee, Victoria, 3030, Australia</span>
                                </span>
                                <span class="d-block">
                                    <i class="ri-mail-send-line align-bottom me-1"></i>
                                    <a href="mailto:gvhwerribee@greencrossvet.com.au">gvhwerribee@greencrossvet.com.au</a>
                                </span>
                                <span class="d-block">
                                    <i class="ri-phone-line align-bottom me-1"></i> <a href="tel:0387211414">03 8721 1414</a>
                                </span>
                            </p>
                        </div>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-pages.main>
