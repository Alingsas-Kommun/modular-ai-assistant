import '../scss/frontend.scss';
import Alpine from 'alpinejs';
import { marked } from 'marked';

import './components/module';

// Make marked available globally
window.marked = marked;

// Initialize Alpine
window.Alpine = Alpine;
Alpine.start();