// app.js
import './bootstrap';
import Vue from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';

import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';
import Notifications from 'vue-notification'

Vue.use(Vuetify);
Vue.use(Notifications);
const vuetify = new Vuetify({
    icons: {
        iconfont: 'mdi' // Использование Material Design Icons
    }
});

new Vue({
    router,
    vuetify,
    store,
    render: h => h(App),
}).$mount('#app');
