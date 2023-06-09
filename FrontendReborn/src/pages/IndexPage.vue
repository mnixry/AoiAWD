<template>
  <q-page>
    <div class="q-pa-md q-gutter-md">
      <q-btn label="Add Rule" icon="add" color="accent" to="/waf/add" />
      <q-btn
        label="Refresh"
        icon="refresh"
        color="secondary"
        @click="loadRules"
      />
      <q-btn
        label="Delete"
        icon="delete"
        color="negative"
        @click="deleteRules"
      />
    </div>
    <div class="q-ma-md">
      <q-table
        bordered
        title="WAF Rules"
        :columns="[
          {
            name: 'id',
            label: 'Rule ID',
            field: (v) => v._id.$oid,
            align: 'left',
          },
          {
            name: 'name',
            label: 'Rule Name',
            field: (v) => v.name ?? '[unnamed]',
            align: 'left',
          },
          {
            name: 'type',
            label: 'Rule Type',
            field: (v) => v.expression.type,
          },
          {
            name: 'value',
            label: 'Rule Value',
            field: (v) =>
              v.expression.type === 'regex'
                ? `${v.expression.field} => ${v.expression.regex}`
                : v.expression.code,
          },
        ]"
        :rows="rules"
        :rows-per-page-options="[25, 50, 0]"
        :row-key="(v) => v._id.$oid"
        selection="multiple"
        v-model:selected="selectedRules"
      />
    </div>
  </q-page>
</template>

<script setup lang="ts">
import { WAFListResult } from 'src/boot/api.models';
import { waf } from 'boot/axios';
import { onMounted, ref } from 'vue';
import { useQuasar } from 'quasar';

const rules = ref<WAFListResult['data']>([]),
  selectedRules = ref<WAFListResult['data']>([]);
const $q = useQuasar();

const loadRules = async () => {
  const { status, data } = await waf.listRules();
  if (status) rules.value = data;
};

const deleteRules = async () => {
  $q.loading.show();
  try {
    await Promise.all(
      selectedRules.value.map((rule) => waf.deleteRule(rule._id.$oid))
    );
    await loadRules();
  } catch (e) {
    $q.notify({
      type: 'negative',
      message: 'Failed to delete rules',
      caption: String(e),
    });
  } finally {
    $q.loading.hide();
  }
};

onMounted(loadRules);
</script>
