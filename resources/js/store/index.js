import Vue from "vue";
import Vuex from 'vuex';

import auth from './auth';
import calls from './calls';

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        auth,
        calls
    }
})