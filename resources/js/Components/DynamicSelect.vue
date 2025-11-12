<template>
  <select ref="select" class="form-select" :disabled="disabled">
    <option disabled value="">Selecione um concurso</option>
    <option
      v-for="item in options"
      :key="item.id"
      :value="item.id"
      :selected="item.id === modelValue"
    >
      {{ item.name }}
    </option>
  </select>
</template>

<script setup>
import { onMounted, watch, ref } from "vue";

const props = defineProps({
  modelValue: [String, Number],
  options: { type: Array, default: () => [] },
  disabled: Boolean,
});
const emit = defineEmits(["update:modelValue"]);
const select = ref(null);

onMounted(() => {
  if (window.$ && $.fn.select2) {
    $(select.value)
      .select2({
        width: "100%",
        placeholder: "Selecione um concurso",
      })
      .on("select2:select", (e) => {
        emit("update:modelValue", e.params.data.id);
      });

    // Sincronizar valor inicial
    $(select.value).val(props.modelValue).trigger("change");
  } else {
    console.error("Select2 ou jQuery não estão disponíveis.");
  }
});

watch(
  () => props.modelValue,
  (value) => {
    $(select.value).val(value).trigger("change");
  }
);
</script>

<style scoped>
.form-select {
  width: 100%;
}
</style>
