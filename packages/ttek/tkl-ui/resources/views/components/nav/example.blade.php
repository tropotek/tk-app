<x-tkl-ui::nav.item text="Home" href="/" />

{{-- Empty Test --}}
<x-tkl-ui::nav.dropdown
    text="Empty Test"
    :visible="auth()->check()"
>
    <x-tkl-ui::nav.dropdown-item
        text="Start Application"
        href="/"
        :visible="auth()->check()"
    />
    <x-tkl-ui::nav.divider />
    <x-tkl-ui::nav.dropdown-item
        text="Manage Applicants"
        href="/"
        :visible="auth()->check()"
    />
</x-tkl-ui::nav.dropdown>

{{-- Students --}}
<x-tkl-ui::nav.dropdown text="Students">
    <x-tkl-ui::nav.dropdown-item
        text="Start Application"
        href="/"
    />
    <x-tkl-ui::nav.dropdown-item
        text="Manage Applicants"
        href="/"
        :visible="auth()->check()"
    />
    <x-tkl-ui::nav.dropdown-item
        text="Manage Students"
        href="/"
        :visible="auth()->check()"
    />
</x-tkl-ui::nav.dropdown>

{{-- Testing --}}
<x-tkl-ui::nav.dropdown
    text="Testing"
    :visible="auth()->check()"
>
    <x-tkl-ui::nav.dropdown-item text="Icons" href="/" />
    <x-tkl-ui::nav.dropdown-item text="Session" href="/" />
    <x-tkl-ui::nav.dropdown-item text="PHP Info" href="/" />
</x-tkl-ui::nav.dropdown>
