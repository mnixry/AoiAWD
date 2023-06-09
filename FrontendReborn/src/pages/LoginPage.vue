<template>
  <div class="fullscreen text-center q-pa-md flex flex-center">
    <div>
      <div class="text-h4 q-py-lg">Login To AoiAWD</div>
      <q-card bordered style="min-width: 300px">
        <q-card-section>
          <q-input
            v-model="accessToken"
            label="Access Token"
            rounded
            clearable
            type="password"
          />
        </q-card-section>
        <q-card-actions class="row justify-center">
          <q-btn label="Submit" icon="login" color="primary" @click="submit" />
        </q-card-actions>
      </q-card>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useAccessTokenStore } from 'src/stores/access-token';
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const accessToken = ref('');
const tokenStore = useAccessTokenStore(),
  $router = useRouter();

const submit = () => {
  tokenStore.setAccessToken(accessToken.value);
  $router.push('/');
};
</script>
