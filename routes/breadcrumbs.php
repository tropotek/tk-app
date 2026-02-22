<?php // routes/breadcrumbs.php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', url('/'));
});

Breadcrumbs::for('ideas', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Ideas', url('/ideas'));
});

