<template>
  <div class="dropdown">
    <a class="dropdown-toggle header-icon" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="ri-list-check-3"></i>
      <span class="count-label warning">{{ notifications.length }}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-300">
      <h5 class="fw-semibold px-3 py-2 text-primary">Activity</h5>

      <!-- Scroll starts -->
      <div class="scroll300">
        <!-- Activity List Starts -->
        <div class="p-3">
          <ul class="p-0 activity-list2">
            <li v-for="notification in notifications" :key="notification.id" class="activity-item pb-3 mb-3">
              <a href="#" @click.prevent="handleNotificationClick(notification)">
                <h5 class="fw-regular">
                  <i :class="`ri-circle-fill ${notification.typeClass} me-1`"></i>
                  {{ notification.title }}
                </h5>
                <div class="ps-3 ms-2 border-start">
                  <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0">
                      <img :src="notification.image" class="img-shadow img-3x rounded-1" alt="Notification">
                    </div>
                    <div class="flex-grow-1 ms-3">
                      {{ notification.description }}
                    </div>
                  </div>
                  <p class="m-0 small">{{ notification.time }}</p>
                </div>
              </a>
            </li>
          </ul>
        </div>
        <!-- Activity List Ends -->
      </div>
      <!-- Scroll ends -->

      <!-- View all button starts -->
      <div class="d-grid m-3">
        <Link href="/activities" class="btn btn-primary">View all</Link>
      </div>
      <!-- View all button ends -->
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
  name: 'NotificationsDropdown',
  components: {
    Link
  },
  data() {
    return {
      notifications: [
        {
          id: 1,
          title: 'Invoices',
          typeClass: 'text-danger',
          image: '/assets/images/products/1.jpg',
          description: '23 invoices have been paid to the MediCare Labs.',
          time: '10:20AM Today',
          route: 'invoices'
        },
        {
          id: 2,
          title: 'Purchased',
          typeClass: 'text-info',
          image: '/assets/images/products/2.jpg',
          description: '28 new surgical equipments have been purchased.',
          time: '04:30PM Today',
          route: 'purchases'
        },
        {
          id: 3,
          title: 'Appointed',
          typeClass: 'text-success',
          image: '/assets/images/products/8.jpg',
          description: '36 new doctors and 28 staff members appointed.',
          time: '06:50PM Today',
          route: 'staff'
        },
        {
          id: 4,
          title: 'Requested',
          typeClass: 'text-warning',
          image: '/assets/images/products/9.jpg',
          description: 'Requested for 6 new vehicles for medical emergency.',
          time: '08:30PM Today',
          route: 'vehicles'
        }
      ]
    }
  },
  methods: {
    handleNotificationClick(notification) {
      if (notification.route) {
        this.$inertia.visit(route(notification.route))
      }
    },
    markAsRead(notificationId) {
      // Implement mark as read functionality
      this.notifications = this.notifications.filter(n => n.id !== notificationId)
    }
  }
}
</script>
