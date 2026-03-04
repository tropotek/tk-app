# Sis Templating Conventions


## View Directory structure

View base directory structure:

```
views/
    pages/
    components/
    emails/
    ...
```

Controller view templates to be stored in a subdirectory matching the controllers directory name. EG: `pages/{controller-dir}`.
This will make locating views and controllers easier.


## Component Directory structure

Component templates are grouped in subdirectories related to their areas of responsibility.

Component base directory structure:
```
components/
    form/
    page/           // For all top-level page layouts (`<html>`)
        meta/       // Contains page headers and footers, etc.
    layouts/        // Contains reusable content layouts, 3-col, 2-col, etc
    nav/
    ui/             // Single UI components used within pages (breadcrumbs, flashmessages, etc)
    ...
```

When a new component has many subcomponents, create a subdirectory for those components.


## Page Layouts

When creating a view for a controller, ensure that any `script` and `style` tags are placed inside the layout component tag, added tags outside the main layout component can interfere with the parent layout if its not expecting them. If it is necessary to add a `script` or `style` tag outside the layout component, consider creating a new parent layout component.
```
<x-layouts.main>
  <style>...</style>
  ...
  <script>...</script>
</x-layouts.main>
```

## Styles And CSS Classes

Avoid adding the Bootstrap css classes `container` and `container-fluid` within your page layouts. Let the parent layout page determine the content styling. Create a new layout template with the layout styles needed if a layout does not meet your requirements.

Don't add the `style` tag directly to any element unless it is absolutely necessary. Add a CSS class to your template using a style tag or add it to the global stylesheet if it will be used on multiple pages.







