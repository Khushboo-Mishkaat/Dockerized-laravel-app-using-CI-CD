<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { defineProps, ref, watch } from 'vue';
import Notification from '@/Components/Notification.vue';

const props = defineProps({
  users: Object,
});

const users = ref(props.users);
const currentFilter = ref('all');
const searchQuery = ref('');
const selectedUsers = ref([]);
const selectedGuestUsers = ref([]);
const loading = ref(false);

const truncateText = (text, maxLength = 20) => {
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
};

function fetchUsers(page = 1) {
  loading.value = true; 
  const url = new URL(`/users`, window.location.origin);
  url.searchParams.set('filter', currentFilter.value);
  url.searchParams.set('page', page);

  if (searchQuery.value) {
    url.searchParams.set('search', searchQuery.value);
  }

  fetch(url.toString(), {
    headers: {
      Accept: 'application/json',
    },
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      users.value = data;
      loading.value = false;
    })
    .catch((error) => {
      console.error('There was a problem with the fetch operation:', error);
      loading.value = false; 
    });
}

function handlePageChange(pageUrl) {
  if (pageUrl) {
    const url = new URL(pageUrl, window.location.origin);
    const page = url.searchParams.get('page');
    fetchUsers(page);
  }
}

function handleSearch() {
  fetchUsers();
}

function toggleUserSelection(userId) {
  if (selectedUsers.value.includes(userId)) {
    selectedUsers.value = selectedUsers.value.filter(id => id !== userId);
  } else {
    selectedUsers.value.push(userId);
  }
}

function isSelected(userId) {
  return selectedUsers.value.includes(userId);
}

function selectAllUsers() {
  if (selectedUsers.value.length === users.value.data.length) {
    selectedUsers.value = [];
  } else {
    selectedUsers.value = users.value.data.map(user => user.cognitoId);
  }
}

function toggleGuestUserSelection(userId) {
  if (selectedGuestUsers.value.includes(userId)) {
    selectedGuestUsers.value = selectedGuestUsers.value.filter(id => id !== userId);
  } else {
    selectedGuestUsers.value.push(userId);
  }
}

function isSelectedGuestUser(userId) {
  return selectedGuestUsers.value.includes(userId);
}

function selectAllGuestUser() {
  if (selectedGuestUsers.value.length === users.value.guest_user.length) {
    selectedGuestUsers.value = [];
  } else {
    selectedGuestUsers.value = users.value.guest_user.map(user => user.deviceId);
  }
}

watch(currentFilter, () => {
  fetchUsers();
});

</script>

<template>
  <AdminLayout>
    <template #header></template>
    <div>
      <div class="rounded-lg">
        <!-- User Head -->
        <div class="flex items-center justify-between p-2 bg-white  rounded-lg">
          <h1 class="text-2xl font-bold mb-1 ml-7">Users list</h1>
          <div class="mb-1">
            <select v-model="currentFilter" class="rounded-md text-sm bg-gray-800 border text-white border-blue-500">
              <option value="all">All Users</option>
              <option value="guest">Guest Users</option>
              <option value="registered-no-subscription">Registered But Not Subscribed</option>
              <option value="subscribed">Subscribed</option>
              <option value="previously-subscribed">Subscribed But Not Renewed</option>
              <option value="inactive">Inactive Users</option>
            </select>
          </div>
        </div>


        <!-- User Card -->
        <div class="p-6 text-gray-900 flex-wrap w-full h-[180px] ">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 mb-4 ">
            <div class="bg-white shadow-md rounded-lg p-4 border border-gray-300">
              <h2 class="text-lg font-semibold mb-2">Total Users</h2>
              <p class="text-2xl font-bold">{{ users.totalUsers }}</p>
            </div>
            <div class="bg-white shadow-md rounded-lg p-4 border border-gray-300">
              <h2 class="text-lg font-semibold mb-2">Subscribed Users</h2>
              <p class="text-2xl font-bold">{{ users.totalSubscribedUsers }}</p>
            </div>
            <div class="bg-white shadow-md rounded-lg p-4 border border-gray-300">
              <h2 class="text-lg font-semibold mb-2">New Registrations (Last 30 Days)</h2>
              <p class="text-2xl font-bold">{{ users.newRegistrations }}</p>
            </div>
          </div>
        </div>

        <!-- User Table -->
        <div class="bg-white rounded-lg">
          <div class="flex items-center justify-between">
            <div class="mt-3 ml-3">
              <input v-model="searchQuery" @input="handleSearch" type="text" placeholder="Search by username or email"
                class="border rounded-lg text-sm w-[400px] mb-4" />
            </div>
            <Notification :selectedUsers="selectedUsers" :selectedGuestUsers="selectedGuestUsers" />
          </div>
          <div v-if="users.data.length === 0" class="text-red-500">
            No users are present.
          </div>
          <div v-if="loading" class="flex justify-center items-center mt-10">
            <div class="loader"></div>
          </div>
          <table v-if="users.data.length > 0 && !loading" class="min-w-full bg-white border-gray-300 mb-4">
            <thead class="bg-gray-100">
              <tr v-if="currentFilter === 'guest'">
                <th class="py-2 px-4 border-b text-left text-sm font-medium text-gray-700">
                  <input type="checkbox" @change="selectAllGuestUser"
                    :checked="selectedGuestUsers.length === (users.guest_user ? users.guest_user.length : 0)" />
                </th>
                <th class="py-2 px-4 border-b text-left text-sm font-medium text-gray-700">Device ID</th>
                <th class="py-2 px-4 border-b text-left text-sm font-medium text-gray-700">FCM Token</th>
                <th class="py-2 px-4 border-b text-left text-sm font-medium text-gray-700">Created</th>
              </tr>
              <tr v-else>
                <th class="py-2 px-4 border-b text-left text-sm font-medium text-gray-700">
                  <input type="checkbox" @change="selectAllUsers"
                    :checked="selectedUsers.length === users.data.length" />
                </th>
                <th class="py-2 px-4 border-b text-left text-sm font-medium text-gray-700">Username</th>
                <th class="py-2 px-4 border-b text-left text-sm font-medium text-gray-700">Email</th>
                <th class="py-2 px-4 border-b text-left text-sm font-medium text-gray-700">Subscription Status</th>
                <th class="py-2 px-4 border-b text-left text-sm font-medium text-gray-700">Last Subscription Date</th>
                <th class="py-2 px-4 border-b text-left text-sm font-medium text-gray-700">Number of Subscriptions</th>
              </tr>
            </thead>
            <div v-if="currentFilter === 'guest' && users.guest_user.length === 0" class="text-red-500">
              There are no guest users.
            </div>
            <tbody>
              <tr v-for="(user, index) in users.guest_user" :key="'guest-' + index" v-if="currentFilter === 'guest'">
                <td class="py-2 px-4 border-b text-left">
                  <input type="checkbox" @change="toggleGuestUserSelection(user.deviceId)"
                    :checked="isSelectedGuestUser(user.deviceId)" />
                </td>
                <td class="py-2 px-4 border-b text-gray-800">{{ truncateText(user.deviceId) || 'N/A' }}</td>
                <td class="py-2 px-4 border-b text-gray-800">{{ truncateText(user.fcm_token) || 'N/A' }}</td>
                <td class="py-2 px-4 border-b text-gray-800">{{ user.created || 'N/A' }}</td>
              </tr>
              <tr v-for="(user, index) in users.data" :key="index" v-else>
                <td class="py-2 px-4 border-b text-left">
                  <input type="checkbox" @change="toggleUserSelection(user.cognitoId)"
                    :checked="isSelected(user.cognitoId)" />
                </td>
                <td class="py-2 px-4 border-b text-gray-800">{{ user.userName || 'N/A' }}</td>
                <td class="py-2 px-4 border-b text-gray-800">{{ user.email || 'N/A' }}</td>
                <td class="py-2 px-4 border-b text-gray-800">{{ user.subscriptionStatus || 'N/A' }}</td>
                <td class="py-2 px-4 border-b text-gray-800">{{ user.lastSubscriptionDate || 'N/A' }}</td>
                <td class="py-2 px-4 border-b text-gray-800">{{ user.numberOfSubscriptions || 'N/A' }}</td>
              </tr>
            </tbody>
          </table>
         
          </div>
                  <!-- Pagination Controls -->
        <div v-if="users.total > users.per_page && currentFilter !== 'guest' && !loading" class="flex justify-between items-center">
          <button @click="handlePageChange(users.prev_page_url)" :disabled="!users.prev_page_url"
            class="bg-gray-200 p-2 rounded-md text-sm text-gray-700">
            Previous
          </button>
          <span class="text-sm text-gray-700">
            Page {{ users.current_page }} of {{ users.last_page }}
          </span>
          <button @click="handlePageChange(users.next_page_url)" :disabled="!users.next_page_url"
            class="bg-gray-200 p-2 rounded-md text-sm text-gray-700">
            Next
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
