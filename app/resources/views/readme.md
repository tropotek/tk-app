
# Form Components Developer Guide

This project includes reusable Blade form components provided by the `tk-base` package.

These components are intended to standardize:

- form layout
- field rendering
- view/edit/create modes
- validation styling
- button behavior
- grouped field layouts

## Available form components

### Form wrapper

- `x-tk-base::form`

### Buttons

- `x-tk-base::form.buttons.default-btns`
- `x-tk-base::form.buttons.link`
- `x-tk-base::form.buttons.submit`

### Fields

- `x-tk-base::form.fields.hidden`
- `x-tk-base::form.fields.input`
- `x-tk-base::form.fields.select`
- `x-tk-base::form.fields.checkbox`
- `x-tk-base::form.fields.radio`
- `x-tk-base::form.fields.file`
- `x-tk-base::form.fields.textarea`

### Form UI wrappers

- `x-tk-base::form.ui.fieldgroup`
- `x-tk-base::form.ui.fieldset`

---

## Form modes

The form system supports three modes:

- `view`
- `edit`
- `create`

These modes affect how fields render:

- in `view` mode, fields render as read-only/plain text where applicable
- in `edit` mode, fields render as editable controls
- in `create` mode, fields render as editable controls intended for new records

Example:
```
blade
<x-tk-base::form
method="post"
action="{{ route('users.store') }}"
mode="create"
>
    ...
</x-tk-base::form>
```
## Basic usage

A form is usually rendered from a controller by passing:

- `mode`
- `values`
- `action`
- `method`

### Controller example
```
php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create()
    {
        return view('pages.users.form', [
            'mode' => 'create',
            'values' => [],
            'action' => route('users.store'),
            'method' => 'post',
        ]);
    }

    public function edit(Request $request, int $id)
    {
        $user = [
            'title' => 'mr',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
        ];

        return view('pages.users.form', [
            'mode' => 'edit',
            'values' => $user,
            'action' => route('users.update', $id),
            'method' => 'put',
        ]);
    }

    public function show(Request $request, int $id)
    {
        $user = [
            'title' => 'mr',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
        ];

        return view('pages.users.form', [
            'mode' => 'view',
            'values' => $user,
            'action' => route('users.update', $id),
            'method' => 'put',
        ]);
    }
}
```
### Blade example
```
blade
@props([
    'mode' => 'view',
    'values' => [],
    'action' => '',
    'method' => 'post',
])

<x-layouts.main>
    <x-tk-base::form
        :method="$method"
        :action="$action"
        :mode="$mode"
    >
        <x-slot:buttons>
            <x-tk-base::form.buttons.default-btns
                editRoute="{{ route('users.edit', 1) }}"
                cancelRoute="{{ route('users.show', 1) }}"
            />
        </x-slot:buttons>

        <x-slot:fields>
            <x-tk-base::form.fields.hidden
                name="id"
                :value="$values['id'] ?? ''"
            />

            <x-tk-base::form.ui.fieldgroup class="col">
                <x-tk-base::form.fields.select
                    name="title"
                    :options="[
                        '' => '-- Select --',
                        'mr' => 'Mr',
                        'mrs' => 'Mrs',
                        'miss' => 'Miss',
                    ]"
                    fieldCss="col-sm-3"
                    :value="$values['title'] ?? ''"
                />

                <x-tk-base::form.fields.input
                    name="firstName"
                    fieldCss="col-sm-4"
                    required=""
                    :value="$values['firstName'] ?? ''"
                />

                <x-tk-base::form.fields.input
                    name="lastName"
                    fieldCss="col-sm-5"
                    :value="$values['lastName'] ?? ''"
                />
            </x-tk-base::form.ui.fieldgroup>

            <x-tk-base::form.ui.fieldset legend="Contact details" class="col">
                <x-tk-base::form.fields.input
                    name="email"
                    type="email"
                    :value="$values['email'] ?? ''"
                />

                <x-tk-base::form.fields.textarea
                    name="notes"
                    :value="$values['notes'] ?? ''"
                />
            </x-tk-base::form.ui.fieldset>
        </x-slot:fields>
    </x-tk-base::form>
</x-layouts.main>
```
---

## Form wrapper

Use `x-tk-base::form` as the root form component.

### Important behavior

- automatically includes CSRF protection
- automatically adds method spoofing for non-`GET`/`POST` requests
- automatically adds `multipart/form-data` when a file input is present
- exposes a mode-specific CSS class: `mode-view`, `mode-edit`, or `mode-create`

### Common props

| Prop | Required | Description |
|---|---|---|
| `mode` | yes | `view`, `edit`, or `create` |
| `method` | yes | HTTP method such as `post`, `put`, `patch`, `delete` |
| `action` | yes | form action URL |
| `buttons` | no | slot for action buttons |
| `fields` | no | slot for form fields |

Example:
```
blade
<x-tk-base::form
    method="put"
    action="{{ route('users.update', $userId) }}"
    mode="edit"
>
    <x-slot:buttons>
        ...
    </x-slot:buttons>

    <x-slot:fields>
        ...
    </x-slot:fields>
</x-tk-base::form>
```
---

## Buttons

## Default button set

`x-tk-base::form.buttons.default-btns` renders standard actions based on the current mode.

### Behavior by mode

- `view`: shows an edit link if `editRoute` is provided
- `edit`: shows a cancel link and a submit button
- `create`: shows a cancel link and a submit button

### Props

| Prop | Description |
|---|---|
| `editRoute` | route to switch to edit mode |
| `cancelRoute` | route to leave the form |
| `viewLabel` | label for the view-mode action |
| `editLabel` | label for the edit submit button |
| `createLabel` | label for the create submit button |
| `cancelLabel` | label for the cancel button |
| `viewCss`, `editCss`, `createCss`, `cancelCss` | button classes |

Example:
```
blade
<x-tk-base::form.buttons.default-btns
    editRoute="{{ route('users.edit', $userId) }}"
    cancelRoute="{{ route('users.show', $userId) }}"
/>
```
---

## Field layout wrappers

## Field group

Use `x-tk-base::form.ui.fieldgroup` to group related fields in a Bootstrap row.
```
blade
<x-tk-base::form.ui.fieldgroup class="col">
    <x-tk-base::form.fields.input
        name="firstName"
        fieldCss="col-sm-6"
        :value="$values['firstName'] ?? ''"
    />

    <x-tk-base::form.fields.input
        name="lastName"
        fieldCss="col-sm-6"
        :value="$values['lastName'] ?? ''"
    />
</x-tk-base::form.ui.fieldgroup>
```
Use this when you want related fields displayed together without a visual legend.

## Fieldset

Use `x-tk-base::form.ui.fieldset` when the group needs a title or stronger visual separation.
```
blade
<x-tk-base::form.ui.fieldset legend="Contact details" class="col">
    <x-tk-base::form.fields.input
        name="email"
        type="email"
        :value="$values['email'] ?? ''"
    />

    <x-tk-base::form.fields.input
        name="phone"
        :value="$values['phone'] ?? ''"
    />
</x-tk-base::form.ui.fieldset>
```
Use a fieldset when the grouped fields belong to a named section.

---

## Field components

Most field components support these common props:

| Prop | Description |
|---|---|
| `name` | input name |
| `value` | current value |
| `label` | optional label override |
| `fieldCss` | layout classes for the field wrapper |
| `help` | helper text |
| `errorText` | custom error text |

The components also use Laravel `old()` values automatically, so validation failures preserve user input.

## Hidden field
```
blade
<x-tk-base::form.fields.hidden
    name="id"
    :value="$values['id'] ?? ''"
/>
```
Use for IDs, tokens, or additional request metadata.

## Input field
```
blade
<x-tk-base::form.fields.input
    name="firstName"
    label="First name"
    fieldCss="col-sm-6"
    required=""
    :value="$values['firstName'] ?? ''"
/>
```
Useful for text, email, number, date, and similar input types.

You can also set `type` explicitly:
```
blade
<x-tk-base::form.fields.input
    name="dob"
    label="Date of birth"
    type="date"
    fieldCss="col-sm-6"
    :value="$values['dob'] ?? ''"
/>
```
## Select field
```
blade
<x-tk-base::form.fields.select
    name="title"
    :options="[
        '' => '-- Select --',
        'mr' => 'Mr',
        'mrs' => 'Mrs',
        'miss' => 'Miss',
    ]"
    fieldCss="col-sm-4"
    :value="$values['title'] ?? ''"
/>
```
### Notes

- supports option groups using nested arrays
- in `view` mode it renders as plain text
- array-style names such as `roles[]` are supported

Optgroup example:
```
blade
<x-tk-base::form.fields.select
    name="department"
    :options="[
        'Technical' => [
            'dev' => 'Development',
            'qa' => 'QA',
        ],
        'Operations' => [
            'ops' => 'Operations',
            'support' => 'Support',
        ],
    ]"
    :value="$values['department'] ?? ''"
/>
```
## Checkbox field
```
blade
<x-tk-base::form.fields.checkbox
    name="options[]"
    :options="[
        'option1' => 'Option 1',
        'option2' => 'Option 2',
        'option3' => 'Option 3',
    ]"
    :value="$values['options'] ?? []"
/>
```
For a switch-style checkbox:
```
blade
<x-tk-base::form.fields.checkbox
    name="best"
    label="Best option"
    :options="['1' => 'Yes']"
    :isSwitch="true"
    :value="$values['best'] ?? ''"
/>
```
### Notes

- use `name="options[]"` for multi-select arrays
- pass an array as the value for multiple selections
- in `view` mode the component renders icon-based output

## Radio field
```
blade
<x-tk-base::form.fields.radio
    name="status"
    :options="[
        'draft' => 'Draft',
        'published' => 'Published',
        'archived' => 'Archived',
    ]"
    :value="$values['status'] ?? ''"
/>
```
Use when exactly one option should be selected.

## File field
```
blade
<x-tk-base::form.fields.file
    name="avatar"
    label="Profile image"
    help="Upload an image file"
    value="<a href='/storage/example.jpg' target='_blank'>Current file</a>"
/>
```
### Notes

- the form wrapper automatically applies `multipart/form-data` when a file input is present
- in `view` mode, the `value` prop is displayed as markup
- use the `value` prop to show a file link or file summary for existing uploads

## Textarea field
```
blade
<x-tk-base::form.fields.textarea
    name="description"
    label="Description"
    :value="$values['description'] ?? ''"
/>
```
Use for multi-line text values.

---

## Validation and old input

The form fields integrate with Laravel validation behavior:

- field values are restored using `old(...)`
- invalid fields receive Bootstrap invalid classes when validation errors exist

Example controller action:
```
php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string'],
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'description' => ['nullable', 'string'],
        ]);

        // Save record...

        return redirect()->route('users.index');
    }
}
```
Because the field components use `old()`, submitted values are repopulated automatically after validation errors.

---

## Recommended project usage pattern

For consistency across the project:

1. keep the controller responsible for loading the record and preparing `mode`, `values`, `method`, and `action`
2. keep the Blade view responsible for rendering fields
3. use `fieldgroup` for layout grouping
4. use `fieldset` for named sections
5. use `default-btns` unless the page needs custom actions
6. use `fieldCss` for grid layout instead of custom inline styles

---

## Example full form
```
blade
@props([
    'mode' => 'view',
    'values' => [],
    'action' => '',
    'method' => 'post',
])

<x-layouts.main>
    <x-tk-base::form
        :method="$method"
        :action="$action"
        :mode="$mode"
    >
        <x-slot:buttons>
            <x-tk-base::form.buttons.default-btns
                editRoute="{{ route('users.edit', $values['id'] ?? 0) }}"
                cancelRoute="{{ route('users.index') }}"
            />
        </x-slot:buttons>

        <x-slot:fields>
            <x-tk-base::form.fields.hidden
                name="id"
                :value="$values['id'] ?? ''"
            />

            <x-tk-base::form.ui.fieldgroup class="col">
                <x-tk-base::form.fields.select
                    name="title"
                    :options="[
                        '' => '-- Select --',
                        'mr' => 'Mr',
                        'mrs' => 'Mrs',
                        'miss' => 'Miss',
                    ]"
                    fieldCss="col-sm-3"
                    :value="$values['title'] ?? ''"
                />

                <x-tk-base::form.fields.input
                    name="firstName"
                    fieldCss="col-sm-4"
                    required=""
                    :value="$values['firstName'] ?? ''"
                />

                <x-tk-base::form.fields.input
                    name="lastName"
                    fieldCss="col-sm-5"
                    :value="$values['lastName'] ?? ''"
                />
            </x-tk-base::form.ui.fieldgroup>

            <x-tk-base::form.ui.fieldset legend="Profile" class="col">
                <x-tk-base::form.fields.input
                    name="email"
                    type="email"
                    :value="$values['email'] ?? ''"
                />

                <x-tk-base::form.fields.checkbox
                    name="interests[]"
                    :options="[
                        'music' => 'Music',
                        'sport' => 'Sport',
                        'travel' => 'Travel',
                    ]"
                    :value="$values['interests'] ?? []"
                />

                <x-tk-base::form.fields.radio
                    name="status"
                    :options="[
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]"
                    :value="$values['status'] ?? ''"
                />

                <x-tk-base::form.fields.file
                    name="avatar"
                    label="Avatar"
                    help="Upload a profile image"
                    value="<a href='/storage/avatar.jpg' target='_blank'>Current avatar</a>"
                />

                <x-tk-base::form.fields.textarea
                    name="description"
                    :value="$values['description'] ?? ''"
                />
            </x-tk-base::form.ui.fieldset>
        </x-slot:fields>
    </x-tk-base::form>
</x-layouts.main>
```
---

## Summary

Use the form component system when building CRUD-style pages to keep templates predictable and consistent.

### In short

- wrap everything in `x-tk-base::form`
- pass `mode`, `method`, and `action`
- put buttons in the `buttons` slot
- put fields in the `fields` slot
- use `fieldgroup` for layout rows
- use `fieldset` for titled sections
- pass values from the controller
- let the components handle readonly/view behavior and old input restoration

That way the forms stay tidy, consistent, and much less likely to grow mysterious one-off markup tentacles.
