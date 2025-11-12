<template>
  <div class="file-upload-wrapper">
    <label class="file-label">
      <input
        type="file"
        class="file-input"
        @change="handleFileChange"
        :accept="accept"
      />
      <div class="upload-box">
        <div class="upload-content">
          <iconify-icon icon="mdi:upload" class="upload-icon" />
          <span class="upload-text">
            {{ fileName || "Carregue um ficheiro" }}
          </span>
        </div>
      </div>
    </label>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  modelValue: File,
  accept: {
    type: String,
    default: ".pdf,.jpg,.jpeg,.png",
  },
});

const emit = defineEmits(["update:modelValue"]);
const fileName = ref("");

function handleFileChange(event) {
  const file = event.target.files[0];
  if (file) {
    emit("update:modelValue", file);
    fileName.value = file.name;
  }
}

watch(
  () => props.modelValue,
  (file) => {
    fileName.value = file?.name || "";
  },
  { immediate: true }
);
</script>

<style scoped>
.file-label {
  display: block;
  cursor: pointer;
  width: 100%;
}

.file-input {
  display: none;
}

.upload-box {
  border: 2px dashed #cbd5e0;
  padding: 1.5rem;
  border-radius: 8px;
  transition: all 0.3s ease;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.upload-box:hover {
  border-color: #4a90e2;
}

.upload-content {
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.upload-icon {
  font-size: 32px;
  color: #4a90e2;
  margin-bottom: 0.5rem;
}

.upload-text {
  font-size: 14px;
  font-weight: 500;
}
</style>
