<template>
  <component
    :is="layout"
    tabindex="-1"
    @keydown.ctrl="$root.$emit('on-keydown-ctrl')"
    @keyup.ctrl="$root.$emit('on-keyup-ctrl')"
  />
</template>

<script lang="ts">

import Vue from 'vue'

export default Vue.extend({
  name: 'LayoutDefault',

  data () {
    return {
      leftDrawerOpen: false
    }
  },

  mounted () {
    window.addEventListener('keyup', this.keyboardKeyUpHandler)
    window.addEventListener('keydown', this.keyboardKeyDownHandler)
  },

  beforeDestroy () {
    window.removeEventListener('keyup', this.keyboardKeyUpHandler)
    window.removeEventListener('keydown', this.keyboardKeyDownHandler)
  },

  computed: {
    layout () {
      return this.$route.meta.layout || 'clean'
    }
  },

  methods: {
    keyboardKeyUpHandler (e: KeyboardEvent) {
      this.$root.$emit('on-keyup', e)
    },

    keyboardKeyDownHandler (e: KeyboardEvent) {
      this.$root.$emit('on-keydown', e)
    }
  }
})
</script>

<style lang="scss">
  //body {
  //  overflow: hidden !important;
  //}
</style>
