<template>
  <q-page class="row justify-evenly items-center">
    <q-card class="col-12 col-md-8">
      <q-card-section class="row items-center">
        <q-btn round icon="arrow_back" class="q-mx-md" @click="$router.back" />
        <div class="text-h6">Add Custom WAF Rule</div>
      </q-card-section>
      <q-separator />
      <q-form @submit.prevent="submitRule">
        <q-card-section>
          <q-input label="Rule Name" clearable v-model="ruleName" />
          <q-select
            label="Rule Type"
            v-model="ruleType"
            :options="['regex', 'function']"
          />
          <div v-if="ruleType === 'regex'">
            <q-select
              label="Regex Applied Field"
              v-model="ruleField"
              :options="[
                'script',
                'method',
                'uri',
                'remote',
                'header',
                'get',
                'post',
                'cookie',
                'file',
                'buffer',
              ]"
              clearable
            />
            <q-input
              label="Regular Expression"
              v-model="ruleRegex"
              clearable
              placeholder="/regex/"
            />
          </div>
          <div v-else-if="ruleType === 'function'">
            <q-input
              label="Evaluate Function Code"
              class="q-py-md"
              style="font-family: monospace"
              clearable
              v-model="ruleCode"
              type="textarea"
              autogrow
              filled
            />
          </div>
        </q-card-section>
        <q-card-actions>
          <q-btn label="Submit" icon="check" type="submit" color="accent" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-page>
</template>
<script setup lang="ts">
import { ref } from 'vue';
import { waf } from 'boot/axios';
import { useRouter } from 'vue-router';

const ruleName = ref<string>(''),
  ruleType = ref<string>('regex'),
  ruleField = ref<string>('uri'),
  ruleRegex = ref<string>(''),
  ruleCode = ref<string>('');

const $router = useRouter();

const submitRule = async () => {
  const name = ruleName.value.trim() || undefined;
  const response = await waf.createRule(
    ruleType.value === 'regex'
      ? {
          name,
          expression: {
            type: ruleType.value as 'regex',
            field: ruleField.value.trim(),
            regex: ruleRegex.value.trim(),
          },
        }
      : {
          name,
          expression: {
            type: ruleType.value as 'function',
            code: ruleCode.value.trim(),
          },
        }
  );
};
</script>
