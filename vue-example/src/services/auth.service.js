import api from './api';

const AuthService = {
  /**
   * Login user
   * @param {Object} credentials - User credentials
   * @param {string} credentials.email - User email
   * @param {string} credentials.password - User password
   * @returns {Promise} - Response from API
   */
  login(credentials) {
    return api.post('/api/login', credentials);
  },

  /**
   * Register new user
   * @param {Object} userData - User data
   * @param {string} userData.name - User name
   * @param {string} userData.email - User email
   * @param {string} userData.password - User password
   * @returns {Promise} - Response from API
   */
  register(userData) {
    return api.post('/api/register', userData);
  },

  /**
   * Get current user info
   * @returns {Promise} - Response from API
   */
  getCurrentUser() {
    return api.get('/api/me');
  },

  /**
   * Logout user
   * @returns {Promise} - Response from API
   */
  logout() {
    return api.post('/api/logout');
  }
};

export default AuthService;