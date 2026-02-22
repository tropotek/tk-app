@aware([ 'preText' => '', 'postText' => ''])
@props([
    // required
    'help' => '',
    // optional
    'preText' => '',
    'postText' => ''
])
<div class="form-text text-secondary">{!! $preText !!}<small>{!! $help !!}</small>{!! $postText !!}</div>
