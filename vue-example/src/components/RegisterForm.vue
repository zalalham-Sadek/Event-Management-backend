<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          إنشاء حساب جديد
        </h2>
      </div>
      
      <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          {{ error }}
        </div>
        
        <div v-if="success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
          {{ success }}
        </div>
        
        <div class="rounded-md shadow-sm -space-y-px">
          <div>
            <label for="name" class="sr-only">الاسم</label>
            <input
              id="name"
              v-model="name"
              name="name"
              type="text"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="الاسم"
            />
          </div>
          <div>
            <label for="email" class="sr-only">البريد الإلكتروني</label>
            <input
              id="email"
              v-model="email"
              name="email"
              type="email"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="البريد الإلكتروني"
            />
          </div>
          <div>
            <label for="password" class="sr-only">كلمة المرور</label>
            <input
              id="password"
              v-model="password"
              name="password"
              type="password"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="كلمة المرور"
            />
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            <span v-if="loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            {{ loading ? 'جاري إنشاء الحساب...' : 'إنشاء حساب' }}
          </button>
        </div>
        
        <div class="text-center">
          <button
            type="button"
            @click="$emit('switch-to-login')"
            class="text-indigo-600 hover:text-indigo-500"
          >
            لديك حساب بالفعل؟ تسجيل الدخول
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { AuthService } from '../services';

export default {
  name: 'RegisterForm',
  data() {
    return {
      name: '',
      email: '',
      password: '',
      loading: false,
      error: '',
      success: ''
    };
  },
  methods: {
    async handleRegister() {
      this.loading = true;
      this.error = '';
      this.success = '';
      
             try {
         console.log('Attempting registration with:', { name: this.name, email: this.email, password: '***' });
         
         // Validate form data
         if (!this.name || !this.email || !this.password) {
           this.error = 'جميع الحقول مطلوبة';
           return;
         }
         
                   if (this.password.length < 6) {
            this.error = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
            return;
          }
          
          // Basic email validation
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(this.email)) {
            this.error = 'البريد الإلكتروني غير صحيح';
            return;
          }
         
         const response = await AuthService.register({
           name: this.name,
           email: this.email,
           password: this.password
         });
         
         console.log('Registration response:', response);
        
        const data = response.data;
        
        if (data.message === 'Registration successful') {
          this.success = 'تم إنشاء الحساب بنجاح! جاري تسجيل الدخول تلقائياً...';
          
          // Store user data and auto-login
          localStorage.setItem('user', JSON.stringify(data.user));
          
          // Emit registration success event to parent component
          this.$emit('registration-success', data.user);
          
          // Clear form
          this.name = '';
          this.email = '';
          this.password = '';
        } else {
          this.error = data.error || 'حدث خطأ أثناء إنشاء الحساب';
        }
      } catch (err) {
        console.error('Registration error:', err);
        this.error = err.response?.data?.error || 'خطأ في الاتصال بالسيرفر';
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>
