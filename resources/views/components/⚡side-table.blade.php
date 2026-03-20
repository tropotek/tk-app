<?php

use Livewire\Component;

new class extends Component {


};
?>
<div class="card mb-3 border-info">
    <div class="card-header text-bg-info">
        <h6 class="mb-0">
            <a href="#collapse-example2" id="heading-example2" role="button"
                class="d-block text-decoration-none text-white" data-bs-toggle="collapse">
                <i class="fa fa-chevron-down text-white-50 float-end"></i>
                Basic Livewire table
            </a>
        </h6>
    </div>
    <div id="collapse-example2" class="collapse show">
        <div class="card-body">

            <div class="table-responsive">

                <livewire:tables.user-table />

            </div>

        </div>
    </div>
</div>
