<x-tk-base::nav.item text="Home" href="/" />

{{-- Empty Test --}}
<x-tk-base::nav.dropdown
    text="Empty Test"
    :visible="auth()->check()"
>
    <x-tk-base::nav.dropdown-item
        text="Start Application"
        href="/"
        :visible="auth()->check()"
    />
    <x-tk-base::nav.divider />
    <x-tk-base::nav.dropdown-item
        text="Manage Applicants"
        href="/"
        :visible="auth()->check()"
    />
</x-tk-base::nav.dropdown>

{{-- Students --}}
<x-tk-base::nav.dropdown text="Students">
    <x-tk-base::nav.dropdown-item
        text="Start Application"
        href="/"
    />
    <x-tk-base::nav.dropdown-item
        text="Manage Applicants"
        href="/"
        :visible="auth()->check()"
    />
    <x-tk-base::nav.dropdown-item
        text="Manage Students"
        href="/"
        :visible="auth()->check()"
    />
</x-tk-base::nav.dropdown>

{{-- Testing --}}
<x-tk-base::nav.dropdown
    text="Testing"
    :visible="auth()->check()"
>
    <x-tk-base::nav.dropdown-item text="Icons" href="/" />
    <x-tk-base::nav.dropdown-item text="Session" href="/" />
    <x-tk-base::nav.dropdown-item text="PHP Info" href="/" />
</x-tk-base::nav.dropdown>
