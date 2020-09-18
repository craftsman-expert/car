import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'

Vue.use(Vuex)

export interface StoreContext {
  commit: (type: string, payload: any, options?: any) => void;
  dispatch: (type: string, payload: any) => void;
  getters: Record<string, any>;
  rootGetters: Record<string, any>;
  rootState: Record<string, any>;
  state: Record<string, any>;
}

export default new Vuex.Store({
  state: {
  },
  mutations: {
  },
  actions: {
  },
  modules: {},
  plugins: [
    createPersistedState({
      key: 'car-main',
      paths: [],
      storage: {
        getItem: key => get(key),
        setItem: (key, value) => set(key, value),
        removeItem: key => remove(key)
      }
    })
  ]
})

function get (key: string) {
  return localStorage.getItem(key)
}

function set (key: string, value: any) {
  return localStorage.setItem(key, value)
}

function remove (key: string) {
  return localStorage.removeItem(key)
}
