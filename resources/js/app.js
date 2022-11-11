import Vue from 'vue';
import VueRouter from 'vue-router';
import * as Sentry from '@sentry/browser';
import * as Integrations from '@sentry/integrations';
import {TYMIRIUM_CONFIG} from './config';

import routes from './routes';
import store from './store.js';

// Sentry.init({
//     dsn: TYMIRIUM_CONFIG.SENTRY_VUE_DSN,
//     integrations: [new Integrations.Vue({Vue, attachProps: true, logErrors: true})],
// });

Vue.use(VueRouter);
Vue.config.performance = true;

let router = new VueRouter(routes);

const waitForStorageToBeReady = async (to, from, next) => {
    await store.restored;
    next()
};

Vue.mixin({
    methods: {
        slugify: function (string) {
            const a = 'àáâäæãåāăąçćčđďèéêëēėęěğǵḧîïíīįìłḿñńǹňôöòóœøōõőṕŕřßśšşșťțûüùúūǘůűųẃẍÿýžźż·/_,:;'
            const b = 'aaaaaaaaaacccddeeeeeeeegghiiiiiilmnnnnoooooooooprrsssssttuuuuuuuuuwxyyzzz------'
            const p = new RegExp(a.split('').join('|'), 'g')

            return string.toString().toLowerCase()
                .replace(/\s+/g, '-') // Replace spaces with -
                .replace(p, c => b.charAt(a.indexOf(c))) // Replace special characters
                .replace(/&/g, '-and-') // Replace & with 'and'
                .replace(/[^\w\-]+/g, '') // Remove all non-word characters
                .replace(/\-\-+/g, '-') // Replace multiple - with single -
                .replace(/^-+/, '') // Trim - from start of text
                .replace(/-+$/, '') // Trim - from end of text
        }
    }
});

router.beforeEach(waitForStorageToBeReady);

const app = new Vue({
    router: router,
    store
}).$mount('#syngent-app-wrapper');
