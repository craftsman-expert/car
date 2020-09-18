import Vue from 'vue'
import axios, { AxiosInstance, AxiosResponse } from 'axios'
import { Cookies } from 'quasar'
import { app } from '@/main'

// Full config:  https://github.com/axios/axios#request-config
// axios.defaults.baseURL = process.env.baseURL || process.env.apiUrl || '';
// axios.defaults.headers.common['Authorization'] = AUTH_TOKEN;
// axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';

const config = {
  baseURL: process.env.VUE_APP_API,
  timeout: 30000,
  validateStatus (status: number) {
    return status < 500 // Resolve only if the status code is less than 500
  }
}

const _axios: AxiosInstance = axios.create(config)
const _axiosForRefresh: AxiosInstance = axios.create(config)

_axios.interceptors.request.use(
  async function (config) {
    return config
  },
  function (error) {
    // Do something with request error
    return Promise.reject(error)
  }
)

/*
  Array of patterns to ignore status checks.
 */

const PATTERNS_FOR_IGNORE_RESPONSE_INTERCEPTOR = [
  /\/account\/profile\/change-password/
]

// Add a response interceptor
_axios.interceptors.response.use(
  function (response: AxiosResponse) {
    for (let i = 0; i < PATTERNS_FOR_IGNORE_RESPONSE_INTERCEPTOR.length; i++) {
      const regx: RegExp = PATTERNS_FOR_IGNORE_RESPONSE_INTERCEPTOR[i]
      // @ts-ignore
      if (regx.test(response.config.url)) {
        return response
      }
    }

    // If not authorized then go to login page
    if (response.status === 401) {
      app.$router.replace('/login')
    }

    return response
  },
  function (error) {
    // Do something with response error
    return Promise.reject(error)
  }
)

class AxiosPlugin {
  install () {
    Object.defineProperties(Vue.prototype, {
      axios: {
        get () {
          return _axios
        }
      },
      $axios: {
        get () {
          return _axios
        }
      }
    })
  }
}

const axiosPlugin: AxiosPlugin = new AxiosPlugin()

Vue.use(axiosPlugin)

export default axiosPlugin
export const $axios: AxiosInstance = _axios
