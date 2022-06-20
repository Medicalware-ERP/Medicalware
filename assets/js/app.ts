/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../../node_modules/normalize.css/normalize.css'
import '../styles/app.scss';
import '@fortawesome/fontawesome-free/css/all.css';
import Alpine from 'alpinejs';

// start the Stimulus application
import '../bootstrap';
window.Alpine = Alpine;

Alpine.start();

if (Array.from(document.querySelectorAll('select[multiple]')).length > 0) {
    import('jquery').then(jq => {
        import('select2').then(() => {
            jq.default('select[multiple]').select2()
        });
    });
}

