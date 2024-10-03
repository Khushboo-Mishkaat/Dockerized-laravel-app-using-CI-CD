<script setup>
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import "../../css/dashlite.css"
import AdminLayout from '@/Layouts/AdminLayout.vue';
const props = defineProps(['totalUsers', 'newRegistrations', 'totalSubscribedUsers']);
</script>

<template>

  <Head title="Dashboard" />

  <AdminLayout>
    <template #header>
    </template>

    <div>
      <div >
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 h-[600px]  flex-wrap">
            <h1 class="nk-block-title page-title">Analytics</h1>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4 ">
              <div class="col-lg-12 col-sm-6">
                <div class="card h-100 bg-warning">
                  <div class="nk-cmwg nk-cmwg1">
                    <div class="card-inner pt-3">
                      <div class="d-flex justify-content-between">
                        <div class="flex-item">
                          <div class="text-white d-flex flex-wrap">
                            <span class="fs-2 me-1">{{ totalUsers }}</span>
                            <span class="align-self-end fs-14px pb-1">
                            </span>
                          </div>
                          <h6 class="text-white">Total User</h6>
                        </div>

                      </div>
                    </div><!-- .card-inner -->
                    <div class="nk-cmwg1-ck mt-auto">
                      <canvas class="campaign-line-chart-s1 rounded-bottom" id="totalAudience"></canvas>
                    </div>
                  </div><!-- .nk-cmwg -->
                </div><!-- .card -->
              </div>
              <div class="col-lg-12 col-sm-6">
                <div class="card h-100 bg-info">
                  <div class="nk-cmwg nk-cmwg1">
                    <div class="card-inner pt-3">
                      <div class="d-flex justify-content-between">
                        <div class="flex-item">
                          <div class="text-white d-flex flex-wrap">
                            <span class="fs-2 me-1">{{ totalSubscribedUsers }}</span>
                            <span class="align-self-end fs-14px pb-1">
                            </span>
                          </div>
                          <h6 class="text-white">Total Subcribed User</h6>
                        </div>

                      </div>
                    </div><!-- .card-inner -->
                    <div class="nk-cmwg1-ck mt-auto">
                      <canvas class="campaign-line-chart-s1 rounded-bottom" id="totalAudience"></canvas>
                    </div>
                  </div><!-- .nk-cmwg -->
                </div><!-- .card -->
              </div>
              <div class="col-lg-12 col-sm-6">
                <div class="card h-100 bg-primary">
                  <div class="nk-cmwg nk-cmwg1">
                    <div class="card-inner pt-3">
                      <div class="d-flex justify-content-between">
                        <div class="flex-item">
                          <div class="text-white d-flex flex-wrap">
                            <span class="fs-2 me-1">{{ newRegistrations }}</span>
                            <span class="align-self-end fs-14px pb-1">
                            </span>
                          </div>
                          <h6 class="text-white">New Registrations (Last 30 Days)</h6>
                        </div>

                      </div>
                    </div><!-- .card-inner -->
                    <div class="nk-cmwg1-ck mt-auto">
                      <canvas class="campaign-line-chart-s1 rounded-bottom" id="totalAudience"></canvas>
                    </div>
                  </div><!-- .nk-cmwg -->
                </div><!-- .card -->
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>
