import Vue from 'vue'
import VueRouter, { Route, RouteConfig } from 'vue-router'
import Index from '@/views/Tracks/Index.vue'

Vue.use(VueRouter)

const routes: Array<RouteConfig> = [
  {
    path: '/',
    name: 'index',
    component: Index,
    meta: {
      layout: 'default',
      title: 'index',
      breadcrumbs: [
        { name: 'index', link: '/', class: '' }
      ],
      middleware: []
    }
  },
  {
    path: '/tracks',
    name: 'tracks',
    component: Index,
    meta: {
      layout: 'default',
      title: 'tracks',
      breadcrumbs: [
        { name: 'index', link: '/', class: '' },
        { name: 'tracks', link: '/tracks', class: '' }
      ],
      middleware: []
    }
  },
  {
    path: '/tracks/add',
    name: 'tracks-add',
    component: () => import(/* webpackChunkName: "audio-add" */ '../views/Tracks/Add.vue'),
    meta: {
      layout: 'default',
      title: 'tracks-add',
      breadcrumbs: [
        { name: 'index', link: '/', class: '' },
        { name: 'tracks', link: '/tracks', class: '' },
        { name: 'tracks-add', link: '/tracks/add', class: '' }
      ],
      middleware: []
    }
  },
  {
    path: '/tracks/edit/:id',
    name: 'tracks-audio-edit',
    component: () => import(/* webpackChunkName: "tracks-audio-edit" */ '../views/Tracks/Edit.vue'),
    meta: {
      layout: 'default',
      title: 'tracks-audio-edit',
      breadcrumbs: [
        { name: 'index', link: '/', class: '' },
        { name: 'tracks-audio-edit', link: '/', class: '' }
      ],
      middleware: []
    }
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

router.beforeEach((to: Route, from: Route, next: any) => {
  if (!Array.isArray(to.meta.middleware)) {
    return next()
  }

  if (to.meta.middleware.length === 0) {
    return next()
  }

  const middleware = to.meta.middleware
  const context = {
    to,
    from,
    next
  }
  return middleware[0]({
    ...context
  })
})

export default router
