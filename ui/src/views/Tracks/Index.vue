<template>
  <div>
    <q-toolbar>
      <q-space />
      <q-btn
        flat
        round
        icon="mdi-folder-plus-outline"
        @click="onAdd"
      />
      <q-btn
        flat
        round
        icon="mdi-refresh"
        @click="onRefresh"
      />
    </q-toolbar>
    <q-list separator>
      <q-item
        v-for="(item, index) in audioRecords"
        :key="index"
        clickable
      >
        <q-item-section avatar>
          <q-btn
            round
            :icon="item.playing ? 'mdi-pause' : 'mdi-play'"
            class="card-btn color-play"
            @click="onPlay(item, isPlaying = !isPlaying)"
          />
        </q-item-section>
        <q-item-section>
          <q-item-label>
            {{ item.name }}
          </q-item-label>
          <q-item-label caption>
            {{ item.performer.name }}
          </q-item-label>
        </q-item-section>
        <q-item-section avatar>
          <q-btn
            flat
            round
            :icon="item.like ? 'mdi-heart' : 'mdi-heart-outline'"
            :color="item.like ? 'red' : 'grey'"
            @click="onClickHeart(item)"
          />
        </q-item-section>
        <q-item-section avatar>
          <q-btn
            flat
            round
            icon="mdi-dots-horizontal"
          >
            <q-menu
              transition-show="flip-right"
              transition-hide="flip-left"
              auto-close
            >
              <q-list style="min-width: 100px">
                <q-item
                  clickable
                  @click="onDownloadClick(item)"
                >
                  <q-item-section avatar>
                    <q-icon name="mdi-download" />
                  </q-item-section>
                  <q-item-section>Скачать</q-item-section>
                </q-item>
                <q-item
                  clickable
                  :to="{ path: `/tracks/edit/${item.id}` }"
                >
                  <q-item-section avatar>
                    <q-icon name="mdi-file-document-edit-outline" />
                  </q-item-section>
                  <q-item-section>Редактировать</q-item-section>
                </q-item>
                <q-item
                  clickable
                  disable
                >
                  <q-item-section avatar>
                    <q-icon name="mdi-share-variant" />
                  </q-item-section>
                  <q-item-section>Поделиться</q-item-section>
                </q-item>
                <q-item
                  clickable
                  @click="onClickHeart(item)"
                >
                  <q-item-section avatar>
                    <q-icon :name="item.like ? 'mdi-heart' : 'mdi-heart-outline'" />
                  </q-item-section>
                  <q-item-section v-if="item.like">Не нарвится</q-item-section>
                  <q-item-section v-else>Нравится</q-item-section>
                </q-item>
                <q-item
                  clickable
                  @click="onDeleteClick(item.id)"
                >
                  <q-item-section avatar>
                    <q-icon name="mdi-delete"/>
                  </q-item-section>
                  <q-item-section>Удалить из фонотеки</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
        </q-item-section>
      </q-item>
    </q-list>

    <q-media-player
      ref="player"
      type="audio"
      dense
      style="display: none"
    />
    <q-file
      ref="filesVoice"
      v-model="filesVoice"
      style="display: none"
      multiple
      :max-files="10"
    />
  </div>
</template>

<script lang="ts">
import Vue from 'vue'
import { Audio } from '@/api/Audio'
import axios, { AxiosResponse } from 'axios'
import { debounce } from 'quasar'
import { AudioInterface } from '@/api/interfaces/AudioInterface'

export default Vue.extend({

  data () {
    return {
      filesVoice: [],
      isPlaying: false,
      audioRecords: [] as AudioInterface[],
      searchAudioRecords: debounce((q = '') => {
        new Audio()
          .search(q)
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

  watch: {
    filesVoice (items?: File[]) {
      if (!Array.isArray(items)) {
        return null
      }

      if (items.length === 0) {
        return null
      }
      const formData: FormData = new FormData()
      items.forEach((f: File) => {
        formData.append(f.name, f)
      })

      new Audio()
        .upload(formData)
        .then(() => {
          this.$notify.success('Файлы успешно загружены!')
          this.filesVoice = []
          this.onRefresh()
        })
    }
  },

  mounted () {
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

      // eslint-disable-next-line
      if (this.$refs.player.$media.src !== audio.src) {
        // eslint-disable-next-line
        this.$refs.player.$media.src = audio.src
      }

      if (isPlaying) {
        this.$refs.player.$media.play()
      } else {
        this.$refs.player.$media.pause()
      }
    },

    onDownloadClick (item: AudioInterface) {
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
    },

    onRefresh () {
      new Audio()
        .search()
        .then((audioRecords: AudioInterface[]) => {
          this.audioRecords = audioRecords.map(e => {
            e.playing = false
            return e
          })
        })
    },

    onDeleteClick (id: number) {
      new Audio()
        .delete(id)
        .then(() => {
          const index: number = this.audioRecords.findIndex((e: AudioInterface) => e.id === id)
          if (index > -1) {
            this.audioRecords.splice(index, 1)
          }
        })
    },

    onClickHeart (item: AudioInterface) {
      if (item.like) {
        item.like = false
        new Audio().notLike(item.id)
      } else {
        item.like = true
        new Audio().like(item.id)
      }
    },

    onAdd () {
      // eslint-disable-next-line
      this.$refs.filesVoice.pickFiles()
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
