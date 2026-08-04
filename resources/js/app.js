console.log('APP JS LOADED');

import './bootstrap';
import './master';
import './notification';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
