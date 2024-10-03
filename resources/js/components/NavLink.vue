<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'inline-flex items-center px-3 py-2 bg-indigo-100 border-b-2 border-indigo-500 text-sm font-semibold text-indigo-700 focus:outline-none focus:border-indigo-700 transition duration-200 ease-in-out'
        : 'inline-flex items-center px-3 py-2 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:bg-gray-100 focus:text-gray-700 transition duration-200 ease-in-out'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
