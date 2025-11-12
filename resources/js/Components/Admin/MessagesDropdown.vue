<template>
  <div class="dropdown">
    <a class="dropdown-toggle header-icon" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="ri-message-3-line"></i>
      <span class="count-label">{{ unreadMessages.length }}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-300">
      <h5 class="fw-semibold px-3 py-2 text-primary">Messages</h5>

      <!-- Scroll starts -->
      <div class="scroll300">
        <!-- Messages list starts -->
        <div class="p-3">
          <div v-for="message in messages" :key="message.id" class="d-flex py-2 message-item">
            <img :src="message.avatar" class="img-shadow img-3x me-3 rounded-5" alt="User Avatar">
            <div class="m-0">
              <h6 class="mb-1 fw-semibold">{{ message.name }}</h6>
              <p class="mb-1 text-truncate" style="max-width: 200px;">
                {{ message.preview }}
              </p>
              <p class="small m-0 opacity-50">{{ message.time }}</p>
            </div>
            <button v-if="!message.read" class="btn btn-sm btn-primary ms-2 mark-read-btn" @click="markAsRead(message.id)">
              <i class="ri-check-line"></i>
            </button>
          </div>
        </div>
        <!-- Messages list ends -->
      </div>
      <!-- Scroll ends -->

      <!-- View all button starts -->
      <div class="d-grid m-3">
        <Link href="/messages" class="btn btn-primary">View all</Link>
      </div>
      <!-- View all button ends -->
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
  name: 'MessagesDropdown',
  components: {
    Link
  },
  data() {
    return {
      messages: [
        {
          id: 1,
          name: 'Nick Gonzalez',
          avatar: '/assets/images/user3.png',
          preview: 'Appointed as a new President 2014-2025',
          time: 'Today, 07:30pm',
          read: false,
          route: 'messages.view'
        },
        {
          id: 2,
          name: 'Clyde Fowler',
          avatar: '/assets/images/user1.png',
          preview: 'Congratulate, James for new job.',
          time: 'Today, 08:00pm',
          read: false,
          route: 'messages.view'
        },
        {
          id: 3,
          name: 'Sophie Michiels',
          avatar: '/assets/images/user4.png',
          preview: 'Lewis added new doctors training schedule.',
          time: 'Today, 09:30pm',
          read: true,
          route: 'messages.view'
        }
      ]
    }
  },
  computed: {
    unreadMessages() {
      return this.messages.filter(message => !message.read)
    }
  },
  methods: {
    handleMessageClick(message) {
      if (message.route) {
        this.$inertia.visit(route(message.route))
      }
    },
    markAsRead(messageId) {
      // Implement mark as read functionality
      const message = this.messages.find(m => m.id === messageId)
      if (message) {
        message.read = true
      }
    },
    loadMessages() {
      // Implement message loading from API
      this.$inertia.reload({ only: ['messages'] })
    }
  },
  mounted() {
    // Optionally load messages on component mount
    // this.loadMessages()
  }
}
</script>

<style scoped>
.message-item {
  position: relative;
  transition: background-color 0.2s ease;
}

.message-item:hover {
  background-color: rgba(0, 0, 0, 0.02);
  border-radius: 8px;
}

.mark-read-btn {
  width: 24px;
  height: 24px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.count-label {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #dc3545;
  color: white;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  font-size: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.count-label.success {
  background: #198754;
}

.count-label.warning {
  background: #ffc107;
}
</style>
