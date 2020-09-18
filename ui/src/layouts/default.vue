<template>
  <q-layout view="lHh lpR fFf">
    <q-header
      ref="main_header"
      class="bg-white text-grey-8"
      :height-hint="58"
    >
      <q-toolbar class="bg-image" >
        <div class="full-width row no-wrap q-mt-md q-pb-lg">
          <div class="col-8 offset-2">
            <q-input
              v-model="search"
              color="bg-grey-7"
              placeholder="Поиск"
              class="q-ml-md"
              dark
            >
              <template v-slot:prepend>
                <q-btn
                  v-if="search === ''"
                  icon="mdi-magnify"
                  flat
                  dense
                  rounded
                  class="cursor-pointer"
                />
                <q-btn
                  v-else
                  icon="mdi-close"
                  flat
                  dense
                  rounded
                  class="cursor-pointer"
                  @click="search = ''"
                />
              </template>
            </q-input>
          </div>
        </div>
      </q-toolbar>
    </q-header>
    <q-drawer
      v-model="drawer"
      :mini="!drawer || miniState"
      side="left"
      :width="250"
      :breakpoint="500"
      show-if-above
      content-style="green"
      content-class=""
      dark
      @mouseover="miniState = false"
      @mouseout="miniState = true"
    >
      <q-scroll-area class="fit">
        <q-list
          dark
          padding
          class="rounded-borders"
          style="margin-bottom: auto"
        >
          <q-item style="margin-bottom: 15px">
            <q-item-section
              avatar
            >
              <q-icon name="mdi-animation-play" style="color: #ffe500 !important;" />
            </q-item-section>
          </q-item>
          <template
            v-for="(item, key) in mainMenu"
          >
            <q-separator
              :key="key"
              color="white"
              inset=""
              v-if="item.divider"
              spaced=""
            />
            <q-item
              v-else
              :key="key"
              v-ripple
              active
              clickable
              :to="item.to"
            >
              <q-item-section
                avatar
                style="color: #ffeb3b;"
              >
                <q-icon :name="item.icon" />
              </q-item-section>
              <q-item-section style="color: #ffeb3b;">
                {{ item.name }}
              </q-item-section>
              <q-item-section
                v-if="item.side"
                avatar
                style="color: #ffeb3b;"
              >
                <q-btn
                  round
                  flat
                  :icon="item.side.icon"
                  :to="item.side.to"
                />
              </q-item-section>
            </q-item>
          </template>
        </q-list>
      </q-scroll-area>
    </q-drawer>
    <q-page-container>
      <q-page
        class="background"
      >
        <q-scroll-area :style="{height: `${$screenHeight - 100}px`}">
          <div class="row no-wrap q-mt-md">
            <div class="col-8 offset-2">
              <router-view />
            </div>
          </div>
        </q-scroll-area>
      </q-page>
    </q-page-container>
    <div
      v-if="screenDevVisible"
      class="text-grey-5"
      style="left: 100px; bottom: 30px; display: block; position: absolute; z-index: 5000; font-size: 13px; pointer-events: none;"
    >
      <div
        class="for-dev-info"
      >
        <span>screen.width: {{ $q.screen.width }}</span><br>
        <span>screen.height: {{ $q.screen.height }}</span><br>
        <span>screen.name: {{ $q.screen.name }}</span><br>
        <span>screen.gt: {{ $q.screen.gt }}</span><br>
        <span>screen.sizes: {{ $q.screen.sizes }}</span><br>
      </div>
    </div>
  </q-layout>
</template>

<script lang="ts">

import Vue from 'vue'

export default Vue.extend({
  data () {
    return {
      screenDevVisible: false,
      search: '',
      drawer: false,
      drawerRight: true,
      miniState: true
    }
  },

  watch: {
    search (q: string) {
      this.$root.$emit('on-search', q)
    }
  },

  computed: {
    drawerRightIsVisible () {
      return !/^\/dashboard/.test(this.$route.path)
    },
    title () {
      return this.$t(`titles.${this.$route.meta.title}`)
    },
    breadcrumbs () {
      return this.$route.meta.breadcrumbs || []
    },
    accountMenu () {
      return [
        {
          name: 'billing',
          icon: 'mdi-cash-usd-outline',
          to: '/billing/plan'
        },
        {
          name: 'settings',
          icon: 'mdi-wrench-outline',
          to: '/settings/profile'
        },
        {
          divider: true
        },
        {
          name: 'sign_out',
          icon: 'mdi-exit-run',
          handler: () => {
            this.logOut()
          }
        }
      ]
    },
    mainMenu () {
      return [
        {
          name: 'Главная',
          icon: 'mdi-home',
          to: '/'
        },
        {
          divider: true
        },
        {
          name: 'Треки',
          icon: 'mdi-folder-music-outline',
          to: '/tracks',
          side: {
            icon: 'mdi-plus',
            to: '/tracks/add'
          }
        },
        {
          name: 'Исполнители',
          icon: 'mdi-account-tie-voice',
          to: '/performers'
        },
        {
          divider: true
        },
        {
          name: 'Настройки',
          icon: 'mdi-cog',
          to: '/settings'
        },
        {
          name: 'Помощь',
          icon: 'mdi-help-circle-outline',
          to: '/help'
        }
      ]
    },
    pageHeight () {
      // todo: Вычисление высоты клиентской области для дочерней страницы
      return `${this.$q.screen.height - 95}px`
    }
  },

  mounted () {
    this.$root.$on('on-keydown', this.onKeyDown)
    this.$root.$on('on-keyup', this.onKeyUp)
  },

  destroyed () {
    this.$root.$off('on-keydown', this.onKeyDown)
    this.$root.$off('on-keyup', this.onKeyUp)
  },

  methods: {
    logOut () {
      this.$q.dialog({
        title: 'Запрос подтверждения!',
        persistent: true,
        message: 'Вы действительно хотите выйти?',
        ok: {
          outline: true,
          label: this.$t('yes')
        },
        cancel: {
          outline: true,
          label: this.$t('cancel')
        }
      }).onOk(() => {
        this.$q.cookies.remove('access_token')
        this.$q.cookies.remove('refresh_token')
        this.$router.replace('/login')
      })
    },

    onKeyDown (e: KeyboardEvent) {
      if (e.code === 'ControlLeft') {
        this.screenDevVisible = true
      }
    },

    onKeyUp (e: KeyboardEvent) {
      if (e.code === 'ControlLeft') {
        this.screenDevVisible = false
      }
    }
  }
})
</script>

<style lang="scss">
  html body {
    font-family: "PF DinDisplay Pro", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    font-size: 16px;
    word-spacing: 2px;
    -ms-text-size-adjust: 100%;
    -webkit-text-size-adjust: 100%;
    -moz-osx-font-smoothing: grayscale;
    -webkit-font-smoothing: antialiased;
    box-sizing: border-box;
  }

  *, *:before, *:after {
    box-sizing: border-box;
    margin: 0;
  }

  .background {
    background-color: white;
  }

  .for-dev-info {
    border-style: solid;
    padding: 10px;
    border-width: 1px;
    border-radius: 5px;
    box-shadow: 0 0 8px -4px #e6ad25;
  }

  .bg-image {
    background-image: url("https://wavetoysmusic.com/wp-content/uploads/drums-background-music.jpg") ;
    -moz-background-size: 100%;
    -webkit-background-size: 100%;
    -o-background-size: 100%;
    background-size: 100%;
  }
</style>
