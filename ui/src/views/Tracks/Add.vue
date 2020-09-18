/* eslint-disable */
<template>
  <div class="q-pa-none">
    <div class="row">
      <div class="col-grow q-pa-none">
        <q-card
          square
          flat
        >
          <q-parallax
            src="/img/music.jpg"
            :height="250"
          />
          <q-card-section class="q-pa-none">
            <div class="text-grey-8 text-h6">
              Title
            </div>
            <div class="text-grey-6 text-subtitle2">
              sub titles
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <q-media-player
      ref="player"
      type="audio"
      dense
      style="display: none"
    />
  </div>
</template>

<script lang="ts">
/* eslint-disable */
import Vue from 'vue'
import { Audio } from '@/api/Audio'

import axios, { AxiosResponse } from 'axios'
import { debounce } from 'quasar'
import { AudioInterface } from '@/api/interfaces/AudioInterface'

export default Vue.extend({
  data () {
    return {
      isPlaying: false,
      audioRecords: [] as AudioInterface[] | any[],
      searchAudioRecords: debounce((name = '', performer = '') => {
        new Audio()
          .search(name, performer)
          .then((audioRecords: AudioInterface[]) => {
            // @ts-ignore
            this.audioRecords = audioRecords.map(e => {
              e.playing = false
              return e
            })
          })
      }, 350)
    }
  },

  mounted () {
    /* eslint-disable */
    this.$root.$on('on-search', this.onSearch)
  },

  beforeCreate () {
    new Audio()
      .search()
      .then((audioRecords: AudioInterface[]) => {
        // @ts-ignore
        this.audioRecords = audioRecords.map(e => {
          e.playing = false
          return e
        })
      })
  },

  destroyed () {
    // @ts-ignore
    this.$root.$off('on-search', this.onSearch)
  },

  computed: {
    buttonsPlayer () {
      if (this.isPlaying) {
        return {
          opacity: 1
        }
      }
      return {}
    }
  },

  methods: {
    onPlay (audio: AudioInterface, isPlaying: boolean) {
      this.audioRecords.forEach((e: AudioInterface) => {
        e.playing = false
      })
      audio.playing = isPlaying

      // @ts-ignore
      if (this.$refs.player.$media.src !== audio.src) {
        // @ts-ignore
        this.$refs.player.$media.src = audio.src
      }

      if (isPlaying) {
        // @ts-ignore
        this.$refs.player.$media.play()
      } else {
        // @ts-ignore
        this.$refs.player.$media.pause()
      }
    },

    onDownload (item: AudioInterface) {
      axios.get(item.src, { responseType: 'blob' })
        .then((response: AxiosResponse) => {
          const blob = new Blob([response.data], { type: 'application/mp3' })
          const link = document.createElement('a')
          link.href = URL.createObjectURL(blob)
          // @ts-ignore
          link.download = item.src.split('/').pop()
          link.click()
          URL.revokeObjectURL(link.href)
        }).catch(console.error)
    },

    onSearch (q: string) {
      this.searchAudioRecords(q)
    }
  }
})
</script>

<style lang="scss" scoped>
.card {
  max-width: 400px;
}

.like {
}

.bg-buttons {
  padding: 100% 0 100% 0;
  opacity: 0;
  transition-delay: 2s;
  transition: opacity 0.5s;
}

.bg-buttons:hover {
  transition: opacity 0.5s;
  opacity: 1;
}

.color {
  &-play {
    background: #ffd53bcc !important;
  }
}
</style>
