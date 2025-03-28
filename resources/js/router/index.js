import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

const routes = [
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/auth/Login.vue'),
    meta: { requiresAuth: false },
  }, {
    path: '/register',
    name: 'register',
    component: () => import('../views/auth/Register.vue'),
    meta: { requiresAuth: false },
  }, {
    path: '/',
    name: 'dashboard',
    component: () => import('../views/dashboard/Dashboard.vue'),
    meta: { requiresAuth: true },
  }, {
    path: '/calls',
    name: 'calls',
    component: () => import('../views/calls/CallList.vue'),
    meta: { requiresAuth: true },
  },
  
];

const router = new Router({
  routes,
});

// Защищаем маршруты с меткой requiresAuth
// Защищаем маршруты с меткой requiresAuth
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  
  // Проверка на защищённые маршруты
  if (to.matched.some(record => record.meta.requiresAuth) && !token) {
    next({ name: 'login' });
  } else if (to.name === 'login' && token) {
    // Если пользователь уже авторизован, и он пытается попасть на страницу логина
    next({ name: 'dashboard' });
  } else {
    next();
  }
});

export default router;
