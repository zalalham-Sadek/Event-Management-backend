import api from './api';

const UserService = {
  /**
   * Get all users
   * @returns {Promise} - Response from API
   */
  getAllUsers() {
    return api.get('/api/users');
  },

  /**
   * Get user by ID
   * @param {number} id - User ID
   * @returns {Promise} - Response from API
   */
  getUser(id) {
    return api.get(`/api/users/${id}`);
  },

  /**
   * Create new user
   * @param {Object} userData - User data
   * @returns {Promise} - Response from API
   */
  createUser(userData) {
    return api.post('/api/users', userData);
  },

  /**
   * Update user
   * @param {number} id - User ID
   * @param {Object} userData - User data to update
   * @returns {Promise} - Response from API
   */
  updateUser(id, userData) {
    return api.put(`/api/users/${id}`, userData);
  },

  /**
   * Delete user
   * @param {number} id - User ID
   * @returns {Promise} - Response from API
   */
  deleteUser(id) {
    return api.delete(`/api/users/${id}`);
  }
};

export default UserService;