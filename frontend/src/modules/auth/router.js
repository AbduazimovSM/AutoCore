export default [
  {
    path: '/auth/login',
    name: 'login',
    component: () => import('./pages/Login.vue'),
  },
  {
    path: '/auth/register',
    name: 'register',
    component: () => import('./pages/Register.vue'),
  }
];