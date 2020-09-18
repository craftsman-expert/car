import Vue from 'vue'

import './styles/quasar.sass'
import 'quasar/dist/quasar.ie.polyfills'
import '@quasar/extras/material-icons/material-icons.css'
import '@mdi/font/css/materialdesignicons.css'
import { Notify, Quasar, Cookies, Dialog, Screen, date } from 'quasar'

// @ts-ignore
import QMediaPlayer from '@quasar/quasar-ui-qmediaplayer'
import '@quasar/quasar-ui-qmediaplayer/dist/index.css'

Vue.use(QMediaPlayer)

Vue.use(Quasar, {
  config: {
    notify: {}
  },
  components: { /* not needed if importStrategy is not 'manual' */ },
  directives: { /* not needed if importStrategy is not 'manual' */ },
  plugins: {
    Screen,
    Dialog,
    Cookies,
    Notify
  }
})
