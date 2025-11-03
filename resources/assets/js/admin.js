import '../scss/admin.scss';
import Alpine from 'alpinejs';
import { marked } from 'marked';
import './components/module';
import './components/editorIntegration';
import './components/modelTest';
import './components/apiKeyConfiguration';

// Make marked available globally
window.marked = marked;

// Initialize Alpine
window.Alpine = Alpine;
Alpine.start();