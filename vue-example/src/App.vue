<template>
  <div>
    <!-- Show login form if not logged in -->
    <LoginForm 
      v-if="!isLoggedIn && !showRegister" 
      @login-success="handleLoginSuccess"
      @switch-to-register="showRegister = true"
    />
    
    <!-- Show register form if not logged in and showRegister is true -->
    <RegisterForm 
      v-if="!isLoggedIn && showRegister" 
      @switch-to-login="showRegister = false"
      @registration-success="handleRegistrationSuccess"
    />
    
    <!-- Show main app if logged in -->
    <div v-else-if="isLoggedIn">
      <div class="p-4 bg-gray-100 flex justify-between items-center">
        <h1 class="text-xl font-bold">مرحباً {{ currentUser.name }}</h1>
        <div class="flex items-center space-x-4 space-x-reverse">
          <button 
            v-if="isAdmin"
            @click="showAdminDashboard = !showAdminDashboard"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
          >
            {{ showAdminDashboard ? 'عرض المستخدمين' : 'لوحة التحكم' }}
          </button>
          <button @click="logout" class="bg-red-500 text-white px-4 py-2 rounded">تسجيل الخروج</button>
        </div>
      </div>
      
      <!-- Admin Dashboard -->
      <AdminDashboard 
        v-if="isAdmin && showAdminDashboard"
        :current-user="currentUser"
        @show-message="showMessage"
      />
      
      <!-- User List (for regular users or when admin dashboard is not shown) -->
      <UserList v-else />
    </div>
  </div>
</template>

<script>
import { AuthService } from './services';
import UserList from './components/UserList.vue';
import LoginForm from './components/LoginForm.vue';
import RegisterForm from './components/RegisterForm.vue';
import AdminDashboard from './components/AdminDashboard.vue';

export default {
  components: {
    UserList,
    LoginForm,
    RegisterForm,
    AdminDashboard
  },
  data() {
    return {
      isLoggedIn: false,
      currentUser: null,
      showRegister: false,
      showAdminDashboard: false
    };
  },
  computed: {
    isAdmin() {
      return this.currentUser && this.currentUser.roles && this.currentUser.roles.includes('admin');
    }
  },
  mounted() {
    // Check if user is already logged in (from localStorage)
    const savedUser = localStorage.getItem('user');
    if (savedUser) {
      try {
        this.currentUser = JSON.parse(savedUser);
        this.isLoggedIn = true;
      } catch (err) {
        console.error('Error parsing saved user:', err);
        localStorage.removeItem('user');
      }
    }
  },
  methods: {
    handleLoginSuccess(user) {
      this.currentUser = user;
      this.isLoggedIn = true;
      this.showRegister = false;
      console.log('Login successful, user:', user);
    },
    
    handleRegistrationSuccess(user) {
      this.currentUser = user;
      this.isLoggedIn = true;
      this.showRegister = false;
      console.log('Registration successful, user:', user);
    },
    
    showMessage(message, type = 'info') {
      // Simple alert for now, you can implement a proper notification system
      alert(`${type === 'success' ? '✅' : '❌'} ${message}`);
    },
    
    async logout() {
      try {
        await AuthService.logout();
        this.isLoggedIn = false;
        this.currentUser = null;
        this.showRegister = false;
        localStorage.removeItem('user');
      } catch (err) {
        console.error('Logout error:', err);
        // Even if logout fails, clear local state
        this.isLoggedIn = false;
        this.currentUser = null;
        this.showRegister = false;
        localStorage.removeItem('user');
      }
    }
  }
};
</script>
