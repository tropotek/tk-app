# TK Base Package

A package to hold useful objects and templates for in-house web applications.


## Contents

- [Form Templates](../resources/views/components/form/readme.md)
- [Menu Builder](./Menu/readme.md)
- [Breadcrumbs](./Breadcrumbs/readme.md)


## VarDump Debug logger (/src/Debug/VarDump.php)
Use this to send messages to the debug log file. Send strings, objects, and arrays.
The output will show booleans and null values, so you can identify them easily.

The messages can be seen in the `storage/logs/laravel.log` file or when using the `debugBar` package.

Usage:
```php
// view a dump of the entire object in messages:
vd($object);
// use this to see the output along with the trace of the execution:
vdd($object);
```

Tail the laravel log file with:
```bash
$ tail -f storage/logs/laravel.log
```



## Util Classes (/src/Utils)

- `\Tk\Util\File`: filesystem related functions
- `\Tk\Util\Form`: Form formating and template helpers


