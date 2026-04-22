# README for Alpine Plugin

## Introduction

Welcome to the **Alpine Plugin** documentation. This plugin provides a utility function called `confirmModal` which can be used to display a confirmation modal in your Laravel application. The modal is styled using Bootstrap and allows users to confirm or cancel actions.

## Installation

Before you can use the `confirmModal` plugin, ensure that the following dependencies are installed:

- **Bootstrap**: Ensure that Bootstrap is included in your project. You can do this by including the Bootstrap CSS and JavaScript files in your HTML.

```html
<!-- Include Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>

<!-- Include Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```


## Usage

The `confirmModal` function is exported as a global in the browser window. You can use it by importing the plugin into your JavaScript file.

```html
<!-- Include confirmModal.js -->
<script src="/path/to/confirmModal.js"></script>
```

Alternativly, if using VITE you can add plugin to your `app.js` file.
```javascript
// ...
import confirmModal from '/path/to/confirmModal.js';
Alpine.plugin(confirmModal);
// ...
```


### Basic Example

To use the `confirmModal` plugin, you can add an Alpine directive to any element that has a click event listener.

```html
<button x-confirm="'Are you sure you want to delete this item?'">Delete</button>
```

In the above example, when the button is clicked, a confirmation modal will be displayed asking if the user wants to delete the item.


### Advanced Example

You can also use the `confirmModal` function directly in your JavaScript code.

```javascript
import confirmModal from '/path/to/confirmModal.js';

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('myButton').addEventListener('click', async (e) => {
        e.preventDefault();

        const confirmed = await confirmModal.prompt('Please enter a confirmation message:');
        
        if (confirmed) {
            console.log(confirmed);
        }
    });
});
```

In this advanced example, the `prompt` method is used to display an input modal for collecting user input. The confirmed value will be logged to the console.


## Customization

The `confirmModal` function allows you to customize the message displayed in the confirmation modal by passing a custom message as a string or using expression evaluation.

```html
<button x-confirm="'Are you sure you want to delete {{ $item->name }}?'">Delete</button>
```

In this example, the `$item->name` will be evaluated and used as the message for the confirmation modal.


## Notes

The `confirmModal` plugin is designed to be straightforward and reusable. You can integrate it into any part of your Laravel application.
