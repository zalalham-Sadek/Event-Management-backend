import axios from 'axios';

// Create axios instance with base URL
const api = axios.create({
  baseURL: 'http://expand-php.test',
  headers: {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  },
  withCredentials: true, // Important for cookies/sessions
});

// Add request interceptor for debugging
api.interceptors.request.use(config => {
  console.log('API Request:', config);
  return config;
}, error => {
  console.error('API Request Error:', error);
  return Promise.reject(error);
});

// Add response interceptor for debugging
api.interceptors.response.use(response => {
  console.log('API Response:', response);
  return response;
}, error => {
  console.error('API Response Error:', error);
  return Promise.reject(error);
});

export default api;