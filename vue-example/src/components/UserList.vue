<template>
  <div class="p-6 max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold">قائمة المستخدمين</h2>
      <span class="text-sm text-gray-600">إجمالي المستخدمين: {{ users.length }}</span>
    </div>
    
    <div v-if="loading" class="text-center py-4">
      جاري التحميل...
    </div>
    
    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      {{ error }}
    </div>
    
    <div v-else>
      <table class="min-w-full bg-white border border-gray-200">
        <thead>
          <tr>
            <th class="py-2 px-4 border-b">الاسم</th>
            <th class="py-2 px-4 border-b">البريد الإلكتروني</th>
            <th class="py-2 px-4 border-b">الأدوار</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id" class="border-b hover:bg-gray-50">
            <td class="py-2 px-4">{{ user.name }}</td>
            <td class="py-2 px-4">{{ user.email }}</td>
            <td class="py-2 px-4">
              <span v-for="role in user.roles" :key="role" class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded mr-1">
                {{ role }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { UserService } from '../services';

export default {
  data() {
    return {
      users: [],
      loading: true,
      error: null
    };
  },
  async mounted() {
    try {
      console.log('Fetching users from API...');
      const response = await UserService.getAllUsers();
      console.log('API response:', response);
      this.users = response.data.users; // Access the users array from the response
      this.loading = false;
    } catch (err) {
      console.error('API Error:', err);
      console.log('Error details:', {
        message: err.message,
        response: err.response,
        request: err.request
      });
      this.error = err.response?.data?.error || 'حدث خطأ أثناء تحميل بيانات المستخدمين';
      this.loading = false;
    }
  }
};
</script>