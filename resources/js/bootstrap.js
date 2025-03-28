// bootstrap.js
import _ from 'lodash';
window._ = _;

import axios from 'axios';
import router from './router';

window.axios = axios;

axios.defaults.baseURL = 'http://localhost:8000/api';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

const csrfToken = document.head.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
}

const token = localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 401) {
            localStorage.removeItem('token');
            router.push({name: 'login'});
        }
        return Promise.reject(error);
    }
);

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
