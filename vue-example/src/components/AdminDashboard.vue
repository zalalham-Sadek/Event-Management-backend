<template>
  <div class="p-6 max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold">لوحة تحكم المدير</h2>
      <button 
        @click="showCreateUserModal = true"
        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
      >
        إضافة مستخدم جديد
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-blue-100 p-4 rounded-lg">
        <h3 class="text-lg font-semibold text-blue-800">إجمالي المستخدمين</h3>
        <p class="text-2xl font-bold text-blue-600">{{ users.length }}</p>
      </div>
      <div class="bg-green-100 p-4 rounded-lg">
        <h3 class="text-lg font-semibold text-green-800">المديرين</h3>
        <p class="text-2xl font-bold text-green-600">{{ adminCount }}</p>
      </div>
      <div class="bg-purple-100 p-4 rounded-lg">
        <h3 class="text-lg font-semibold text-purple-800">المستخدمين العاديين</h3>
        <p class="text-2xl font-bold text-purple-600">{{ userCount }}</p>
      </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold">قائمة المستخدمين</h3>
      </div>
      
      <div v-if="loading" class="p-6 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-2 text-gray-600">جاري التحميل...</p>
      </div>
      
      <div v-else-if="error" class="p-6 text-center">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          {{ error }}
        </div>
      </div>
      
      <div v-else>
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الدور</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الإنشاء</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ user.email }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  v-for="role in user.roles" 
                  :key="role"
                  :class="role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'"
                  class="inline-block px-2 py-1 text-xs font-medium rounded-full mr-1"
                >
                  {{ role === 'admin' ? 'مدير' : 'مستخدم' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(user.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button 
                  @click="editUser(user)"
                  class="text-indigo-600 hover:text-indigo-900 mr-3"
                >
                  تعديل
                </button>
                <button 
                  v-if="user.id !== currentUser.id"
                  @click="deleteUser(user.id)"
                  class="text-red-600 hover:text-red-900"
                >
                  حذف
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create User Modal -->
    <div v-if="showCreateUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">إضافة مستخدم جديد</h3>
          
          <form @submit.prevent="createUser">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">الاسم</label>
              <input 
                v-model="newUser.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
              <input 
                v-model="newUser.email"
                type="email"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور</label>
              <input 
                v-model="newUser.password"
                type="password"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">الدور</label>
              <select 
                v-model="newUser.role"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="user">مستخدم</option>
                <option value="admin">مدير</option>
              </select>
            </div>
            
            <div class="flex justify-end space-x-3 space-x-reverse">
              <button 
                type="button"
                @click="showCreateUserModal = false"
                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50"
              >
                إلغاء
              </button>
              <button 
                type="submit"
                :disabled="creating"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
              >
                {{ creating ? 'جاري الإنشاء...' : 'إنشاء' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { AdminService } from '../services';

export default {
  name: 'AdminDashboard',
  props: {
    currentUser: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      users: [],
      loading: true,
      error: null,
      showCreateUserModal: false,
      creating: false,
      newUser: {
        name: '',
        email: '',
        password: '',
        role: 'user'
      }
    };
  },
  computed: {
    adminCount() {
      return this.users.filter(user => user.roles && user.roles.includes('admin')).length;
    },
    userCount() {
      return this.users.filter(user => user.roles && user.roles.includes('user')).length;
    }
  },
  async mounted() {
    await this.loadUsers();
  },
  methods: {
    async loadUsers() {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await AdminService.getAllUsers();
        this.users = response.data.users;
      } catch (err) {
        console.error('Failed to load users:', err);
        this.error = err.response?.data?.error || 'فشل في تحميل المستخدمين';
      } finally {
        this.loading = false;
      }
    },
    
    async createUser() {
      this.creating = true;
      
      try {
        const response = await AdminService.createUser(this.newUser);
        this.users.push(response.data.user);
        this.showCreateUserModal = false;
        this.newUser = { name: '', email: '', password: '', role: 'user' };
        
        // Show success message
        this.$emit('show-message', 'تم إنشاء المستخدم بنجاح', 'success');
      } catch (err) {
        console.error('Failed to create user:', err);
        this.$emit('show-message', err.response?.data?.error || 'فشل في إنشاء المستخدم', 'error');
      } finally {
        this.creating = false;
      }
    },
    
    async deleteUser(userId) {
      if (!confirm('هل أنت متأكد من حذف هذا المستخدم؟')) {
        return;
      }
      
      try {
        await AdminService.deleteUser(userId);
        this.users = this.users.filter(user => user.id !== userId);
        this.$emit('show-message', 'تم حذف المستخدم بنجاح', 'success');
      } catch (err) {
        console.error('Failed to delete user:', err);
        this.$emit('show-message', err.response?.data?.error || 'فشل في حذف المستخدم', 'error');
      }
    },
    
    editUser(user) {
      // TODO: Implement edit user functionality
      console.log('Edit user:', user);
    },
    
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('ar-SA');
    }
  }
};
</script>
