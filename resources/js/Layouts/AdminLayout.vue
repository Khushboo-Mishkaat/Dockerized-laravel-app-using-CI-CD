<script setup>
import { ref, watch, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import DropdownLink from '@/Components/DropdownLink.vue';
import '../../css/dashlite.css'

const activeItem = ref('');

const page = usePage();

const sidebarItems = [
  { path: "/users", label: "Manage Users", icon: '/userSidebar.svg' },
  { path: "/workflow", label: "Workflows", icon: '/workflow.svg' },
];

const normalizeUrl = (url) => url.split('?')[0].replace(/\/$/, '');

watch(() => page.url, (newUrl) => {
  activeItem.value = normalizeUrl(newUrl);
});

onMounted(() => {
  activeItem.value = normalizeUrl(page.url);
});
const toggleDarkMode = () => {
  const r = "dark-mode-bookmarklet";
  const d = document.getElementById(r);
  if (d) {
    return d.parentNode.removeChild(d);
  }
  const t = document.createElement("style");
  t.id = r;
  t.innerHTML = "html, img, video, canvas, svg { background: #fff; filter: invert(0.9); }";
  document.body.appendChild(t);
};
</script>

<template>
  <div class="nk-app-root">
    <div class="nk-main">
      <div class="nk-header-menu bg-white is-light font-semibold text-[#34201C]">
        <div class="container-xl wide-xl">
          <div class="nk-header-wrap">
            <span class="text-2xl sm:ml-10 rounded-lg max-[640px]:text-xl max-[485px]:text-sm max-[640px]:ml-5"></span>
            <div class="nk-header-tools">
              <ul class="nk-quick-nav">
                <li>
                  <DropdownLink :href="route('logout')" method="post" as="button"
                    class=" bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Log Out
                  </DropdownLink>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="nk-sidebar nk-sidebar-fixed is-light border-r-[1px]">
        <div class="nk-sidebar-element nk-sidebar-head mt-3">
          <div class="sm:flex">
            <Link href="/" class="logo-link ml-4">
            <img src="/logo.png" alt="Logo" class="logo-dark w-20" />
            </Link>
            <button class="menu-toggle-icon d-xl-none max-[640px]:text-center">
              <em class="icon ni ni-menu ml-5"></em>
            </button>
          </div>
        </div>
        <div class="nk-sidebar-element nk-sidebar-body">
          <div class="nk-sidebar-menu mt-4" data-simplebar>
            <div class="nk-sidebar-content">
              <ul class="nk-menu rounded">
                <li v-for="(item, index) in sidebarItems" :key="index" class="nk-menu-item">
                  <Link :href="item.path" class="nk-menu-link">
                    <img :src="item.icon" class="w-5 h-5" />
                    <span class="nk-menu-text ml-3 w-[100%]">{{ item.label }}</span>
                  </Link>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="nk-wrap">
        <div class="nk-content">
          <div class="container-fluid">
            <div class="nk-content-inner">
              <div class="nk-content-body">
                <slot />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<style scoped>
.nk-menu-link-active {
  background-color: #f0f0f0 !important;
  color: #007bff !important;
  font-weight: bold !important;
}
</style>
