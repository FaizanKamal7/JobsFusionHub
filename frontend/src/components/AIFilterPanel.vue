<template>
  <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg border border-purple-200 p-6 mb-6">
    <div class="flex items-center gap-2 mb-3">
      <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
      </svg>
      <h3 class="text-lg font-semibold text-gray-900">AI-Powered Filter</h3>
    </div>

    <p class="text-sm text-gray-600 mb-4">
      Describe what you're looking for in natural language
    </p>

    <div class="relative mb-4">
      <input
        v-model="aiQuery"
        type="text"
        placeholder="e.g., entry-level frontend role under $100k"
        class="block w-full px-4 py-3 pr-12 border border-purple-300 rounded-md leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
        @keyup.enter="applyAIFilter"
      />
      <button
        @click="applyAIFilter"
        :disabled="!aiQuery.trim() || isProcessing"
        class="absolute inset-y-0 right-0 px-4 flex items-center justify-center bg-purple-600 text-white rounded-r-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
      >
        <svg v-if="!isProcessing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        <svg v-else class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </button>
    </div>

    <!-- Example Queries -->
    <div class="mb-4">
      <p class="text-xs text-gray-500 mb-2">Try these examples:</p>
      <div class="flex flex-wrap gap-2">
        <button
          v-for="example in exampleQueries"
          :key="example"
          @click="useExample(example)"
          class="text-xs px-3 py-1.5 bg-white border border-purple-200 text-purple-700 rounded-full hover:bg-purple-50 hover:border-purple-300 transition-colors"
        >
          {{ example }}
        </button>
      </div>
    </div>

    <!-- Active AI Filter Display -->
    <div v-if="modelValue.isActive" class="bg-white rounded-md p-3 border border-purple-200">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <div class="flex items-center gap-2 mb-1">
            <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="text-sm font-medium text-gray-900">Active AI Filter</span>
          </div>
          <p class="text-sm text-gray-600">{{ modelValue.query }}</p>
        </div>
        <button
          @click="clearAIFilter"
          class="ml-2 text-gray-400 hover:text-gray-600"
        >
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import type { AIFilter } from '@/types/job';

const props = defineProps<{
  modelValue: AIFilter;
}>();

const emit = defineEmits<{
  'update:modelValue': [filter: AIFilter];
}>();

const aiQuery = ref('');
const isProcessing = ref(false);

const exampleQueries = [
  'Entry-level frontend roles under $100k',
  'Remote senior positions with React',
  'Hybrid tech hubs jobs',
  'Contract work above $120k'
];

const applyAIFilter = async () => {
  if (!aiQuery.value.trim()) return;

  isProcessing.value = true;

  // Simulate AI processing
  await new Promise(resolve => setTimeout(resolve, 1000));

  emit('update:modelValue', {
    query: aiQuery.value,
    isActive: true
  });

  isProcessing.value = false;
  aiQuery.value = '';
};

const useExample = (example: string) => {
  aiQuery.value = example;
  applyAIFilter();
};

const clearAIFilter = () => {
  emit('update:modelValue', {
    query: '',
    isActive: false
  });
};
</script>
