/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

import router from './router';
import store from './store';

// Vuetify
import Vuetify from 'vuetify';
import ru from 'vuetify/es5/locale/ru';
Vue.use(Vuetify);

// Moment JS
import moment from 'moment-timezone';
Vue.filter('moment', (value, format, timezone) => {
  return moment.tz(value, timezone || 'Asia/Yekaterinburg').format(format);
});

import momentDurationFormatSetup from 'moment-duration-format';
momentDurationFormatSetup(moment);
Vue.filter('duration', (value, units, format) => {
  return moment.duration(value, units).format(format, {
    trim: false
  });
});

moment.locale('ru');

/**
 * Clipboard
 * https://github.com/Inndy/vue-clipboard2
 */
import VueClipboard from 'vue-clipboard2';
Vue.use(VueClipboard);

/**
 * Notifications
 * https://github.com/euvl/vue-notification
 */
import Notifications from 'vue-notification';
Vue.use(Notifications);

/**
 * Yandex Maps
 * https://yandex-maps-unofficial.github.io/vue-yandex-maps/guide/vue2.html
 */
import { createYmapsVue2 } from 'vue-yandex-maps';
Vue.use(createYmapsVue2({
  apikey: '682a76c5-43e6-4830-8ffa-4cfc4e2a5579',
}));

// Currency
Vue.filter('monetary', function (value, options = {}) {
  let formatter = new Intl.NumberFormat('ru-RU', Object.assign({ style: 'currency', currency: 'RUB' }, options));
  return formatter.format(value);
});

/**
 * Phone mask
 * https://thewebdev.info/2022/03/13/how-to-use-mask-in-a-vue-js-vuetify-text-field
 */
import { VueMaskDirective } from 'v-mask';
Vue.directive('mask', VueMaskDirective);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('app-component', require('./App.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

new Vue({
  router,
  store,
  vuetify: new Vuetify({
    lang: {
      locales: { ru },
      current: 'ru'
    },
    icons: {
      iconfont: 'md'
    }
  }),
  el: 'main'
});