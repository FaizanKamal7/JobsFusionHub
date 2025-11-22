<template>
  <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
    <!-- Search Inputs -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <input
          v-model="localFilters.query"
          type="text"
          placeholder="Search job titles, companies, or keywords..."
          class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
          @input="debouncedUpdate"
        />
      </div>
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </div>
        <input
          v-model="localFilters.location"
          type="text"
          placeholder="Location"
          class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
          @input="debouncedUpdate"
        />
      </div>
    </div>

    <!-- Filters Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
      <!-- Job Type Filter -->
      <div class="relative">
        <button
          @click="toggleDropdown('types')"
          class="w-full px-4 py-2.5 text-left bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 flex items-center justify-between"
        >
          <span>{{ getJobTypeLabel() }}</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div
          v-if="openDropdown === 'types'"
          class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg"
        >
          <div class="py-1">
            <label
              v-for="type in jobTypes"
              :key="type"
              class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer"
            >
              <input
                type="checkbox"
                :value="type"
                v-model="localFilters.types"
                @change="updateFilters"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
              />
              <span class="ml-3 text-sm text-gray-700 capitalize">{{ type }}</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Work Mode Filter -->
      <div class="relative">
        <button
          @click="toggleDropdown('modes')"
          class="w-full px-4 py-2.5 text-left bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 flex items-center justify-between"
        >
          <span>{{ getWorkModeLabel() }}</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div
          v-if="openDropdown === 'modes'"
          class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg"
        >
          <div class="py-1">
            <label
              v-for="mode in workModes"
              :key="mode"
              class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer"
            >
              <input
                type="checkbox"
                :value="mode"
                v-model="localFilters.modes"
                @change="updateFilters"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
              />
              <span class="ml-3 text-sm text-gray-700 capitalize">{{ mode }}</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Salary Range -->
      <div class="relative">
        <button
          @click="toggleDropdown('salary')"
          class="w-full px-4 py-2.5 text-left bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 flex items-center justify-between"
        >
          <span>Salary Range</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div
          v-if="openDropdown === 'salary'"
          class="absolute z-10 mt-1 w-64 bg-white border border-gray-200 rounded-md shadow-lg p-4"
        >
          <div class="space-y-3">
            <div>
              <label class="block text-xs text-gray-600 mb-1">Min ($)</label>
              <input
                type="number"
                v-model.number="localFilters.salaryRange.min"
                @change="updateFilters"
                placeholder="0"
                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
              />
            </div>
            <div>
              <label class="block text-xs text-gray-600 mb-1">Max ($)</label>
              <input
                type="number"
                v-model.number="localFilters.salaryRange.max"
                @change="updateFilters"
                placeholder="200000"
                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Sources Filter -->
      <div class="relative">
        <button
          @click="toggleDropdown('sources')"
          class="w-full px-4 py-2.5 text-left bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 flex items-center justify-between"
        >
          <span>{{ getSourcesLabel() }}</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div
          v-if="openDropdown === 'sources'"
          class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg"
        >
          <div class="py-1">
            <label
              v-for="source in allSources"
              :key="source"
              class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer"
            >
              <input
                type="checkbox"
                :value="source"
                v-model="localFilters.sources"
                @change="updateFilters"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
              />
              <span class="ml-3 text-sm text-gray-700">{{ source }}</span>
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- Active Filters -->
    <div v-if="hasActiveFilters" class="mt-4 flex items-center gap-2">
      <span class="text-sm text-gray-600">Active filters:</span>
      <div class="flex flex-wrap gap-2">
        <span
          v-for="(filter, index) in activeFiltersList"
          :key="index"
          class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-primary-100 text-primary-800"
        >
          {{ filter }}
          <button @click="removeFilter(filter)" class="ml-1 hover:text-primary-900">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
        </span>
        <button
          @click="clearFilters"
          class="text-xs text-primary-600 hover:text-primary-800 font-medium"
        >
          Clear all
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import type { SearchFilters, JobType, WorkMode, JobSource } from '@/types/job';

const props = defineProps<{
  modelValue: SearchFilters;
}>();

const emit = defineEmits<{
  'update:modelValue': [filters: SearchFilters];
}>();

const localFilters = ref<SearchFilters>({ ...props.modelValue });
const openDropdown = ref<string | null>(null);
let debounceTimer: number | undefined;

// Close dropdown when clicking outside
const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as HTMLElement;
  if (!target.closest('.relative')) {
    openDropdown.value = null;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

const jobTypes: JobType[] = ['full-time', 'part-time', 'contract', 'internship'];
const workModes: WorkMode[] = ['remote', 'hybrid', 'onsite'];
const allSources: JobSource[] = ['LinkedIn', 'Indeed', 'Glassdoor', 'Monster', 'ZipRecruiter'];

watch(() => props.modelValue, (newVal) => {
  localFilters.value = { ...newVal };
}, { deep: true });

const toggleDropdown = (dropdown: string) => {
  openDropdown.value = openDropdown.value === dropdown ? null : dropdown;
};

const updateFilters = () => {
  emit('update:modelValue', { ...localFilters.value });
  openDropdown.value = null;
};

const debouncedUpdate = () => {
  if (debounceTimer) clearTimeout(debounceTimer);
  debounceTimer = window.setTimeout(() => {
    updateFilters();
  }, 300);
};

const getJobTypeLabel = () => {
  if (localFilters.value.types.length === 0) return 'All Types';
  if (localFilters.value.types.length === 1) return localFilters.value.types[0];
  return `${localFilters.value.types.length} types`;
};

const getWorkModeLabel = () => {
  if (localFilters.value.modes.length === 0) return 'All Modes';
  if (localFilters.value.modes.length === 1) return localFilters.value.modes[0];
  return `${localFilters.value.modes.length} modes`;
};

const getSourcesLabel = () => {
  if (localFilters.value.sources.length === 0) return 'Sources';
  if (localFilters.value.sources.length === 1) return localFilters.value.sources[0];
  return `${localFilters.value.sources.length} sources`;
};

const hasActiveFilters = computed(() => {
  return (
    localFilters.value.types.length > 0 ||
    localFilters.value.modes.length > 0 ||
    localFilters.value.sources.length > 0 ||
    localFilters.value.salaryRange.min > 0 ||
    localFilters.value.salaryRange.max < 200000
  );
});

const activeFiltersList = computed(() => {
  const filters: string[] = [];
  localFilters.value.types.forEach(type => filters.push(type));
  localFilters.value.modes.forEach(mode => filters.push(mode));
  localFilters.value.sources.forEach(source => filters.push(source));
  if (localFilters.value.salaryRange.min > 0 || localFilters.value.salaryRange.max < 200000) {
    filters.push(`$${localFilters.value.salaryRange.min}k-$${localFilters.value.salaryRange.max}k`);
  }
  return filters;
});

const removeFilter = (filter: string) => {
  if (jobTypes.includes(filter as JobType)) {
    localFilters.value.types = localFilters.value.types.filter(t => t !== filter);
  } else if (workModes.includes(filter as WorkMode)) {
    localFilters.value.modes = localFilters.value.modes.filter(m => m !== filter);
  } else if (allSources.includes(filter as JobSource)) {
    localFilters.value.sources = localFilters.value.sources.filter(s => s !== filter);
  } else if (filter.startsWith('$')) {
    localFilters.value.salaryRange = { min: 0, max: 200000 };
  }
  updateFilters();
};

const clearFilters = () => {
  localFilters.value = {
    query: '',
    location: '',
    types: [],
    modes: [],
    salaryRange: { min: 0, max: 200000 },
    sources: []
  };
  updateFilters();
};
</script>
