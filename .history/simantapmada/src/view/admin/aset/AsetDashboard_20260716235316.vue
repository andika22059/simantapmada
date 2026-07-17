<template>
  <div class="vue-aset-body">
    <div class="aset-page">
      <div class="aset-header">
        <div>
          <h2>
            <i class="fa-solid fa-boxes-stacked" style="color: #34d399"></i>
            Dashboard Aset Desa
          </h2>
          <p>Ringkasan data kekayaan dan pelabelan fisik barang milik desa.</p>
        </div>
        <button class="btn-back-main" @click="router.push('/admin')">
          <i class="fa-solid fa-house"></i> Menu Utama
        </button>
      </div>

      <div class="aset-stats">
        <div class="stat-card c-blue">
          <div class="stat-icon"><i class="fa-solid fa-cube"></i></div>
          <div class="stat-info">
            <span>Total Aset</span>
            <h3>{{ total }}</h3>
          </div>
        </div>
        <div class="stat-card c-emerald">
          <div class="stat-icon">
            <i class="fa-solid fa-map-location-dot"></i>
          </div>
          <div class="stat-info">
            <span>Tanah</span>
            <h3>{{ tanah }}</h3>
          </div>
        </div>
        <div class="stat-card c-purple">
          <div class="stat-icon"><i class="fa-solid fa-building"></i></div>
          <div class="stat-info">
            <span>Gedung</span>
            <h3>{{ gedung }}</h3>
          </div>
        </div>
        <div class="stat-card c-orange">
          <div class="stat-icon"><i class="fa-solid fa-car-side"></i></div>
          <div class="stat-info">
            <span>Kendaraan</span>
            <h3>{{ kendaraan }}</h3>
          </div>
        </div>
        <div class="stat-card c-slate">
          <div class="stat-icon">
            <i class="fa-solid fa-screwdriver-wrench"></i>
          </div>
          <div class="stat-info">
            <span>Peralatan</span>
            <h3>{{ peralatan }}</h3>
          </div>
        </div>
      </div>

      <div class="aset-kondisi">
        <div class="kondisi-card kondisi-baik">
          <div class="k-icon"><i class="fa-solid fa-circle-check"></i></div>
          <div class="k-info">
            <span>Kondisi Baik</span>
            <h4>{{ baik }}</h4>
          </div>
        </div>
        <div class="kondisi-card kondisi-ringan">
          <div class="k-icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
          </div>
          <div class="k-info">
            <span>Rusak Ringan</span>
            <h4>{{ ringan }}</h4>
          </div>
        </div>
        <div class="kondisi-card kondisi-berat">
          <div class="k-icon"><i class="fa-solid fa-circle-xmark"></i></div>
          <div class="k-info">
            <span>Rusak Berat</span>
            <h4>{{ berat }}</h4>
          </div>
        </div>
      </div>

      <div class="aset-table-card">
        <div class="card-title-row">
          <h3>
            <i class="fa-solid fa-clock-rotate-left" style="color: #10b981"></i>
            5 Rekaman Inventaris Terbaru
          </h3>
          <div style="display: flex; gap: 10px; flex-wrap: wrap">
            <button
              @click="router.push('/admin/aset/list')"
              class="btn-outline"
            >
              <i class="fa-solid fa-list"></i> Daftar Aset Desa
            </button>
            <button
              @click="router.push('/admin/aset/tambah')"
              class="btn-primary"
            >
              <i class="fa-solid fa-plus"></i> Input Aset Baru
            </button>
          </div>
        </div>

        <div style="overflow-x: auto">
          <table class="aset-table">
            <thead>
              <tr>
                <th>Nomor Aset</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Tahun</th>
                <th>Kondisi</th>
                <th style="text-align: center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoading">
                <td
                  colspan="6"
                  style="text-align: center; padding: 40px; color: #94a3b8"
                >
                  <i
                    class="fa-solid fa-spinner fa-spin"
                    style="font-size: 40px; margin-bottom: 10px"
                  ></i
                  ><br />Memuat data aset...
                </td>
              </tr>

              <tr v-else-if="recentAset.length === 0">
                <td
                  colspan="6"
                  style="text-align: center; padding: 40px; color: #94a3b8"
                >
                  <i
                    class="fa-solid fa-box-open"
                    style="font-size: 40px; margin-bottom: 10px"
                  ></i
                  ><br />Belum ada data aset tercatat.
                </td>
              </tr>

              <tr v-for="row in recentAset" :key="row.id">
                <td>
                  <span class="kode-badge">{{ row.nomor_aset }}</span>
                </td>
                <td>
                  <span class="nama-aset">{{ row.nama_aset }}</span>
                </td>
                <td>
                  <span
                    style="
                      font-weight: 700;
                      color: #475569;
                      background: #f1f5f9;
                      padding: 4px 8px;
                      border-radius: 6px;
                      font-size: 12px;
                    "
                    >{{ row.jenis_aset }}</span
                  >
                </td>
                <td>
                  <span style="font-weight: 700; color: #0f172a">{{
                    row.tahun_perolehan
                  }}</span>
                </td>
                <td>
                  <span :class="['badge', getKondisiBadge(row.kondisi)]">{{
                    row.kondisi
                  }}</span>
                </td>
                <td style="text-align: center">
                  <button class="btn-detail" @click="bukaModal(row)">
                    <i class="fa-solid fa-eye"></i> Detail
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <Teleport to="body">
      <div
        id="modalAset"
        class="modal-overlay"
        :class="{ active: isModalOpen }"
        @click.self="tutupModal"
      >
        <div class="modal-box">
          <div class="modal-header">
            <h3>
              <i class="fa-solid fa-box-open" style="color: #10b981"></i>
              Rincian Aset Desa (KIB)
            </h3>
            <button class="btn-close" @click="tutupModal">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <div class="modal-content">
            <div class="preview-top" id="area-foto">
              <img
                v-if="selectedAset.foto && selectedAset.foto.length > 0"
                :src="selectedAset.foto[0]"
                alt="Foto Aset"
                style="width: 100%; height: 100%; object-fit: cover"
              />
              <div
                v-else
                style="
                  width: 100%;
                  height: 100%;
                  background: #e2e8f0;
                  display: flex;
                  justify-content: center;
                  align-items: center;
                  color: #94a3b8;
                "
              >
                <i class="fa-solid fa-image" style="font-size: 50px"></i>
              </div>
            </div>

            <div class="action-bar">
              <button id="btnCetakModal" class="btn-modal-action">
                <i class="fa-solid fa-print"></i> Cetak Dokumen & Label KIB
              </button>
              <button
                class="btn-modal-action"
                style="background: #ef4444"
                @click="hapusAset(selectedAset.id)"
              >
                <i class="fa-solid fa-trash-can"></i> Hapus Aset
              </button>
            </div>

            <div class="info-qr-grid">
              <div class="info-side">
                <div style="margin-bottom: 25px">
                  <span class="label-sm">Nama Inventaris Aset</span>
                  <div
                    class="value-lg"
                    id="d-nama"
                    style="font-size: 22px; color: #10b981"
                  >
                    {{ selectedAset.nama_aset || "-" }}
                  </div>
                </div>

                <div class="info-group-grid">
                  <div class="info-item">
                    <span class="label-sm">Nomor Register Aset</span
                    ><span
                      class="value-lg"
                      id="d-kode"
                      style="
                        font-family: monospace;
                        color: #3b82f6;
                        font-size: 16px;
                      "
                      >{{ selectedAset.nomor_aset || "-" }}</span
                    >
                  </div>
                  <div class="info-item">
                    <span class="label-sm">Kategori Jenis</span
                    ><span class="value-lg" id="d-jenis">{{
                      selectedAset.jenis_aset || "-"
                    }}</span>
                  </div>
                  <div class="info-item">
                    <span class="label-sm">Tahun Perolehan</span
                    ><span class="value-lg" id="d-tahun">{{
                      selectedAset.tahun_perolehan || "-"
                    }}</span>
                  </div>
                  <div class="info-item">
                    <span class="label-sm">Kondisi Fisik</span
                    ><span
                      :class="['badge', getKondisiBadge(selectedAset.kondisi)]"
                      style="margin-top: 5px; display: inline-block"
                      >{{ selectedAset.kondisi || "-" }}</span
                    >
                  </div>
                </div>

                <div
                  style="
                    background: #f0fdf4;
                    padding: 15px;
                    border-radius: 12px;
                    border: 1px dashed #bbf7d0;
                    margin-top: 20px;
                  "
                >
                  <span class="label-sm">Penempatan & Estimasi Nilai</span>
                  <div
                    style="
                      display: flex;
                      justify-content: space-between;
                      margin-top: 5px;
                      align-items: center;
                    "
                  >
                    <span
                      style="font-weight: 700; color: #0f172a; font-size: 16px"
                      id="d-lokasi"
                      >{{ selectedAset.lokasi || "-" }}</span
                    >
                    <span
                      style="font-weight: 800; color: #10b981; font-size: 18px"
                      id="d-nilai"
                      >{{
                        formatRupiahTampilan(selectedAset.nilai_perolehan)
                      }}</span
                    >
                  </div>
                </div>
              </div>

              <div class="qr-box">
                <div id="qrcode-display" class="qr-canvas">
                  <img
                    v-if="selectedAset.id"
                    :src="qrUrl"
                    alt="QR Label Aset"
                    style="width: 100%; height: 100%; object-fit: contain"
                  />
                  <i
                    v-else
                    class="fa-solid fa-qrcode"
                    style="font-size: 80px; color: #cbd5e1"
                  ></i>
                </div>
                <span
                  style="
                    font-size: 11px;
                    font-weight: 800;
                    color: #64748b;
                    margin-top: 15px;
                    text-align: center;
                  "
                  >SCAN LABEL FISIK</span
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const router = useRouter();

// ==========================================
// STATE & DATA
// ==========================================
const isModalOpen = ref(false);
const selectedAset = ref({});
const isLoading = ref(true);
const dataAset = ref([]);

const fetchAset = async () => {
  isLoading.value = true;
  try {
    const res = await axios.get(`${import.meta.env.VITE_API_URL}/api/aset`);
    dataAset.value = res.data?.data || [];
  } catch (error) {
    console.error("Gagal memuat data aset:", error);
    dataAset.value = [];
  } finally {
    isLoading.value = false;
  }
};

onMounted(fetchAset);

const total = computed(() => dataAset.value.length);
const tanah = computed(
  () => dataAset.value.filter((a) => a.jenis_aset === "Tanah").length,
);
const gedung = computed(
  () => dataAset.value.filter((a) => a.jenis_aset === "Gedung").length,
);
const kendaraan = computed(
  () => dataAset.value.filter((a) => a.jenis_aset === "Kendaraan").length,
);
const peralatan = computed(
  () => dataAset.value.filter((a) => a.jenis_aset === "Peralatan").length,
);

const baik = computed(
  () => dataAset.value.filter((a) => a.kondisi === "Baik").length,
);
const ringan = computed(
  () => dataAset.value.filter((a) => a.kondisi === "Rusak Ringan").length,
);
const berat = computed(
  () => dataAset.value.filter((a) => a.kondisi === "Rusak Berat").length,
);

// 🔥 5 data teratas (backend sudah urutkan dari yang terbaru)
const recentAset = computed(() => dataAset.value.slice(0, 5));

// URL halaman scan publik (dibuka warga tanpa login setelah scan QR)
const scanUrl = computed(
  () => `${window.location.origin}/scan/aset/${selectedAset.value?.id || ""}`,
);

// QR aset — isinya URL halaman scan publik, jadi saat di-scan langsung
// mengarah ke detail aset. Sama seperti di halaman Daftar Aset.
const qrUrl = computed(
  () =>
    `https://api.qrserver.com/v1/create-qr-code/?size=240x240&margin=8&data=${encodeURIComponent(
      scanUrl.value,
    )}`,
);


const formatRupiahTampilan = (angka) => {
  if (!angka) return "Rp 0";
  return (
    "Rp " + Number(angka).toLocaleString("id-ID", { maximumFractionDigits: 0 })
  );
};

// Helper Warna Badge Kondisi
const getKondisiBadge = (kondisi) => {
  if (kondisi === "Baik") return "badge-baik";
  if (kondisi === "Rusak Ringan") return "badge-ringan";
  if (kondisi === "Rusak Berat") return "badge-berat";
  return "badge-baik";
};

// Logika Modal
const bukaModal = (row) => {
  selectedAset.value = row;
  isModalOpen.value = true;
};

const tutupModal = () => {
  isModalOpen.value = false;
};

// Hapus Aset
const hapusAset = async (id) => {
  const konfirmasi = await Swal.fire({
    icon: "warning",
    title: "Hapus Data Aset?",
    text: "Data dan foto aset ini akan dihapus permanen dari database!",
    showCancelButton: true,
    confirmButtonText: "Ya, Hapus",
    cancelButtonText: "Batal",
    confirmButtonColor: "#ef4444",
    cancelButtonColor: "#64748b",
  });

  if (!konfirmasi.isConfirmed) return;

  try {
    await axios.delete(`${import.meta.env.VITE_API_URL}/api/aset/${id}`);
    tutupModal();
    await fetchAset();
    Swal.fire({
      icon: "success",
      title: "Terhapus!",
      text: "Data aset berhasil dihapus dari database.",
      confirmButtonColor: "#10b981",
      timer: 2500,
      timerProgressBar: true,
    });
  } catch (error) {
    console.error("Gagal menghapus aset:", error);
    Swal.fire({
      icon: "error",
      title: "Gagal Menghapus",
      text: "Terjadi kesalahan saat menghapus data. Cek koneksi server/database.",
      confirmButtonColor: "#10b981",
    });
  }
};
</script>

<style>
/* =====================================================================
   SiMANTAP SYSTEM - COLORFUL PREMIUM ECOSYSTEM
   Module: Asset Management Dashboard (ULTRA SAAS EDITION)
   DITANAM LANGSUNG BIAR AMAN DARI BUG PATH
   ===================================================================== */
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.vue-aset-body {
  font-family: "Plus Jakarta Sans", sans-serif;
  background-color: #f8fafc;
  background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
  background-size: 24px 24px;
  color: #1e293b;
  margin: 0;
  padding: 40px 20px 60px;
  min-height: 100vh;
  box-sizing: border-box;
}

.aset-page {
  max-width: 95%;
  margin: 0 auto;
  animation: premiumFadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes premiumFadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* === HEADER PREMIUM (GLASSMORPHISM) === */
.aset-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 35px;
  background-color: #1e293b;
  background-image: radial-gradient(
    rgba(255, 255, 255, 0.08) 1.2px,
    transparent 1.2px
  );
  background-size: 22px 22px;
  padding: 28px 35px;
  border-radius: 20px;
  border: none;
  box-shadow: 0 15px 35px -10px rgba(15, 23, 42, 0.35);
}

.aset-header div h2 {
  margin: 0;
  font-size: 26px;
  font-weight: 800;
  color: #ffffff;
  display: flex;
  align-items: center;
  gap: 12px;
  letter-spacing: -0.5px;
}

.aset-header p {
  margin: 6px 0 0;
  color: #94a3b8;
  font-size: 14px;
  font-weight: 500;
}

.btn-back-main {
  background: rgba(255, 255, 255, 0.08);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.18);
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 700;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  transition: 0.3s;
  cursor: pointer;
}
.btn-back-main:hover {
  background: #ffffff;
  color: #1e293b;
  border-color: #ffffff;
  transform: translateY(-2px);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
}

/* === STATS CARDS (COLOR POPPING 3D) === */
.aset-stats {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 20px;
  margin-bottom: 25px;
}

.stat-card {
  background: white;
  padding: 25px 20px;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 15px;
  border: 1px solid #f1f5f9;
  position: relative;
  overflow: hidden;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.stat-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 5px;
}

.stat-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
}

.c-blue::before {
  background: var(--primary);
}
.c-blue .stat-icon {
  background: var(--primary-soft);
  color: var(--primary);
}

.c-emerald::before {
  background: var(--primary-dark);
}
.c-emerald .stat-icon {
  background: var(--primary-soft);
  color: var(--primary-dark);
}

.c-purple::before {
  background: #0f766e;
}
.c-purple .stat-icon {
  background: #f0fdfa;
  color: #0f766e;
}

.c-orange::before {
  background: #047857;
}
.c-orange .stat-icon {
  background: #d1fae5;
  color: #047857;
}

.c-slate::before {
  background: var(--navy);
}
.c-slate .stat-icon {
  background: #f1f5f9;
  color: var(--navy);
}

.stat-icon {
  width: 55px;
  height: 55px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  transition: 0.3s;
}
.stat-card:hover .stat-icon {
  transform: scale(1.1) rotate(5deg);
}

.stat-info span {
  font-size: 12px;
  color: #64748b;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.stat-info h3 {
  margin: 5px 0 0 0;
  font-size: 30px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1;
}

/* === KONDISI ASET === */
.aset-kondisi {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-bottom: 35px;
}
.kondisi-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 15px;
  border: 1px solid #f1f5f9;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.02);
  transition: 0.3s;
}
.kondisi-card:hover {
  transform: translateY(-3px);
}

.kondisi-baik {
  border-left: 5px solid #10b981;
}
.kondisi-baik .k-icon {
  color: #10b981;
  background: #ecfdf5;
}

.kondisi-ringan {
  border-left: 5px solid #f59e0b;
}
.kondisi-ringan .k-icon {
  color: #f59e0b;
  background: #fef3c7;
}

.kondisi-berat {
  border-left: 5px solid #ef4444;
}
.kondisi-berat .k-icon {
  color: #ef4444;
  background: #fef2f2;
}

.k-icon {
  width: 45px;
  height: 45px;
  border-radius: 12px;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 20px;
}
.k-info span {
  font-size: 11px;
  font-weight: 800;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.k-info h4 {
  margin: 2px 0 0 0;
  font-size: 22px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1;
}

/* === TABEL 5 TERBARU === */
.aset-table-card {
  background: white;
  border-radius: 20px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.03);
  border: 1px solid #e2e8f0;
  overflow: hidden;
}

.card-title-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 25px 30px;
  border-bottom: 2px dashed #f1f5f9;
  background: #ffffff;
  flex-wrap: wrap;
  gap: 15px;
}
.card-title-row h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 800;
  color: #0f172a;
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-outline,
.btn-primary {
  padding: 10px 20px;
  border-radius: 10px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: 0.3s;
}
.btn-outline {
  background: white;
  color: #0f172a;
  border: 2px solid #e2e8f0;
}
.btn-outline:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
  transform: translateY(-2px);
}

.btn-primary {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border: none;
  box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
}
.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(16, 185, 129, 0.3);
}

.aset-table {
  width: 100%;
  border-collapse: collapse;
}
.aset-table th {
  background: #f8fafc;
  color: #0f172a;
  font-weight: 800;
  font-size: 12px;
  text-transform: uppercase;
  padding: 18px 20px;
  text-align: left;
  border-bottom: 2px solid #e2e8f0;
  letter-spacing: 0.5px;
}
.aset-table td {
  padding: 18px 20px;
  font-size: 14px;
  color: #334155;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-weight: 500;
}
.aset-table tbody tr {
  transition: 0.2s;
}
.aset-table tbody tr:hover td {
  background: #f8fafc;
}

.kode-badge {
  font-family: monospace;
  font-size: 14px;
  font-weight: 800;
  color: #3b82f6;
  background: #eff6ff;
  padding: 4px 8px;
  border-radius: 6px;
  border: 1px dashed #bfdbfe;
}
.nama-aset {
  font-weight: 700;
  color: #0f172a;
  font-size: 15px;
}

/* Badge Kondisi */
.badge {
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: inline-block;
}
.badge-baik {
  background: #ecfdf5;
  color: #059669;
  border: 1px solid #a7f3d0;
}
.badge-ringan {
  background: #fff7ed;
  color: #ea580c;
  border: 1px solid #fed7aa;
}
.badge-berat {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fca5a5;
}

.btn-detail {
  background: #ffffff;
  color: #10b981;
  border: 2px solid #a7f3d0;
  padding: 8px 16px;
  border-radius: 10px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  transition: 0.3s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.btn-detail:hover {
  background: #10b981;
  color: white;
  border-color: #10b981;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
}

/* === MODAL POPUP (KIB) === */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(15, 23, 42, 0.7);
  backdrop-filter: blur(8px);
  z-index: 100000;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  visibility: hidden;
  transition: 0.3s;
}
.modal-overlay.active {
  opacity: 1;
  visibility: visible;
}

.modal-box {
  background: white;
  width: 850px;
  max-width: 95%;
  border-radius: 24px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
  transform: scale(0.95) translateY(20px);
  transition: 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.modal-overlay.active .modal-box {
  transform: scale(1) translateY(0);
}

.modal-header {
  padding: 20px 30px;
  border-bottom: 1px solid #f1f5f9;
  background: #ffffff;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.modal-header h3 {
  margin: 0;
  font-size: 18px;
  color: #0f172a;
  font-weight: 800;
  display: flex;
  align-items: center;
  gap: 10px;
}
.btn-close {
  background: #f1f5f9;
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: #94a3b8;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: 0.2s;
}
.btn-close:hover {
  background: #fee2e2;
  color: #ef4444;
  transform: rotate(90deg);
}

.modal-content {
  background: #f8fafc;
  display: flex;
  flex-direction: column;
  max-height: 80vh;
  overflow-y: auto;
}
.preview-top {
  height: 250px;
  background: #1e293b;
  display: flex;
  justify-content: center;
  align-items: center;
}

.action-bar {
  padding: 15px 30px;
  background: white;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}
.btn-modal-action {
  background: #1e293b;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 10px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: 0.3s;
}
.btn-modal-action:hover {
  background: #0f172a;
  transform: translateY(-2px);
}

.info-qr-grid {
  display: grid;
  grid-template-columns: 1fr 240px;
  background: white;
}
.info-side {
  padding: 30px;
}
.label-sm {
  font-size: 11px;
  color: #94a3b8;
  font-weight: 800;
  text-transform: uppercase;
  display: block;
  margin-bottom: 4px;
  letter-spacing: 0.5px;
}

.info-group-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 25px;
}
.info-item {
  display: flex;
  flex-direction: column;
}
.value-lg {
  font-size: 15px;
  color: #0f172a;
  font-weight: 700;
}

.qr-box {
  background: #f8fafc;
  border-left: 1px dashed #e2e8f0;
  padding: 30px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.qr-canvas {
  width: 140px;
  height: 140px;
  background: white;
  border: 2px solid #e2e8f0;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
}

/* === MOBILE RESPONSIVE MASTER === */
@media (max-width: 1100px) {
  .aset-stats {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (max-width: 900px) {
  .aset-stats {
    grid-template-columns: repeat(3, 1fr);
  }
  .info-qr-grid {
    grid-template-columns: 1fr;
  }
  .qr-box {
    border-left: none;
    border-top: 1px dashed #e2e8f0;
  }
}

@media (max-width: 768px) {
  .vue-aset-body {
    padding: 30px 15px 40px !important;
    overflow-x: hidden !important;
  }
  .aset-page {
    width: 100% !important;
    max-width: 100vw !important;
    padding: 0 !important;
  }

  .aset-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
    padding: 25px 20px;
  }
  .btn-back-main {
    width: 100%;
    justify-content: center;
  }

  .aset-stats {
    grid-template-columns: 1fr 1fr;
    gap: 15px;
  }
  .aset-kondisi {
    grid-template-columns: 1fr;
    gap: 15px;
  }

  .card-title-row {
    flex-direction: column;
    align-items: stretch;
    padding: 20px;
    gap: 15px;
  }
  .card-title-row div {
    flex-direction: column;
    width: 100%;
  }
  .btn-outline,
  .btn-primary {
    width: 100%;
    justify-content: center;
  }

  .aset-table-card {
    border-radius: 16px;
  }
  .aset-table {
    min-width: 800px;
  }

  .info-group-grid {
    grid-template-columns: 1fr;
    gap: 15px;
  }
}

@media (max-width: 480px) {
  .aset-header {
    padding: 18px 15px;
  }
  .aset-stats {
    gap: 10px;
  }
  .custom-table th {
    font-size: 11px;
    padding: 12px 13px;
  }
  .custom-table td {
    font-size: 13px;
    padding: 12px 13px;
  }
}

@media (max-width: 360px) {
  .aset-stats {
    grid-template-columns: 1fr;
  }
  .card-title-row {
    padding: 15px;
  }
}

/* === ANIMASI PELENGKAP (SELARAS THEME) === */
@keyframes rowFadeIn {
  from {
    opacity: 0;
    transform: translateX(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
</style>
