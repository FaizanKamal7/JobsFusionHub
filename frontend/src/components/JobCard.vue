<template>
  <div
    class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow cursor-pointer"
    @click="$emit('view-details', job)"
  >
    <!-- Header -->
    <div class="flex justify-between items-start mb-3">
      <div class="flex-1">
        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ job.title }}</h3>
        <p class="text-gray-600">{{ job.company }}</p>
      </div>
      <div v-if="job.isDuplicate" class="ml-2">
        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
          {{ job.duplicateCount }} duplicates
        </span>
      </div>
    </div>

    <!-- Badges -->
    <div class="flex flex-wrap gap-2 mb-4">
      <span
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
        :class="getJobTypeBadgeClass(job.type)"
      >
        {{ job.type }}
      </span>
      <span
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
        :class="getWorkModeBadgeClass(job.workMode)"
      >
        {{ job.workMode }}
      </span>
    </div>

    <!-- Location and Salary -->
    <div class="space-y-2 mb-4">
      <div class="flex items-center text-sm text-gray-600">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        {{ job.location }}
      </div>
      <div class="flex items-center text-sm text-gray-600">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ formatSalary(job.salary) }}
      </div>
    </div>

    <!-- Sources -->
    <div class="flex items-center mb-4">
      <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <div class="flex flex-wrap gap-1">
        <span
          v-for="source in job.sources"
          :key="source"
          class="text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded"
        >
          {{ source }}
        </span>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-2 pt-4 border-t border-gray-100">
      <button
        @click.stop="$emit('save', job.id)"
        :class="job.status === 'saved' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50'"
        class="flex-1 flex items-center justify-center gap-1 px-3 py-2 text-sm font-medium border border-gray-300 rounded-md transition-colors"
      >
        <svg class="w-4 h-4" :fill="job.status === 'saved' ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
        </svg>
        Save
      </button>
      <button
        @click.stop="$emit('apply', job.id)"
        :class="job.status === 'applied' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50'"
        class="flex-1 flex items-center justify-center gap-1 px-3 py-2 text-sm font-medium border border-gray-300 rounded-md transition-colors"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Applied
      </button>
      <button
        @click.stop="$emit('hide', job.id)"
        :class="job.status === 'hidden' ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-gray-50'"
        class="flex items-center justify-center gap-1 px-3 py-2 text-sm font-medium border border-gray-300 rounded-md transition-colors"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Job } from '@/types/job';

defineProps<{
  job: Job;
}>();

defineEmits<{
  'view-details': [job: Job];
  'save': [id: string];
  'apply': [id: string];
  'hide': [id: string];
}>();

const getJobTypeBadgeClass = (type: string) => {
  const classes = {
    'full-time': 'bg-blue-100 text-blue-800',
    'part-time': 'bg-purple-100 text-purple-800',
    'contract': 'bg-orange-100 text-orange-800',
    'internship': 'bg-pink-100 text-pink-800'
  };
  return classes[type as keyof typeof classes] || 'bg-gray-100 text-gray-800';
};

const getWorkModeBadgeClass = (mode: string) => {
  const classes = {
    'remote': 'bg-green-100 text-green-800',
    'hybrid': 'bg-yellow-100 text-yellow-800',
    'onsite': 'bg-red-100 text-red-800'
  };
  return classes[mode as keyof typeof classes] || 'bg-gray-100 text-gray-800';
};

const formatSalary = (salary: { min: number; max: number; currency: string }) => {
  const formatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: salary.currency,
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  });
  return `${formatter.format(salary.min)} - ${formatter.format(salary.max)}`;
};
</script>
