import './bootstrap';
import EmailInteractions from './modules/email-interactions.js';

class ScheduleSyncApp {
    constructor() {
        this.calendar = null;
        this.forms = new Map();
        this.emailInteractions = new EmailInteractions();
        this.init();
    }

    init() {
        // Application initialization logic goes here.
        console.log('ScheduleSyncApp initialized.');
    }
}

new ScheduleSyncApp();
