import {Controller} from 'stimulus';
import Turbolinks from 'turbolinks';

export default class extends Controller {
    /**
     *
     */
    initialize() {
        this.turbo();
    }

    /**
     * Initialization & configuration Turbolinks
     */
    turbo() {
        if (!Turbolinks.supported) {
            console.warn('Turbo links is not supported');
            return;
        }

        Turbolinks.start();
        Turbolinks.setProgressBarDelay(100);
    }

    /**
     *
     */
    goToTop() {
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    }
}
