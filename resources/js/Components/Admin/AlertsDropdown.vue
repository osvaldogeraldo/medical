<template>
  <div class="dropdown">
    <a class="dropdown-toggle header-icon" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="ri-alarm-warning-line"></i>
      <span class="count-label success">{{ alerts.length }}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-300">
      <h5 class="fw-semibold px-3 py-2 text-primary">Alerts</h5>

      <!-- Scroll starts -->
      <div class="scroll300">
        <!-- Alert list starts -->
        <div class="p-3">
          <div v-for="alert in alerts" :key="alert.id" class="d-flex py-2">
            <div :class="`icon-box md ${alert.bgClass} rounded-circle me-3`">
              <span class="fw-bold fs-6 text-white">{{ alert.initials }}</span>
            </div>
            <div class="m-0">
              <h6 class="mb-1 fw-semibold">{{ alert.name }}</h6>
              <p class="mb-1">
                {{ alert.message }}
              </p>
              <p class="small m-0 opacity-50">{{ alert.time }}</p>
            </div>
          </div>
        </div>
        <!-- Alert list ends -->
      </div>
      <!-- Scroll ends -->

      <!-- View all button starts -->
      <div class="d-grid m-3">
        <Link href="/alerts" class="btn btn-primary">View all</Link>
      </div>
      <!-- View all button ends -->
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
  name: 'AlertsDropdown',
  components: {
    Link
  },
  data() {
    return {
      alerts: [
        {
          id: 1,
          name: 'Douglass Shaw',
          initials: 'DS',
          bgClass: 'bg-info',
          message: 'Appointed as a new President 2014-2025',
          time: 'Today, 07:30pm',
          route: 'staff.profile'
        },
        {
          id: 2,
          name: 'Willie Garrison',
          initials: 'WG',
          bgClass: 'bg-danger',
          message: 'Congratulate, James for new job.',
          time: 'Today, 08:00pm',
          route: 'staff.profile'
        },
        {
          id: 3,
          name: 'Terry Jenkins',
          initials: 'TJ',
          bgClass: 'bg-warning',
          message: 'Lewis added new doctors training schedule.',
          time: 'Today, 09:30pm',
          route: 'training'
        }
      ]
    }
  },
  methods: {
    handleAlertClick(alert) {
      if (alert.route) {
        this.$inertia.visit(route(alert.route))
      }
    },
    dismissAlert(alertId) {
      // Implement dismiss alert functionality
      this.alerts = this.alerts.filter(a => a.id !== alertId)
    }
  }
}
</script>
