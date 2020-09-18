import Vue, { CreateElement } from 'vue'
import i18n from './i18n'
import App from './App.vue'
import './registerServiceWorker'
import router from './router'
import store from './store'
import './quasar'

// Mixins
import './mixins/global'

// Plugins
import './axios'
import './notify'

// Styles
import './assets/scss/style.scss'

// layouts
import DefaultLayout from './layouts/default.vue'
import CleanLayout from './layouts/clean.vue'

Vue.component('default', DefaultLayout)
Vue.component('clean', CleanLayout)

Vue.config.productionTip = false

export const app: Vue = new Vue({
  router,
  store,
  i18n,
  render: (h: CreateElement) => h(App)
}).$mount('#app')
