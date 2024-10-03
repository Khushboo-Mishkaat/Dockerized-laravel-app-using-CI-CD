<script setup>
import { ref,computed } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Modal from '@/Components/Modal.vue';

const showModal = ref(false);
const title = ref('');
const description = ref('');
const titleError = ref('');
const descriptionError = ref('');
const showConfirmationDialog = ref(false);
const notificationSent = ref(false);
const props = defineProps({
  selectedUsers: {
    type: Array,
    required: true,
  },
  selectedGuestUsers: {
    type: Array,
    required: true,
  },
});
const openModal = () => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const resetForm = () => {
  title.value = '';
  description.value = '';
  titleError.value = '';
  descriptionError.value = '';
};

const validateForm = () => {
  titleError.value = title.value.length === 0 ? 'Title is required' : '';
  descriptionError.value = description.value.length === 0 ? 'Description is required' : '';

  return titleError.value === '' && descriptionError.value === '';
};

const sendNotification = () => {
  if (!validateForm()) {
    return;
  }

  showConfirmationDialog.value = true;
};
const getCookie = (name) => {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
};

const confirmSendNotification = async () => {
  showConfirmationDialog.value = false;
  let payload;
  if (props.selectedGuestUsers.length>0) {
    payload = {
      guest_user_ids: props.selectedGuestUsers,
      title: title.value,
      body: description.value,
    };
  } else {
    payload = {
      user_ids: props.selectedUsers,
      title: title.value,
      body: description.value,
    };
  }

  const csrfToken = getCookie('XSRF-TOKEN');

  if (!csrfToken) {
    console.error('CSRF token not found in cookies!');
    return;
  }

  try {
    const response = await fetch('/send-notifications', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
      },
      body: JSON.stringify(payload),
    });

    if (response.ok) {
      const result = await response.json();
      notificationSent.value = true;
      resetForm();
      showModal.value = false;
    } else {
      console.error('Error sending notification:', response);
    }
  } catch (error) {
    console.error('API call error:', error);
  }
};

const closeConfirmationDialog = () => {
  showConfirmationDialog.value = false;
};

const closeNotificationMessage = () => {
  notificationSent.value = false;
};

const userCount = computed(() => {
  return props.selectedGuestUsers.length > 0 ? props.selectedGuestUsers.length : props.selectedUsers.length;
});

const canSendNotification = computed(() => {
  return props.selectedUsers.length > 0 || props.selectedGuestUsers.length > 0;
});

</script>

<template>
  <div>
    <PrimaryButton
    @click="openModal"
      :disabled="!canSendNotification"
      :class="canSendNotification ? 'bg-blue-500 hover:bg-blue-600' : 'bg-gray-400 cursor-not-allowed'"
    >
      Send Notification
    </PrimaryButton>
    <Modal :show="showModal" @close="closeModal">
      <template #default>
        <div class="p-6">
          <h2 class="text-lg font-semibold mb-2">Send Notification</h2>
          <form @submit.prevent="sendNotification">
            <div class="mb-4">
              <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
              <input v-model="title" type="text" id="title" maxlength="100"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Enter notification title" />
              <p v-if="titleError" class="text-red-500 text-sm mt-1">{{ titleError }}</p>
            </div>

            <div class="mb-4">
              <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
              <textarea v-model="description" id="description" maxlength="500"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" rows="4"
                placeholder="Enter notification description"></textarea>
              <p v-if="descriptionError" class="text-red-500 text-sm mt-1">{{ descriptionError }}</p>
            </div>

            <PrimaryButton type="submit">Send Notification</PrimaryButton>
          </form>
        </div>
      </template>
    </Modal>

    <!-- Confirmation Dialog -->
    <Modal :show="showConfirmationDialog" @close="closeConfirmationDialog">
      <template #default>
        <div class="p-6">
          <h2 class="text-lg font-semibold mb-2">Confirm Notification</h2>
          <p>Are you sure you want to send this notification to {{ userCount }} users?</p>
          <div class="mt-4 flex justify-end space-x-2">
            <PrimaryButton @click="confirmSendNotification">Yes, Send</PrimaryButton>
            <button @click="closeConfirmationDialog" class="bg-gray-300 rounded-lg px-4 py-2">Cancel</button>
          </div>
        </div>
      </template>
    </Modal>

    <Modal :show="notificationSent" @close="closeNotificationMessage">
      <template #default>
        <div class="p-6">
          <h2 class="text-lg font-semibold mb-2">Success</h2>
          <p>Notification has been sent successfully to {{ userCount }} users.</p>
          <div class="mt-4 flex justify-end">
            <PrimaryButton @click="closeNotificationMessage">Close</PrimaryButton>
          </div>
        </div>
      </template>
    </Modal>
  </div>
</template>
