<template>
  <!-- Sidebar -->
  <ul
    class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion"
    id="accordionSidebar"
    :class="{ toggled: sidebarToggled }"
  >
    <!-- Sidebar - Brand -->
    <a
      class="sidebar-brand d-flex align-items-center justify-content-center"
      href="#"
    >
      <div class="sidebar-brand-icon mt-2">
        <!-- <img src="/images/KabupatenSukoharjo.png" alt="logo" width="50" /> -->
      </div>
      <div class="sidebar-brand-text mx-3"><sup>Web</sup>Members</div>
    </a>

    <!-- Heading -->
    <div class="sidebar-heading mt-5">Menu</div>
    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
      <router-link class="nav-link" to="/user-dashboard">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
      </router-link>
    </li>

    <!-- Nav Item - note -->
    <li class="nav-item">
      <router-link class="nav-link" to="/invoice-page">
        <i class="bi bi-journal-text"></i>
        <span>Invoice</span>
      </router-link>
    </li>
    <!-- Nav Item - note -->
    <li class="nav-item">
      <router-link class="nav-link" to="/layanan-page">
        <i class="bi bi-graph-up-arrow"></i>
        <span>Layanan</span>
      </router-link>
    </li>
    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block" />
  </ul>
  <!-- End of Sidebar -->
</template>

<script>
import { EventBus } from '../event-bus';
export default {
  data() {
    return {
      sidebarToggled: false,
    };
  },
  methods: {
  },
  created() {
    EventBus.$on('toggle-sidebar', () => {
      this.sidebarToggled = !this.sidebarToggled;
    });
  },
};
</script>
