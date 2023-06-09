import axios, { AxiosInstance } from 'axios';
import { useAccessTokenStore } from 'src/stores/access-token';
import { WAFListResult, WAFRule } from './api.models';

declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    $axios: AxiosInstance;
  }
}

const api = axios.create({
  baseURL: '/api/',
  responseType: 'json',
  headers: { 'Content-Type': 'application/json' },
  validateStatus: () => true,
});
api.interceptors.request.use((config) => {
  config.headers['Token'] = useAccessTokenStore().accessToken;
  return config;
});
api.interceptors.response.use(async (response) => {
  if (response.status === 403) {
    useAccessTokenStore().setAccessToken('');
  }
  return response;
});

export const waf = {
  async createRule(rule: WAFRule) {
    return await api.post('waf/createRule', rule).then((res) => res.data);
  },
  async listRules(): Promise<WAFListResult> {
    return await api.get('waf/listRules').then((res) => res.data);
  },
  async deleteRule(id: string) {
    return await api.post('waf/deleteRule', { id }).then((res) => res.data);
  },
};
