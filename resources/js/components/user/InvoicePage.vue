<template>
  <div id="wrapper">
    <sidebar />

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">
        <navbar />

        <!-- Begin Page Content -->
        <h1 class="text-center h3">List Pengajuan Surat</h1>
        <div class="row mt-5">
          <div class="col-md-1"></div>
          <div class="col-md-10">
            <div class="row">
              <div class="col-md-10"></div>
              <div class="col-md-2">
                <button
                  class="btn btn-primary"
                  data-toggle="modal"
                  data-target="#addInvoiceModal"
                >
                  Tambah Invoice
                </button>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-striped mt-2">
                <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nomor Rekening</th>
                    <th scope="col">Jumlah Transfer</th>
                    <th scope="col">Bukti Transfer</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                  </tr>
                </thead>
                <!-- Table Body -->
                <tbody>
                  <tr v-for="(item, index) in invoices" :key="item.id">
                    <th scope="row">{{ index + 1 }}</th>
                    <td>{{ item.nomor_rekening }}</td>
                    <td>{{ item.jumlah_transfer }}</td>
                    <td>
                      <!-- Tampilkan gambar di dalam tabel -->
                      <img
                        :src="
                          'http://localhost:8000/storage/' + item.bukti_transfer
                        "
                        alt="Bukti Transfer"
                        style="max-width: 100px"
                      />
                    </td>
                    <td>
                      {{ 
                        item.status === '0' ? 'Pending' :
                        item.status === '1' ? 'Active' :
                        item.status === '2' ? 'Expired' : 'Reject'
                      }}
                    </td>
                    <td>
                      <button class="btn btn-warning">Perpanjang</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-md-1"></div>
        </div>
        <!-- /.container-fluid -->

        <!-- Modal Tambah Invoice -->
        <div
          class="modal fade"
          id="addInvoiceModal"
          tabindex="-1"
          role="dialog"
          aria-labelledby="addInvoiceModalLabel"
          aria-hidden="true"
          ref="addInvoiceModalRef"
        >
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="addInvoiceModalLabel">
                  Tambah Invoice
                </h5>
                <button
                  type="button"
                  class="close"
                  data-dismiss="modal"
                  aria-label="Close"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <!-- Form Tambah Invoice -->
                <form @submit.prevent="addInvoice">
                  <div class="form-group">
                    <label for="nomorRekening">Nomor Rekening</label>
                    <input
                      type="text"
                      class="form-control"
                      id="nomorRekening"
                      v-model="newInvoice.nomor_rekening"
                    />
                  </div>
                  <div class="form-group">
                    <label for="jumlahTransfer">Jumlah Transfer</label>
                    <input
                      type="text"
                      class="form-control"
                      id="jumlahTransfer"
                      v-model="newInvoice.jumlah_transfer"
                    />
                  </div>
                  <div class="form-group">
                    <label for="buktiTransfer">Bukti Transfer</label>
                    <input
                      type="file"
                      class="form-control"
                      id="buktiTransfer"
                      @change="handleFileUpload"
                    />
                  </div>
                  <div class="modal-footer">
                    <button
                      type="button"
                      class="btn btn-secondary"
                      data-dismiss="modal"
                    >
                      Close
                    </button>
                    <button type="submit" class="btn btn-primary">
                      Tambah
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal Tambah Invoice -->
      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer />
      <!-- End of Footer -->
    </div>
    <!-- End of Content Wrapper -->
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      invoices: [],
      user_id: "",
      newInvoice: {
        nomor_rekening: "",
        jumlah_transfer: "",
        bukti_transfer: null,
      },
    };
  },
  methods: {
    async fetchDataInvoice() {
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
      } catch (error) {
        console.error(error);
      }
    },
    addInvoice() {
      // Membuat FormData untuk mengirim file
      const formData = new FormData();
      formData.append("nomor_rekening", this.newInvoice.nomor_rekening);
      formData.append("jumlah_transfer", this.newInvoice.jumlah_transfer);
      formData.append("bukti_transfer", this.newInvoice.bukti_transfer);
      formData.append("user_id", this.user_id);

      axios
        .post("http://localhost:8000/api/auth/create-invoice", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
            Authorization: "Bearer " + localStorage.getItem("token"),
          },
        })
        .then((response) => {
          console.log(response.data);
          // Lakukan sesuatu setelah berhasil menambah invoice
          this.newInvoice = {
            nomor_rekening: "",
            jumlah_transfer: "",
            bukti_transfer: null,
          };
          this.showAlert();
        })
        .catch((error) => {
          console.error(error);
        });
    },

    showAlert() {
      // Use sweetalert2
      this.$swal("Data Berhasil Dikirm!!").then(() => {
        $("#addInvoiceModal").modal("hide");
      });
    },

    handleFileUpload(event) {
      // Menggunakan FormData untuk mengirim file
      this.newInvoice.bukti_transfer = event.target.files[0];
    },
  },
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
          this.fetchDataInvoice();
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
