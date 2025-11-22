<template>
  <div id="app">
    <div class="container">
      <header class="header">
        <h1 class="title">Vue.js + Symfony Integration</h1>
        <p class="subtitle">Test your API connection</p>
      </header>

      <div class="card">
        <button
          @click="fetchData"
          :disabled="loading"
          class="btn"
          aria-label="Fetch data from Symfony API"
        >
          <span v-if="!loading">Fetch Data from API</span>
          <span v-else class="loading-text">
            <span class="spinner"></span>
            Loading...
          </span>
        </button>

        <transition name="fade">
          <div v-if="error" class="alert alert-error" role="alert">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <circle cx="12" cy="12" r="10" stroke-width="2"/>
              <line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/>
              <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/>
            </svg>
            <div>
              <strong>Error:</strong> {{ error }}
            </div>
          </div>
        </transition>

        <transition name="fade">
          <div v-if="message && !error" class="response-container">
            <div class="response-header">
              <svg class="icon success-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <polyline points="20 6 9 17 4 12" stroke-width="2"/>
              </svg>
              <h2>API Response</h2>
            </div>
            <pre class="response-data">{{ message }}</pre>
          </div>
        </transition>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { apiClient } from './config/api';
import '@styles/main.css';

const message = ref(null);
const loading = ref(false);
const error = ref(null);

const fetchData = async () => {
  loading.value = true;
  error.value = null;
  message.value = null;

  try {
    const data = await apiClient.get('/test');
    message.value = JSON.stringify(data, null, 2);
  } catch (err) {
    console.error('Error fetching data:', err);
    error.value = err.message || 'Failed to fetch data from the API';
  } finally {
    loading.value = false;
  }
};
</script>