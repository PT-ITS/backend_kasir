<template>
  <div id="wrapper">
    <sidebar-admin />

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">
        <navbar />

        <!-- Begin Page Content -->
        <h1 class="text-center h3">List Invoice</h1>
        <div class="row">
          <div class="col-sm-1"></div>
          <div class="col-sm-10">
            <div class="table-responsive">
              <DataTable
                class="tableCustom display table table-striped"
                v-if="ready"
              >
                <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nama</th>
                    <th scope="col">No. Rekening</th>
                    <th scope="col">Jumlah Transfer</th>
                    <th scope="col">Bukti Transfer</th>
                    <th scope="col">Berlaku Hingga</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in invoices" :key="item.id">
                    <th scope="row">{{ index + 1 }}</th>
                    <td>{{ item.user['name'] }}</td>
                    <td>{{ item.nomor_rekening }}</td>
                    <td>{{ item.jumlah_transfer }}</td>
                    <td>{{ item.bukti_transfer }}</td>
                    <td>{{ item.user['tanggal_berakhir'] }}</td>
                    
          
                  </tr>
                </tbody>
              </DataTable>
            </div>
          </div>
          <div class="col-sm-1"></div>
        </div>
        <!-- /.container-fluid -->
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
import Swal from "sweetalert2";
import DataTable from "datatables.net-vue3";
import DataTablesCore from "datatables.net";
export default {
  data() {
    return {
      invoices: [],
    };
  },
  methods: {
     async fetchDataListInvoice() {
      try {
        const response = await axios.get(
          `http://localhost:8000/api/auth/list-invoice`,
          {
            headers: {
              Authorization: "Bearer " + localStorage.getItem("token"),
            },
          }
        );
        this.invoices = response.data.data;
        console.log('ini invoice',this.invoices);
      } catch (error) {
        console.error(error);
      }
    },
    
  },
  created() {
    this.fetchDataListInvoice();
  },
};
</script>

<style scoped>
@media (min-width: 768px) { /* Ubah ukuran sesuai kebutuhan */
  .table-responsive{
    display:inline;
    width:100%;
    overflow-x:hidden;
    /* -webkit-overflow-scrolling:touch */
    }
}
span {
  cursor: auto !important;
}
</style>