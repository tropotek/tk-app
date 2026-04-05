<x-pages.main>

    <div class=" mb-2">
        <h2>{{ $pageName }}</h2>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            {{--            <x-tkl-ui::table :table="$table1" />--}}

            <div class="row">
                <div class="d-flex flex-nowrap text-nowrap gap-2 align-items-center">

                    <div x-data="{ q: '' }">
                        <input type="text" class="form-control form-control-sm w-auto"
                               placeholder="Name, Email"
                            {{--                               x-model="q"--}}
                            {{--                               @input.debounce.250ms="if (q.trim().length >= 3 || q.trim() === '') $wire.set('search', q.trim())"--}}
                        />
                    </div>

                    <div class="text-nowrap text-primary">
                        <small>Filter By:</small>
                    </div>

                    <select class="form-select form-select-sm w-auto">
                        <option value="">All Countries</option>
                    </select>

                    <button
                        type="button"
                        class="btn btn-link btn-sm"
                        title="Clear Filters & Search"
                        {{--                        wire:click="clearFilters"--}}
                    >
                        <i class="fa fa-circle-xmark fa-lg"></i>
                    </button>

                    <div>
                        <a href="{{route('admin.users.create')}}" class="btn btn-primary btn-sm">
                            New User
                        </a>
                    </div>

                    <div class="flex-grow-1 text-end small">
                        <button
                            type="button"
                            class="btn btn-link btn-sm"
                            title="Download CSV"
                            {{--                    wire:click="csv"--}}
                        >
                            <i class="fa-regular fa-file-excel fa-lg pb-2"></i>
                        </button>

                        <span class="text-secondary">
                    Showing
                    <span class="fw-semibold">{{ $table->rows()->firstItem() }}</span>
                    to
                    <span class="fw-semibold">{{ $table->rows()->lastItem() }}</span>
                    of
                    <span class="fw-semibold">{{ $table->rows()->total() }}</span>
                    results
                </span>
                    </div>
                </div>
            </div>

            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    @foreach ($table->getCells()->filter(fn($r) => $r->isVisible()) as $cell)
                        <th class="{{ ($cell->isSortable() ? 'col-sort '  : '') }}">
                            @if ($cell->isSortable())
                                <a href="{{ $cell->getNextSortUrl($table->sort, $table->dir) }}"
                                   class="fw-bold {{ ($table->sort === $cell->getName()) ? $table->dir : '' }}">
                                    {{ $cell->getHeader() }}
                                </a>
                            @else
                                <span class="fw-bold">{{ $cell->getHeader() }}</span>
                            @endif
                        </th>
                    @endforeach

                    <th class="text-muted"><i class="fa-solid fa-pen-to-square"></i></th>
                </tr>
                </thead>

                <tbody class="table-group-divider">
                @foreach ($table->rows() as $user)
                    <tr wire:key="{{ $user->id }}">
                        @foreach($table->getCells()->filter(fn($r) => $r->isVisible()) as $cell)
                            @if ($cell->getName() == 'name')
                                <td class="fw-bold">
                                    <a href="{{ route('admin.users.edit', $user->id) }}">{{ $cell->html($user) }}</a>
                                </td>
                            @else
                                <td class="tt">{{ $cell->html($user) }}</td>
                            @endif
                        @endforeach

                        <td>
                            <a href="{{ route('admin.users.edit', $user->id) }}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $table->rows()->links() }}


        </div>
    </div>

</x-pages.main>
