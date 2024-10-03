<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
const props = defineProps({
    workflows: Array,
});

// Create a mapping for user filter values
const userFilterMapping = {
    PreviouslySubscribed: 'Subscribed But Not Renewed Users Workflow',
    Subscribed: 'Subscribed Users Workflow',
    NoSubscription: 'Registered But Not Subscribed Users Workflow',
    Guest: 'Guest Users Workflow',
    All: 'All Users Workflow',
};
</script>

<template>
    <AdminLayout>
        <template #header></template>
        <h4 class="nk-block-title page-title mb-4">Workflows</h4>
        <div class="flex flex-wrap -mx-4">
            <div v-for="workflow in props.workflows" :key="workflow.id" class="w-full px-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between">
                        <div class="flex">
                            <div class="mr-4">
                                <img src="https://testorders.gethalalnow.com/assets/images/integration.png"
                                    :alt="`${workflow.name} Logo`" class="w-16 h-16 object-cover">
                            </div>
                            <div class="flex-1">
                                <h6 class="text-lg font-semibold">
                                    <span class="name">{{ userFilterMapping[workflow.user_filter] || workflow.user_filter }}</span>
                                </h6>
                                <div class="mt-2 text-gray-600">
                                    <span class="version">
                                        <span class="font-medium">Repetition: </span>{{ workflow.recurring_duration }}
                                    </span>
                                    <span class="ml-4 release">
                                        <span class="font-medium">Status: </span>
                                        <span :class="workflow.is_active ? 'bg-blue-500' : 'bg-red-500'"
                                            class="text-white py-1 px-2 rounded">
                                            {{ workflow.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a :href="`/workflow/settings/${workflow.id}`"
                                class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition duration-300">
                                <span class="nk-menu-text">Settings</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
