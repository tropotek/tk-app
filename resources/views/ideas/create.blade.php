<x-layout.main>

    <form method="POST" action="/ideas" class="">
        @csrf

        <div class="mb-3">
            <label for="description" class="form-label">Idea</label>
            <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" rows="5" placeholder="Add your ideas"></textarea>
            @error('description')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="form-text" id="description-help">Have an idea to save for later.</div>

        <button type="submit" class="btn btn-sm btn-outline-primary mt-3">Save</button>
        <a href="/ideas" class="btn btn-sm btn-outline-secondary mt-3">Cancel</a>
    </form>

</x-layout.main>