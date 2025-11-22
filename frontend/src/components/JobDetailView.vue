<template>
  <Transition name="modal">
    <div
      v-if="isOpen && job"
      class="fixed inset-0 z-50 overflow-y-auto"
      @click.self="close"
    >
      <div class="flex min-h-screen items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <!-- Modal -->
        <div class="relative bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-hidden">
          <!-- Header -->
          <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div class="flex-1">
              <h2 class="text-2xl font-bold text-gray-900">{{ job.title }}</h2>
              <p class="text-gray-600 mt-1">{{ job.company }}</p>
            </div>
            <button
              @click="close"
              class="text-gray-400 hover:text-gray-600 transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Content -->
          <div class="overflow-y-auto px-6 py-6" style="max-height: calc(90vh - 180px);">
            <!-- Job Info Grid -->
            <div class="grid grid-cols-2 gap-4 mb-6">
              <div>
                <p class="text-sm text-gray-500 mb-1">Location</p>
                <div class="flex items-center text-gray-900">
                  <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  {{ job.location }}
                </div>
              </div>
              <div>
                <p class="text-sm text-gray-500 mb-1">Salary Range</p>
                <div class="flex items-center text-gray-900 font-medium">
                  <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {{ formatSalary(job.salary) }}
                </div>
              </div>
              <div>
                <p class="text-sm text-gray-500 mb-1">Job Type</p>
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                  :class="getJobTypeBadgeClass(job.type)"
                >
                  {{ job.type }}
                </span>
              </div>
              <div>
                <p class="text-sm text-gray-500 mb-1">Work Mode</p>
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                  :class="getWorkModeBadgeClass(job.workMode)"
                >
                  {{ job.workMode }}
                </span>
              </div>
            </div>

            <!-- Sources and Duplicates -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
              <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-gray-700">Found on {{ job.sources.length }} platform{{ job.sources.length > 1 ? 's' : '' }}</p>
                <span v-if="job.isDuplicate" class="text-xs font-medium text-yellow-700 bg-yellow-100 px-2 py-1 rounded">
                  {{ job.duplicateCount }} duplicates
                </span>
              </div>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="source in job.sources"
                  :key="source"
                  class="inline-flex items-center px-3 py-1 rounded-md text-sm bg-white border border-gray-200 text-gray-700"
                >
                  {{ source }}
                </span>
              </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">Job Description</h3>
              <p class="text-gray-700 leading-relaxed">{{ job.description }}</p>
            </div>

            <!-- Requirements -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">Requirements</h3>
              <ul class="space-y-2">
                <li
                  v-for="(req, index) in job.requirements"
                  :key="index"
                  class="flex items-start"
                >
                  <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  <span class="text-gray-700">{{ req }}</span>
                </li>
              </ul>
            </div>

            <!-- Posted Date -->
            <div class="text-sm text-gray-500">
              Posted on {{ formatDate(job.postedDate) }}
            </div>
          </div>

          <!-- Footer Actions -->
          <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4">
            <div class="flex gap-3">
              <button
                @click="$emit('save', job.id)"
                :class="job.status === 'saved' ? 'bg-blue-50 text-blue-700 border-blue-300' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium border rounded-md transition-colors"
              >
                <svg class="w-5 h-5" :fill="job.status === 'saved' ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                {{ job.status === 'saved' ? 'Saved' : 'Save Job' }}
              </button>
              <button
                @click="$emit('apply', job.id)"
                :class="job.status === 'applied' ? 'bg-green-50 text-green-700 border-green-300' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium border rounded-md transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ job.status === 'applied' ? 'Already Applied' : 'Mark as Applied' }}
              </button>
              <a
                :href="`https://www.linkedin.com/jobs/search/?keywords=${encodeURIComponent(job.title)}`"
                target="_blank"
                rel="noopener noreferrer"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700 transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Apply Now
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import type { Job } from '@/types/job';

defineProps<{
  isOpen: boolean;
  job: Job | null;
}>();

const emit = defineEmits<{
  'close': [];
  'save': [id: string];
  'apply': [id: string];
}>();

const close = () => {
  emit('close');
};

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

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
};
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active > div > div {
  transition: transform 0.3s ease;
}

.modal-enter-from > div > div {
  transform: scale(0.95);
}
</style>
