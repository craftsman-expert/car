import { AxiosResponse } from 'axios'
import { $axios } from '@/axios'
import { AudioInterface } from './interfaces/AudioInterface'

export class Audio {
  search (q = '', offset = 0, count = 100): Promise<AudioInterface[]> {
    return new Promise<AudioInterface[]>((resolve, reject) => {
      $axios.get('/audio/search', {
        params: { q, offset, count }
      })
        .then((response: AxiosResponse) => {
          if ([200].includes(response.status)) {
            return resolve(response.data)
          }
          reject(response.data)
        }).catch(reject)
    })
  }

  getById (id: number): Promise<AudioInterface> {
    return new Promise<AudioInterface>((resolve, reject) => {
      $axios.get(`/audio/${id}`)
        .then((response: AxiosResponse) => {
          if ([200].includes(response.status)) {
            return resolve(response.data)
          }
          reject(response.data)
        }).catch(reject)
    })
  }

  save (id: string, data: any): Promise<any> {
    return new Promise<any>((resolve, reject) => {
      $axios.patch(`/audio/${id}`, data)
        .then((response: AxiosResponse) => {
          if ([204, 200].includes(response.status)) {
            return resolve(response.data)
          }
          reject(response.data)
        }).catch(reject)
    })
  }

  upload (data: any): Promise<any> {
    return new Promise<any>((resolve, reject) => {
      $axios.post('/audio/upload', data)
        .then((response: AxiosResponse) => {
          if ([201].includes(response.status)) {
            return resolve(response.data)
          }
          reject(response.data)
        }).catch(reject)
    })
  }

  delete (id: number): Promise<any> {
    return new Promise<any>((resolve, reject) => {
      $axios.delete(`/audio/${id}`)
        .then((response: AxiosResponse) => {
          if ([204, 200].includes(response.status)) {
            return resolve(response.data)
          }
          reject(response.data)
        }).catch(reject)
    })
  }

  like (id: number): Promise<any> {
    return new Promise<any>((resolve, reject) => {
      $axios.get(`/audio/like/${id}`)
        .then((response: AxiosResponse) => {
          if ([204, 200].includes(response.status)) {
            return resolve(response.data)
          }
          reject(response.data)
        }).catch(reject)
    })
  }

  notLike (id: number): Promise<any> {
    return new Promise<any>((resolve, reject) => {
      $axios.get(`/audio/not-like/${id}`)
        .then((response: AxiosResponse) => {
          if ([204, 200].includes(response.status)) {
            return resolve(response.data)
          }
          reject(response.data)
        }).catch(reject)
    })
  }
}
