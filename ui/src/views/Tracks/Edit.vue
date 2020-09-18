<template>
  <div class="q-gutter-y-lg">
    <div class="row q-col-gutter-md">
      <div class="col-grow q-pa-none">
        <q-input
          v-model="audioRecord.name"
          label="Название трека"
        />
      </div>
    </div>
    <div class="row q-col-gutter-md">
      <div class="col-grow q-pa-none">
        <q-select
          v-model="audioRecord.performer"
          use-input
          hide-selected
          fill-input
          input-debounce="350"
          label="Исполнитель"
          :options="performers"
          @filter="filterFn"
          @filter-abort="abortFilterFn"
          option-label="name"
        >
          <template v-slot:no-option>
            <q-item
              clickable
              @click="onNewPerformerClick"
            >
              <q-item-section class="text-grey">
                Не нашли исполнителя? Нажмите чтобы добавить.
              </q-item-section>
            </q-item>
          </template>
        </q-select>
      </div>
    </div>
    <div class="row q-col-gutter-md">
      <div class="col-grow q-pa-none text-right">
        <q-btn
          flat
          label="Сохранить"
          @click="onSave"
        />
      </div>
    </div>

    <q-dialog
      v-model="performerDialog.showing"
      persistent>
      <q-card style="min-width: 350px">
        <q-card-section>
          <div class="text-h6">Имя исполнителя</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-input
            dense
            v-model="performerDialog.name"
            autofocus
          />
        </q-card-section>

        <q-card-actions align="right" class="text-primary">
          <q-btn
            flat
            label="Отмена"
            v-close-popup
          />
          <q-btn
            flat
            label="Добавить"
            v-close-popup
            @click="onAddPerformerClick(performerDialog.name)"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-media-player
      ref="player"
      type="audio"
      dense
      style="display: none"
    />
  </div>
</template>

<script lang="ts">
import Vue from 'vue'
import { Audio } from '@/api/Audio'
import { Performer } from '@/api/Performer'
import axios, { AxiosResponse } from 'axios'
import { PerformerInterface } from '@/api/interfaces/PerformerInterface'
import { AudioInterface } from '@/api/interfaces/AudioInterface'

export default Vue.extend({

  data () {
    return {
      performerDialog: {
        showing: false,
        name: ''
      },
      isPlaying: false,
      performers: [] as PerformerInterface[] | any[],
      audioRecord: {
        id: 0, like: false, name: '', performer: {
          id: 0,
          name: ''
        } as PerformerInterface,
        playing: false, src: ''
      } as AudioInterface
    }
  },

  mounted () {
    this.$root.$on('on-search', this.onSearch)
  },

  beforeCreate () {
    new Audio()
      .getById(parseInt(this.$route.params.id))
      .then((audioRecord: AudioInterface) => {
        // @ts-ignore
        this.audioRecord = audioRecord
        // @ts-ignore
        this.audioRecord.playing = false
      })
  },

  destroyed () {
    this.$root.$off('on-search', this.onSearch)
  },

  computed: {
    drawerRightIsVisible () {
      return !/^\/dashboard/.test(this.$route.path)
    },

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
      // @ts-ignore
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
      // @ts-ignore
      this.searchAudioRecords(q)
    },

    // @ts-ignore
    filterFn (val, update, abort) {
      // call abort() at any time if you can't retrieve data somehow

      new Performer()
        .search(val, 0, 25)
        .then((performers: PerformerInterface[]) => {
          this.performers = performers
        }).catch(abort)
        .finally(update)
    },

    abortFilterFn () {
      // console.log('delayed filter aborted')
    },

    onNewPerformerClick () {
      this.performerDialog.showing = true
    },

    onAddPerformerClick (name: string) {
      new Performer()
        .add(name)
        .then((performer: PerformerInterface) => {
          this.audioRecord.performer = performer
        })
      this.performerDialog.showing = false
    },

    onSave () {
      new Audio()
        // @ts-ignore
        .save(this.audioRecord.id, {
          name: this.audioRecord.name,
          performer_id: this.audioRecord.performer.id
        }).then(() => {
          this.$router.back()
        })
      this.performerDialog.showing = false
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
