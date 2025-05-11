import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router)

export default new Router({
  scrollBehavior () {
    return { x: 0, y: 0 }
  },
  routes: [{
    path: '/',
    name: 'dashboard',
    component: () => import('../views/dashboard/Dashboard.vue')
  }, {
    path: '/calls',
    name: 'calls',
    component: () => import('../views/calls/CallList.vue'),
  }, {
    path: '/sippeers',
    name: 'sippeers',
    component: () => import('../views/sippeers/SippeersList.vue'),
  }, {
    path: '/sippeers/create',
    name: 'sippeers.create',
    component: () => import('../views/sippeers/SippeersCreate.vue'),
  }, {
    path: '/sippeers/:id(\\d+)',
    name: 'sippeers.edit',
    component: () => import('../views/sippeers/SippeersEdit.vue'),
  }, {
    path: '/dialplans',
    name: 'dialplans',
    component: () => import('../views/dialplans/DialplanList.vue'),
  }, {
    path: '/dialplans/create',
    name: 'dialplan.create',
    component: () => import('../views/dialplans/DialplanCreate.vue'),
  }, {
    path: '/dialplans/:id(\\d+)',
    name: 'dialplan.edit',
    component: () => import('../views/dialplans/DialplanEdit.vue'),
  }, {
    path: '/extensions',
    name: 'extensions',
    component: () => import('../views/extensions/ExtensionList.vue'),
  }, {
    path: '/extensions/create',
    name: 'extension.create',
    component: () => import('../views/extensions/ExtensionCreate.vue'),
  }, {
    path: '/extensions/:id(\\d+)',
    name: 'extension.edit',
    component: () => import('../views/extensions/ExtensionEdit.vue'),
  }, {
    path: '/system',
    name: 'system',
    component: () => import('../views/system/SystemDashboard.vue')
  }, {
    path: '/logs',
    name: 'logs',
    component: () => import('../views/logs/LogsList.vue')
  },
  // settings/users
  {
    path: '/settings/users',
    name: 'settings.users',
    component: () => import('../views/settings/users/UsersList.vue')
  }, {
    path: '/settings/users/:id(\\d+)',
    name: 'settings.users.create',
    component: () => import('../views/settings/users/UsersCreate.vue')
  }, {
    path: '/settings/users/create',
    name: 'settings.users.edit',
    component: () => import('../views/settings/users/UsersCreate.vue')
  },
  // settings/configs
  {
    path: '/settings/configs',
    name: 'settings.configs',
    component: () => import('../views/settings/configs/ConfigsList.vue')
  }, {
    path: '/sniffer',
    name: 'sniffer',
    component: () => import('../views/sniffer/PacketsList.vue')
  },
  {
    path: '/sniffer/sip-session/:callId',
    name: 'sniffer.sip.session',
    component: () => import('../views/sniffer/SipSession.vue'),
    props: true 
  }
  
]
});
