<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Job Search Dashboard</h1>
        <p class="text-gray-600 mb-4">Aggregated listings from multiple sources with AI-powered filtering</p>

        <!-- Stats -->
        <div class="flex gap-4">
          <div class="inline-flex items-center px-3 py-1.5 rounded-md bg-blue-100 text-blue-800 text-sm font-medium">
            {{ filteredJobs.length }} jobs found
          </div>
          <div class="inline-flex items-center px-3 py-1.5 rounded-md bg-yellow-100 text-yellow-800 text-sm font-medium">
            {{ duplicateCount }} duplicates detected
          </div>
        </div>
      </div>

      <!-- Search Bar -->
      <SearchBar v-model="filters" />

      <!-- AI Filter Panel -->
      <AIFilterPanel v-model="aiFilter" />

      <!-- Results Header -->
      <div class="flex items-center justify-between mb-4">
        <div class="text-sm text-gray-600">
          Showing {{ displayedJobs.length }} of {{ filteredJobs.length }} jobs
        </div>
        <div class="flex gap-2">
          <button
            @click="viewMode = 'grid'"
            :class="viewMode === 'grid' ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
            class="p-2 rounded-md border border-gray-300 transition-colors"
            title="Grid View"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
          </button>
          <button
            @click="viewMode = 'list'"
            :class="viewMode === 'list' ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
            class="p-2 rounded-md border border-gray-300 transition-colors"
            title="List View"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Job Cards Grid/List -->
      <div
        :class="viewMode === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6' : 'space-y-4'"
        class="mb-8"
      >
        <JobCard
          v-for="job in displayedJobs"
          :key="job.id"
          :job="job"
          @view-details="openJobDetails"
          @save="toggleJobStatus($event, 'saved')"
          @apply="toggleJobStatus($event, 'applied')"
          @hide="toggleJobStatus($event, 'hidden')"
        />
      </div>

      <!-- Empty State -->
      <div v-if="filteredJobs.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No jobs found</h3>
        <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or search query.</p>
      </div>

      <!-- Job Detail Modal -->
      <JobDetailView
        :is-open="isDetailModalOpen"
        :job="selectedJob"
        @close="closeJobDetails"
        @save="toggleJobStatus($event, 'saved')"
        @apply="toggleJobStatus($event, 'applied')"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { mockJobs } from '@/data/mockJobs';
import type { Job, SearchFilters, AIFilter, JobStatus } from '@/types/job';
import JobCard from './JobCard.vue';
import SearchBar from './SearchBar.vue';
import AIFilterPanel from './AIFilterPanel.vue';
import JobDetailView from './JobDetailView.vue';

// State
const jobs = ref<Job[]>(mockJobs);
const filters = ref<SearchFilters>({
  query: '',
  location: '',
  types: [],
  modes: [],
  salaryRange: { min: 0, max: 200000 },
  sources: []
});
const aiFilter = ref<AIFilter>({
  query: '',
  isActive: false
});
const viewMode = ref<'grid' | 'list'>('grid');
const isDetailModalOpen = ref(false);
const selectedJob = ref<Job | null>(null);

// Computed
const filteredJobs = computed(() => {
  let result = [...jobs.value];

  // Apply search query
  if (filters.value.query) {
    const query = filters.value.query.toLowerCase();
    result = result.filter(job =>
      job.title.toLowerCase().includes(query) ||
      job.company.toLowerCase().includes(query) ||
      job.description.toLowerCase().includes(query)
    );
  }

  // Apply location filter
  if (filters.value.location) {
    const location = filters.value.location.toLowerCase();
    result = result.filter(job =>
      job.location.toLowerCase().includes(location)
    );
  }

  // Apply job type filter
  if (filters.value.types.length > 0) {
    result = result.filter(job =>
      filters.value.types.includes(job.type)
    );
  }

  // Apply work mode filter
  if (filters.value.modes.length > 0) {
    result = result.filter(job =>
      filters.value.modes.includes(job.workMode)
    );
  }

  // Apply salary range filter
  result = result.filter(job =>
    job.salary.min >= filters.value.salaryRange.min &&
    job.salary.max <= filters.value.salaryRange.max
  );

  // Apply sources filter
  if (filters.value.sources.length > 0) {
    result = result.filter(job =>
      job.sources.some(source => filters.value.sources.includes(source))
    );
  }

  // Apply AI filter (simplified - in real app, this would use actual AI)
  if (aiFilter.value.isActive) {
    const aiQuery = aiFilter.value.query.toLowerCase();
    result = result.filter(job => {
      // Simple keyword matching for demo
      const jobText = `${job.title} ${job.description} ${job.requirements.join(' ')}`.toLowerCase();
      return jobText.includes(aiQuery) ||
             aiQuery.split(' ').some(word => jobText.includes(word));
    });
  }

  // Filter out hidden jobs
  result = result.filter(job => job.status !== 'hidden');

  return result;
});

const displayedJobs = computed(() => {
  return filteredJobs.value;
});

const duplicateCount = computed(() => {
  return jobs.value.filter(job => job.isDuplicate).length;
});

// Methods
const openJobDetails = (job: Job) => {
  selectedJob.value = job;
  isDetailModalOpen.value = true;
};

const closeJobDetails = () => {
  isDetailModalOpen.value = false;
  selectedJob.value = null;
};

const toggleJobStatus = (jobId: string, status: JobStatus) => {
  const job = jobs.value.find(j => j.id === jobId);
  if (job) {
    job.status = job.status === status ? null : status;
  }
};
</script>
