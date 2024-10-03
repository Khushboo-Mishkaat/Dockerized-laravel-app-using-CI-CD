<script setup>
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    record: Object, // The user record that needs to be updated
});

// Form handling
const form = useForm({
    user_filter: props.record?.user_filter || '',
    recurring_duration: props.record?.recurring_duration || '',
    is_active: props.record?.is_active || false,
    notification_title: props.record?.notification_title || '',
    notification_text: props.record?.notification_text || '',
    duration_minutes: null,
});

// Watch for changes in recurring_duration and set appropriate value for duration_minutes
watch(() => props.record.recurring_duration, (newDuration) => {
    form.duration_minutes = newDuration === 'Monthly' ? 30 : 1;
});

// Submit the updated data
const submit = () => {
    form.put(`/workflow/setting/${props.record.id}`, {
        onSuccess: () => {
            console.log('Settings updated successfully');
        },
        onError: (errors) => {
            console.error('Form submission error:', errors);
        },
    });
};

// Method to handle checkbox change for `is_active`
const handleCheckboxChange = (event) => {
    form.is_active = event.target.checked;
};
</script>

<template>
    <AdminLayout>
        <h3 class="nk-block-title page-title">Settings</h3>
        <div class="nk-block">
            <div class="card">
                <div class="card-inner">
                    <h5 class="card-title">Workflow Settings</h5>
                    <p>Configure your workflow settings here. You can update the data below:</p>
                    <form class="gy-3 form-settings" @submit.prevent="submit">
                        <!-- Toggle to Pause/Continue -->
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label class="form-label">Pause/Continue Feature</label>
                                    <span class="form-note">Enable or disable the notification feature.</span>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is-active"
                                            v-model="form.is_active" @change="handleCheckboxChange">
                                        <label class="custom-control-label" for="is-active">
                                            <span>Active/InActive</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Workflow Filter -->
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label class="form-label" for="integration_name">Workflow Filter</label>
                                    <span class="form-note">Specify the name of the integration.</span>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group">
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="integration_name"
                                            v-model="form.user_filter" placeholder="Name" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Title -->
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label class="form-label" for="notification-title">Notification Title</label>
                                    <span class="form-note">Specify the title/subject of the notification.</span>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group">
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="notification-title"
                                            v-model="form.notification_title" placeholder="Notification Title">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Text -->
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label class="form-label" for="notification-text">Notification Text</label>
                                    <span class="form-note">Specify the text/description of the notification.</span>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group">
                                    <div class="form-control-wrap">
                                        <textarea class="form-control" id="notification-text"
                                            v-model="form.notification_text" placeholder="Notification Text"
                                            rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Recurring Duration -->
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label class="form-label" for="recurring_duration">Recurring Duration</label>
                                    <span class="form-note">Select the duration for the recurrence.</span>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group">
                                    <div class="form-control-wrap">
                                        <select class="form-control" id="recurring_duration"
                                            v-model="form.recurring_duration">
                                            <option value="Daily">Daily</option>
                                            <option value="Weekly">Weekly</option>
                                            <option value="Monthly">Monthly</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row g-3 ">
                            <div class="col-lg-7 offset-lg-5">
                                <div class="form-group mt-2 flex justify-end">
                                    <button type="submit" class="btn btn-lg btn-primary" :disabled="form.processing">
                                        Save Settings
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
