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
        <button @click="logout" class="bg-red-500 text-white px-4 py-2 rounded">تسجيل الخروج</button>
      </div>
      <UserList />
    </div>
  </div>
</template>

<script>
import { AuthService } from './services';
import UserList from './components/UserList.vue';
import LoginForm from './components/LoginForm.vue';
import RegisterForm from './components/RegisterForm.vue';

export default {
  components: {
    UserList,
    LoginForm,
    RegisterForm
  },
  data() {
    return {
      isLoggedIn: false,
      currentUser: null,
      showRegister: false
    };
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
