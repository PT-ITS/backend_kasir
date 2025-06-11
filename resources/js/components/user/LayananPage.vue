<template>
  <div id="wrapper">
    <sidebar />

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">
        <navbar />

        <!-- Begin Page Content -->
        <h1 class="text-center h3">Layanan</h1>

        <!-- Embed Screenleap iframe -->
        <div class="d-flex justify-content-center">
  <iframe
    src="https://screenleap.com/523071095"
    width="90%"
    height="500px"
    frameborder="0"
    allowfullscreen
  ></iframe>
</div>

        <!-- End Embed Screenleap iframe -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer />
      <!-- End of Footer -->
    </div>
    <!-- End of Content Wrapper -->
  </div>
  <!-- End of Page Wrapper -->
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {};
  },
  methods: {},
  created() {
    axios
      .get(`http://localhost:8000/api/auth/me/`, {
        headers: {
          Authorization: "Bearer " + localStorage.getItem("token"),
        },
      })
      .then((response) => {
        this.user_id = response.data.id;
        const level = response.data.level;
        console.log("level:", level);
        const token = localStorage.getItem("token");
        const expires_in = localStorage.getItem("expires_in");
        if (!token || !expires_in || new Date() > new Date(expires_in)) {
          localStorage.removeItem("token");
          localStorage.removeItem("expires_in");
          this.$router.push("/");
        } else if (level != "0") {
          this.$router.push("/unauthorized");
        } else {
          console.log("success");
        }
      })
      .catch((error) => {
        console.error(error);
        this.$router.push("/");
      });
  },
};
</script>

<style scoped>
span {
  cursor: auto !important;
}
</style>
