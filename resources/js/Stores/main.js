import { defineStore } from 'pinia'
import axios from 'axios'
import { Inertia } from '@inertiajs/inertia';

export const useMainStore = defineStore('main', {
  state: () => ({
    /* User */
    userName: null,
    userEmail: null,
    userAvatar: null,
    partialReload: '',
    /* Field focus with ctrl+k (to register only once) */
    isFieldFocusRegistered: false,

    /* Sample data (commonly used) */
    clients: [],
    history: []
  }),
  getters: {

  },
  actions: {
    setUser (payload) {
      if (payload.name) {
        this.userName = payload.name
      }
      if (payload.email) {
        this.userEmail = payload.email
      }
      if (payload.avatar) {
        this.userAvatar = payload.avatar
      }
    },
    reload(field) {
      Inertia.reload({ only: [field] })
      // Inertia.visit(route(field));
    },

    fetch (sampleDataKey) {
      axios
        .get(`data-sources/${sampleDataKey}.json`)
        .then(r => {
          if (r.data && r.data.data) {
            this[sampleDataKey] = r.data.data
          }
        })
        .catch(error => {
          alert(error.message)
        })
    }
  }
})
