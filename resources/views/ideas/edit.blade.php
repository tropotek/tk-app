<x-layout.main>

    <form method="POST" action="/ideas/{{ $idea->id }}" class="">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label for="description" class="form-label">Idea</label>
            <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" rows="5">{{ $idea->description }}</textarea>
            @error('description')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="form-text" id="description-help">Have an idea to save for later.</div>

        <button type="submit" class="btn btn-sm btn-outline-primary mt-3">Update</button>
        <a href="/ideas" class="btn btn-sm btn-outline-secondary mt-3">Cancel</a>
        <button type="submit" form="btn-delete-idea" class="btn btn-sm btn-outline-danger mt-3">Delete</button>
    </form>

    <form action="/ideas/{{ $idea->id }}" method="POST" id="btn-delete-idea">
        @csrf
        @method('DELETE')
    </form>

</x-layout.main>