import { AxiosResponse } from 'axios'
import { $axios } from '@/axios'
import { PerformerInterface } from '@/api/interfaces/PerformerInterface'

export class Performer {
  search (q = '', offset = 0, count = 100): Promise<PerformerInterface[]> {
    return new Promise<PerformerInterface[]>((resolve, reject) => {
      $axios.get('/performers/search', {
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

  getById (id: number): Promise<PerformerInterface> {
    return new Promise<PerformerInterface>((resolve, reject) => {
      $axios.get(`/performers/${id}`)
        .then((response: AxiosResponse) => {
          if ([200].includes(response.status)) {
            return resolve(response.data)
          }
          reject(response.data)
        }).catch(reject)
    })
  }

  upload (data: any): Promise<any> {
    return new Promise<any>((resolve, reject) => {
      $axios.post('/performers/upload', data)
        .then((response: AxiosResponse) => {
          if ([201].includes(response.status)) {
            return resolve(response.data)
          }
          reject(response.data)
        }).catch(reject)
    })
  }

  add (name: string): Promise<PerformerInterface> {
    return new Promise<PerformerInterface>((resolve, reject) => {
      $axios.post('/performers', {
        name
      })
        .then((response: AxiosResponse) => {
          if ([201, 200].includes(response.status)) {
            return resolve(response.data)
          }
          reject(response.data)
        }).catch(reject)
    })
  }

  save (id: string, data: any): Promise<any> {
    return new Promise<any>((resolve, reject) => {
      $axios.patch(`/performers/${id}`, data)
        .then((response: AxiosResponse) => {
          if ([204, 200].includes(response.status)) {
            return resolve(response.data)
          }
          reject(response.data)
        }).catch(reject)
    })
  }

  delete (id: number): Promise<any> {
    return new Promise<any>((resolve, reject) => {
      $axios.delete(`/performers/${id}`)
        .then((response: AxiosResponse) => {
          if ([204, 200].includes(response.status)) {
            return resolve(response.data)
          }
          reject(response.data)
        }).catch(reject)
    })
  }
}
