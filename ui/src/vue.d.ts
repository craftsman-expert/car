// 1. Make sure to import 'vue' before declaring augmented types
import { NotifyInterface } from '@/notify'
import { Store } from 'vuex/types'

// 2. Specify a file with the types you want to augment
//    Vue has the constructor type in types/vue.d.ts
declare module 'vue/types/vue' {
  // 3. Declare augmentation for Vue

  interface Vue {
    $notify: NotifyInterface;
    $store: Store<any>;
    isDev: boolean;
    $locale: string;
    $rules: (...names: string[]) => void;
  }
}

declare module 'axios'
// @ts-ignore
declare module '@quasar/quasar-ui-qmediaplayer'
