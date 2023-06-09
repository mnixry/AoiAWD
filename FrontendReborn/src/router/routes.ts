import { RouteRecordRaw } from 'vue-router';

export const LOGIN_PAGE_NAME = 'login';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/IndexPage.vue') },
      {
        path: '/waf/add',
        component: () => import('pages/WafAddRulePage.vue'),
      },
    ],
  },
  {
    path: '/login',
    name: LOGIN_PAGE_NAME,
    component: () => import('pages/LoginPage.vue'),
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
];

export default routes;
