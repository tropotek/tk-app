import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
window.Alpine = Alpine;

import '@tkl-ui/js/form.js';

import confirmModal from '@tkl-ui/js/alpine/confirmModal.js';

Alpine.plugin(confirmModal);

// Livewire/Alpine initialization
Livewire.start();


// global JS config object
// window.appConfig = {
//     isProd: false,
// };

