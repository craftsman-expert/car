import Vue from 'vue'
import { Notify } from 'quasar'

// Default
const mainOptions = {
  timeout: 4000,
  position: 'top-right'
}

export interface NotifyInterface {
  success (message: string, options?: any): Function;
  warning (message: string, options?: any): Function;
  error (message: string, options?: any): Function;
  info (message: string, options?: any): Function;
  notify (options: any): Function;
}

export class MyNotify implements NotifyInterface {
  /**
   *
   * @param message
   * @param options
   */
  warning (message: string, options = {}): Function {
    const opt = Object.assign(options, { message, color: 'orange', textColor: 'white' })
    return this.notify(opt)
  }

  /**
   * Show if successful
   * @param message
   * @param options
   * @returns {Function}
   */
  success (message: string, options = {}): Function {
    const opt = Object.assign(options, { message, color: 'green', textColor: 'white' })
    return this.notify(opt)
  }

  /**
   * Show if critical error
   * @param message
   * @param options
   * @returns {Function}
   */
  error (message: string, options = {}): Function {
    const opt = Object.assign(options, { message, color: 'red', textColor: 'white' })
    return this.notify(opt)
  }

  /**
   * For information
   * @param message
   * @param options
   * @returns {Function}
   */
  info (message: string, options = {}): Function {
    const opt = Object.assign(options, { message, color: 'blue', textColor: 'white' })
    return this.notify(opt)
  }

  /**
   * Custom notify
   * @param options
   * @returns {Function}
   */
  notify (options: any): Function {
    return Notify.create(Object.assign(options, mainOptions))
  }
}

const $notify = new MyNotify()

class NotifyPlugin {
  install () {
    Object.defineProperties(Vue.prototype, {
      $notify: {
        get (): MyNotify {
          return $notify
        }
      }
    })
  }
}

Vue.use(new NotifyPlugin())
