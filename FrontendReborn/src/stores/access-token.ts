import { defineStore } from 'pinia';
import { LocalStorage } from 'quasar';

export const useAccessTokenStore = defineStore('access-token', {
  state: () => ({
    accessToken: LocalStorage.getItem('access-token') || '',
  }),
  getters: {
    getAccessToken: (state) => state.accessToken,
  },
  actions: {
    setAccessToken(accessToken: string) {
      this.accessToken = accessToken;
      LocalStorage.set('access-token', accessToken);
    }
  }
})
