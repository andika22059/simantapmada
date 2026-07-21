<template>
  <div class="surat-keluar-body">
    <div class="wrapper">
      <div class="page-header">
        <div class="header-text">
          <h2>
            <i class="fa-solid fa-paper-plane text-rose"></i> Surat Keluar
          </h2>
          <p>
            Pengelolaan, penerbitan, dan integrasi otomatis arsip keluar
            instansi.
          </p>
        </div>
        <div class="header-actions">
          <button
            @click="router.push('/admin')"
            class="btn-back-white"
            style="border: none"
          >
            <i class="fa-solid fa-arrow-left"></i> Dashboard Utama
          </button>
        </div>
      </div>

      <div class="summary">
        <div class="summary-card card-rose">
          <div class="summary-info">
            <span>Total Keluar</span>
            <h2>{{ totalSurat }}</h2>
          </div>
          <div class="summary-icon">
            <i class="fa-solid fa-folder-open"></i>
          </div>
        </div>
        <div class="summary-card card-orange">
          <div class="summary-info">
            <span>Bulan Ini</span>
            <h2>{{ bulanIni }}</h2>
          </div>
          <div class="summary-icon">
            <i class="fa-solid fa-calendar-days"></i>
          </div>
        </div>
        <div class="summary-card card-violet">
          <div class="summary-info">
            <span>Tahun Ini</span>
            <h2>{{ tahunIni }}</h2>
          </div>
          <div class="summary-icon"><i class="fa-solid fa-chart-pie"></i></div>
        </div>
      </div>

      <div class="action-bar">
        <div class="action-left">
          <button
            @click="router.push('/admin/surat-keluar/tambah')"
            class="btn-primary"
            style="border: none; cursor: pointer"
          >
            <i class="fas fa-plus"></i> Terbitkan Surat Keluar
          </button>
          <button type="button" class="btn-print" @click="cetakRekap">
            <i class="fas fa-print"></i> Cetak Rekap Bulanan
          </button>
        </div>
        <div class="action-right">
          <select v-model="filterBulan" class="filter-select-sk">
            <option value="">-- Semua Bulan --</option>
            <option value="01">Januari</option>
            <option value="02">Februari</option>
            <option value="03">Maret</option>
            <option value="04">April</option>
            <option value="05">Mei</option>
            <option value="06">Juni</option>
            <option value="07">Juli</option>
            <option value="08">Agustus</option>
            <option value="09">September</option>
            <option value="10">Oktober</option>
            <option value="11">November</option>
            <option value="12">Desember</option>
          </select>
          <select v-model="filterTahun" class="filter-select-sk">
            <option value="">-- Semua Tahun --</option>
            <option value="2024">2024</option>
            <option value="2025">2025</option>
            <option value="2026">2026</option>
            <option value="2027">2027</option>
          </select>
          <div class="input-icon">
            <i class="fas fa-search"></i>
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Cari Nomor, Tujuan, Perihal..."
            />
          </div>
        </div>
      </div>

      <div class="table-card">
        <table class="custom-table" id="tableData">
          <thead>
            <tr>
              <th class="col-no">No</th>
              <th class="sortable col-nomor">
                Nomor Surat <i class="fa-solid fa-sort sort-icon"></i>
              </th>
              <th class="sortable col-tanggal">
                Tanggal <i class="fa-solid fa-sort sort-icon"></i>
              </th>
              <th class="sortable col-tujuan">
                Tujuan <i class="fa-solid fa-sort sort-icon"></i>
              </th>
              <th class="sortable col-perihal">
                Perihal <i class="fa-solid fa-sort sort-icon"></i>
              </th>
              <th class="col-aksi" style="text-align: center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoading">
              <td
                colspan="6"
                class="empty-state"
                style="text-align: center; padding: 40px; color: #94a3b8"
              >
                <i
                  class="fa-solid fa-spinner fa-spin"
                  style="font-size: 30px"
                ></i
                ><br />Memuat data surat keluar...
              </td>
            </tr>

            <tr v-else-if="filteredData.length === 0">
              <td
                colspan="6"
                class="empty-state"
                style="text-align: center; padding: 40px; color: #94a3b8"
              >
                <i
                  class="fa-regular fa-folder-open icon-large"
                  style="font-size: 30px"
                ></i
                ><br />Tidak ada data surat keluar.
              </td>
            </tr>

            <tr v-for="(row, index) in pagedData" :key="row.id" :style="{ animationDelay: (index % 12) * 40 + 'ms' }">
              <td class="col-no">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>
              <td
                class="text-mono-rose"
                style="font-weight: 700; color: var(--primary)"
              >
                {{ row.nomor_surat }}
              </td>
              <td>
                <span class="text-date">{{
                  formatTanggal(row.tanggal_surat)
                }}</span>
              </td>
              <td>
                <span
                  class="text-target"
                  style="font-weight: 600; color: #334155"
                  >{{ row.tujuan }}</span
                >
              </td>
              <td class="text-subject">{{ row.perihal }}</td>
              <td style="text-align: center">
                <div
                  class="action-group"
                  style="display: flex; justify-content: center; gap: 5px"
                >
                  <button
                    @click="openModal(row)"
                    class="btn-aksi btn-detail"
                    title="Lihat Detail"
                  >
                    <i class="fa-solid fa-eye"></i>
                  </button>
                  <button
                    @click="router.push('/admin/surat-keluar/edit/' + row.id)"
                    class="btn-aksi btn-edit"
                    title="Edit Surat"
                  >
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="pagination-container">
          <span class="pg-info">
            Menampilkan {{ infoMulai }}–{{ infoAkhir }} dari
            {{ filteredData.length }} surat
          </span>
          <div class="pg-controls" v-if="totalPages > 1">
            <button
              class="pg-btn"
              :disabled="currentPage === 1"
              @click="prevPage"
              title="Sebelumnya"
            >
              <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button
              v-for="p in nomorHalaman"
              :key="p"
              class="pg-btn"
              :class="{ active: p === currentPage }"
              @click="gotoPage(p)"
            >
              {{ p }}
            </button>
            <button
              class="pg-btn"
              :disabled="currentPage === totalPages"
              @click="nextPage"
              title="Selanjutnya"
            >
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <Teleport to="body">
    <div
      class="modal-overlay"
      id="detailModal"
      :class="{ active: isModalOpen }"
      @click.self="closeModal"
    >
      <div class="modal-box-large">
        <div class="modal-header-preview">
          <h3>
            <i class="fa-solid fa-paper-plane text-rose"></i> Detail Surat
            Keluar Resmi
          </h3>
          <button
            class="btn-close-modal"
            id="closeModalBtn"
            @click="closeModal"
          >
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <div class="modal-body-split">
          <div class="split-left">
            <div
              class="preview-box"
              style="
                background: #0f172a;
                border-radius: 12px;
                height: 340px;
                display: flex;
                align-items: center;
                justify-content: center;
              "
            >
              <iframe
                v-if="selectedRow.file_surat"
                :src="selectedRow.file_surat"
                style="
                  width: 100%;
                  height: 100%;
                  border: none;
                  border-radius: 12px;
                "
              ></iframe>
              <div
                v-else
                class="state-box"
                style="color: #cbd5e1; text-align: center"
              >
                <i
                  class="fas fa-file-circle-xmark"
                  style="font-size: 40px; margin-bottom: 10px"
                ></i
                ><br />Preview tidak tersedia.
              </div>
            </div>
            <div
              class="file-action"
              style="margin-top: 10px; display: flex; gap: 10px"
            >
              <a
                id="btnDownload"
                class="btn-file"
                :href="selectedRow.file_surat || '#'"
                download
                style="
                  flex: 1;
                  text-align: center;
                  padding: 10px;
                  border-radius: 8px;
                  font-size: 13px;
                  text-decoration: none;
                "
              >
                <i class="fas fa-download"></i> Unduh Berkas Digital
              </a>
            </div>
          </div>

          <div class="split-right">
            <h4
              class="modal-section-title"
              style="
                margin: 0 0 20px 0;
                border-bottom: 2px solid #f1f5f9;
                padding-bottom: 12px;
                font-size: 15px;
                color: #1e293b;
                font-weight: 800;
              "
            >
              <i class="fa-solid fa-circle-info" style="color: #3b82f6"></i>
              Informasi Surat
            </h4>
            <div class="info-group">
              <label>Nomor Surat</label>
              <p
                class="text-mono-rose-lg"
                style="
                  color: var(--primary);
                  font-weight: 800;
                  font-family: monospace;
                "
              >
                {{ selectedRow.nomor_surat || "-" }}
              </p>
            </div>
            <div class="info-group">
              <label>Tanggal Surat</label>
              <p>{{ formatTanggal(selectedRow.tanggal_surat) }}</p>
            </div>
            <div class="info-group">
              <label>Tujuan Penyerahan</label>
              <p>{{ selectedRow.tujuan || "-" }}</p>
            </div>
            <div class="info-group">
              <label>Perihal Kedinasan</label>
              <p>{{ selectedRow.perihal || "-" }}</p>
            </div>

            <div class="alert-arsip">
              <p>
                <i class="fa-solid fa-shield-halved"></i> Ekosistem SiMANTAP:
                Salinan arsip digital terkunci di database.
              </p>
            </div>

            <button class="btn-hapus" @click="hapusSurat(selectedRow.id)">
              <i class="fas fa-trash-can"></i> Hapus Berkas Surat Keluar
            </button>
          </div>
        </div>
      </div>
    </div>
    </Teleport>

    <!-- ===== AREA CETAK REKAP SURAT KELUAR ===== -->
    <div id="area-cetak-sk" class="area-cetak-sk">
      <div class="kop-cetak-sk">
        <h2>PEMERINTAH DESA MACANAN</h2>
        <p>Kecamatan Loceret, Kabupaten Nganjuk</p>
        <h3>REKAP BUKU AGENDA SURAT KELUAR</h3>
        <hr />
      </div>

      <div class="info-cetak-sk">
        <span v-if="filterBulan || filterTahun"
          >Periode: <b>{{ labelPeriode }}</b></span
        >
        <span
          >Jumlah: <b>{{ filteredData.length }} surat</b></span
        >
      </div>

      <table class="tabel-cetak-sk">
        <thead>
          <tr>
            <th>No</th>
            <th>Nomor Surat</th>
            <th>Tanggal</th>
            <th>Tujuan</th>
            <th>Perihal</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(row, i) in filteredData" :key="row.id">
            <td>{{ i + 1 }}</td>
            <td>{{ row.nomor_surat }}</td>
            <td>{{ formatTanggal(row.tanggal_surat) }}</td>
            <td>{{ row.tujuan }}</td>
            <td>{{ row.perihal }}</td>
          </tr>
        </tbody>
      </table>

      <div class="ttd-cetak-sk">
        <p>Nganjuk, {{ tglCetak }}</p>
        <p>Mengetahui,</p>
        <div class="ttd-space-sk"></div>
        <p class="ttd-nama-sk">( ........................... )</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const router = useRouter();

// ==========================================
// 1. STATE & DATA
// ==========================================
const searchQuery = ref("");
const filterBulan = ref("");
const filterTahun = ref("");

const namaBulanSK = [
  "Januari", "Februari", "Maret", "April", "Mei", "Juni",
  "Juli", "Agustus", "September", "Oktober", "November", "Desember",
];
const labelPeriode = computed(() => {
  const b = filterBulan.value ? namaBulanSK[Number(filterBulan.value) - 1] : "";
  const t = filterTahun.value || "";
  if (b && t) return `${b} ${t}`;
  if (b) return b;
  if (t) return t;
  return "Semua Periode";
});
const isModalOpen = ref(false);
const selectedRow = ref({});
const isLoading = ref(true);

// 🔥 DATA ASLI DARI DATABASE (BUKAN DUMMY LAGI)
const dataSurat = ref([]);

const fetchSuratKeluar = async () => {
  isLoading.value = true;
  try {
    const res = await axios.get(`${import.meta.env.VITE_API_URL}/api/surat-keluar`);
    dataSurat.value = res.data?.data || [];
  } catch (error) {
    console.error("Gagal memuat data surat keluar:", error);
    dataSurat.value = [];
  } finally {
    isLoading.value = false;
  }
};

onMounted(fetchSuratKeluar);

// 🔥 STATISTIK DIHITUNG OTOMATIS DARI DATA ASLI (BUKAN HARDCODE LAGI)
const totalSurat = computed(() => dataSurat.value.length);

const bulanIni = computed(() => {
  const now = new Date();
  return dataSurat.value.filter((s) => {
    if (!s.tanggal_surat) return false;
    const d = new Date(s.tanggal_surat);
    return (
      d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear()
    );
  }).length;
});

const tahunIni = computed(() => {
  const now = new Date();
  return dataSurat.value.filter((s) => {
    if (!s.tanggal_surat) return false;
    const d = new Date(s.tanggal_surat);
    return d.getFullYear() === now.getFullYear();
  }).length;
});

const formatTanggal = (value) => {
  if (!value) return "-";
  const d = new Date(value);
  if (isNaN(d.getTime())) return value;
  return d.toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
};

// Tanggal cetak
const tglCetak = computed(() =>
  new Date().toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  }),
);

// Cetak rekap surat keluar (ikut filter aktif)
const cetakRekap = () => {
  if (filteredData.value.length === 0) {
    Swal.fire({
      icon: "info",
      title: "Tidak Ada Data",
      text: "Tidak ada surat keluar untuk dicetak sesuai filter saat ini.",
      confirmButtonColor: "#059669",
    });
    return;
  }

  const area = document.getElementById("area-cetak-sk");
  if (!area) {
    window.print();
    return;
  }

  // Simpan posisi asli, pindahkan area cetak ke body
  const parentAsli = area.parentNode;
  const penanda = document.createComment("posisi-area-cetak-sk");
  parentAsli.insertBefore(penanda, area);
  document.body.appendChild(area);
  document.body.classList.add("printing-sk");

  const kembalikan = () => {
    document.body.classList.remove("printing-sk");
    if (penanda.parentNode) {
      penanda.parentNode.insertBefore(area, penanda);
      penanda.remove();
    }
    window.removeEventListener("afterprint", kembalikan);
  };
  window.addEventListener("afterprint", kembalikan);

  window.print();
  // fallback kalau afterprint tak terpicu
  setTimeout(() => {
    if (document.body.classList.contains("printing-sk")) kembalikan();
  }, 1000);
};

// ==========================================
// 2. FUNGSI LOGIKA (Pengganti Script JS Bawah PHP)
// ==========================================

// Fungsi Fitur Pencarian & Filter
const filteredData = computed(() => {
  let result = dataSurat.value;

  if (filterBulan.value) {
    // filterBulan = "MM"; tanggal_surat dari API "YYYY-MM-DD"
    result = result.filter(
      (s) => (s.tanggal_surat || "").slice(5, 7) === filterBulan.value,
    );
  }

  if (filterTahun.value) {
    result = result.filter(
      (s) => (s.tanggal_surat || "").slice(0, 4) === filterTahun.value,
    );
  }

  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    result = result.filter(
      (s) =>
        (s.nomor_surat || "").toLowerCase().includes(q) ||
        (s.tujuan || "").toLowerCase().includes(q) ||
        (s.perihal || "").toLowerCase().includes(q),
    );
  }
  return result;
});

// 🔥 PAGINATION
const currentPage = ref(1);
const perPage = ref(10);

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredData.value.length / perPage.value)),
);
const pagedData = computed(() => {
  const mulai = (currentPage.value - 1) * perPage.value;
  return filteredData.value.slice(mulai, mulai + perPage.value);
});
const infoMulai = computed(() =>
  filteredData.value.length === 0
    ? 0
    : (currentPage.value - 1) * perPage.value + 1,
);
const infoAkhir = computed(() =>
  Math.min(currentPage.value * perPage.value, filteredData.value.length),
);
const gotoPage = (p) => {
  if (p >= 1 && p <= totalPages.value) currentPage.value = p;
};
const nextPage = () => gotoPage(currentPage.value + 1);
const prevPage = () => gotoPage(currentPage.value - 1);

watch([searchQuery, filterBulan, filterTahun], () => {
  currentPage.value = 1;
});
watch(totalPages, (t) => {
  if (currentPage.value > t) currentPage.value = t;
});

const nomorHalaman = computed(() => {
  const total = totalPages.value;
  const cur = currentPage.value;
  const arr = [];
  let mulai = Math.max(1, cur - 2);
  let akhir = Math.min(total, mulai + 4);
  mulai = Math.max(1, akhir - 4);
  for (let i = mulai; i <= akhir; i++) arr.push(i);
  return arr;
});

// Fungsi Modal
const openModal = (row) => {
  selectedRow.value = row;
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

// Fungsi Hapus Surat
const hapusSurat = async (id) => {
  const konfirmasi = await Swal.fire({
    icon: "warning",
    title: "Hapus Surat Keluar?",
    text: "Berkas fisik digital dan rekaman database surat akan dihancurkan total dari server!",
    showCancelButton: true,
    confirmButtonText: "Ya, Hapus",
    cancelButtonText: "Batal",
    confirmButtonColor: "var(--primary)",
    cancelButtonColor: "#64748b",
  });

  if (!konfirmasi.isConfirmed) return;

  try {
    await axios.delete(`${import.meta.env.VITE_API_URL}/api/surat-keluar/${id}`);
    closeModal();
    await fetchSuratKeluar();
    Swal.fire({
      icon: "success",
      title: "Terhapus!",
      text: "Surat keluar berhasil dihapus dari database.",
      confirmButtonColor: "var(--primary)",
      timer: 2500,
      timerProgressBar: true,
    });
  } catch (error) {
    console.error("Gagal menghapus surat keluar:", error);
    Swal.fire({
      icon: "error",
      title: "Gagal Menghapus",
      text: "Terjadi kesalahan saat menghapus data. Cek koneksi server/database.",
      confirmButtonColor: "var(--primary)",
    });
  }
};
</script>

<style scoped>
/* =====================================================================
   CSS ASLI KAMU KITA TANAM LANGSUNG DI SINI BIAR 100% AMAN!
   (Tidak perlu repot-repot ngurus path import lagi)
   ===================================================================== */
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.surat-keluar-body {
  background: linear-gradient(135deg, #fafafa 0%, #d1fae5 100%);
  padding: 20px;
  font-family: "Plus Jakarta Sans", sans-serif;
  color: #0f172a;
  margin: 0;
  min-height: 100vh;
}

.wrapper {
  max-width: 95%;
  margin: 0 auto;
  animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* === HEADER CONTAINER === */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  background-color: #1e293b;
  background-image: radial-gradient(
    rgba(255, 255, 255, 0.08) 1.2px,
    transparent 1.2px
  );
  background-size: 22px 22px;
  padding: 28px 35px;
  border-radius: 20px;
  box-shadow: 0 15px 35px -10px rgba(15, 23, 42, 0.35);
}
.header-text h2 {
  margin: 0;
  color: #ffffff;
  font-size: 28px;
  font-weight: 800;
  display: flex;
  align-items: center;
  gap: 12px;
}
.header-text h2 i {
  color: #34d399;
}
.header-text p {
  margin: 8px 0 0;
  color: #94a3b8;
  font-size: 15px;
  font-weight: 500;
}

.btn-back-white {
  background: rgba(255, 255, 255, 0.08);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.18);
  padding: 10px 20px;
  border-radius: 50px;
  font-weight: 700;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  transition: 0.3s;
}
.btn-back-white:hover {
  background: #ffffff;
  color: #1e293b;
  border-color: #ffffff;
  transform: translateY(-2px);
}

/* === STATS SUMMARY CARDS === */
.summary {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 25px;
  margin-bottom: 35px;
}
.summary-card {
  background: white;
  border-radius: 20px;
  padding: 25px 30px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
  transition: all 0.3s ease;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border: 1px solid #ecfdf5;
}
.summary-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(5, 150, 105, 0.06);
}

.card-rose {
  border-top: 5px solid var(--primary);
}
.card-rose .summary-icon {
  background: #ecfdf5;
  color: var(--primary);
  box-shadow: 0 4px 15px rgba(5, 150, 105, 0.2);
}
.card-rose h2 {
  color: var(--primary);
}

.card-orange {
  border-top: 5px solid #0f766e;
}
.card-orange .summary-icon {
  background: #f0fdfa;
  color: #0f766e;
  box-shadow: 0 4px 15px rgba(15, 118, 110, 0.2);
}
.card-orange h2 {
  color: #0f766e;
}

.card-violet {
  border-top: 5px solid #1e293b;
}
.card-violet .summary-icon {
  background: #f1f5f9;
  color: #1e293b;
  box-shadow: 0 4px 15px rgba(30, 41, 59, 0.2);
}
.card-violet h2 {
  color: #1e293b;
}

.summary-info span {
  display: block;
  font-size: 13px;
  color: #64748b;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.summary-info h2 {
  font-size: 38px;
  font-weight: 800;
  margin: 5px 0 0 0;
  line-height: 1;
}
.summary-icon {
  width: 65px;
  height: 65px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  transition: 0.3s;
}
.summary-card:hover .summary-icon {
  transform: scale(1.1) rotate(-5deg);
}

/* === INTERACTIVE ACTION BAR === */
.action-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  gap: 15px;
  flex-wrap: wrap;
  background: white;
  padding: 15px 25px;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
  border: 1px solid #e2e8f0;
}
.action-left,
.action-right {
  display: flex;
  gap: 12px;
  align-items: center;
}

.btn-primary {
  background: linear-gradient(
    135deg,
    var(--primary) 0%,
    var(--primary-dark) 100%
  );
  color: white;
  padding: 12px 24px;
  border-radius: 50px;
  text-decoration: none;
  font-weight: 700;
  font-size: 14px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
  border: none;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
}
.btn-primary:hover {
  transform: translateY(-2px);
  background: linear-gradient(
    135deg,
    var(--primary-light) 0%,
    var(--primary) 100%
  );
  box-shadow: 0 8px 20px rgba(5, 150, 105, 0.4);
}
.btn-primary:active {
  transform: translateY(1px);
  background: linear-gradient(
    135deg,
    var(--primary-dark) 0%,
    var(--primary-dark) 100%
  );
  box-shadow: none;
}

.btn-print {
  background: #1e293b;
  color: white;
  padding: 12px 20px;
  border-radius: 50px;
  text-decoration: none;
  font-size: 13px;
  font-weight: 700;
  transition: 0.3s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 4px 10px rgba(15, 23, 42, 0.2);
}
.btn-print:hover {
  background: #0f172a;
  transform: translateY(-2px);
}

/* SEARCH INPUT BAR */
.filter-select-sk {
  padding: 12px 18px;
  border: 2px solid #f1f5f9;
  border-radius: 50px;
  font-size: 13px;
  outline: none;
  font-family: inherit;
  font-weight: 600;
  color: #0f172a;
  background: #f8fafc;
  cursor: pointer;
  transition: 0.3s;
}
.filter-select-sk:focus,
.filter-select-sk:hover {
  background: #fff;
  border-color: var(--primary, #10b981);
}
.input-icon {
  position: relative;
}
.input-icon i {
  position: absolute;
  left: 18px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--primary);
  font-size: 15px;
}
.input-icon input {
  padding: 12px 20px 12px 45px;
  border: 2px solid #f1f5f9;
  border-radius: 50px;
  font-size: 13px;
  outline: none;
  font-family: inherit;
  font-weight: 600;
  color: #0f172a;
  background: #f8fafc;
  transition: 0.3s;
}
.input-icon input[type="text"] {
  width: 260px;
}
.input-icon input:focus {
  background: white;
  border-color: var(--primary);
  box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.15);
  width: 290px;
}

/* === DATA TABLE ARCHITECTURE === */
.table-card {
  background: white;
  border-radius: 16px;
  padding: 10px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
  border: 1px solid #e2e8f0;
  margin-bottom: 30px;
  position: relative;
}

.custom-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
}

.custom-table th {
  position: sticky;
  top: 0;
  background-color: #f8fafc;
  color: #0f172a;
  z-index: 999;
  font-weight: 800;
  font-size: 12px;
  text-transform: uppercase;
  padding: 16px 20px;
  text-align: left;
  cursor: pointer;
  user-select: none;
  white-space: nowrap;
  transition: 0.2s;
  box-shadow: inset 0 -2px 0 #e2e8f0;
}
.custom-table th:first-child {
  border-top-left-radius: 12px;
}
.custom-table th:last-child {
  border-top-right-radius: 12px;
}
.custom-table th:hover {
  background: #d1fae5;
}

.sort-icon {
  margin-left: 8px;
  color: #6ee7b7;
  font-size: 12px;
}
.custom-table th.asc .sort-icon,
.custom-table th.desc .sort-icon {
  color: var(--primary);
}

.custom-table td {
  padding: 16px 20px;
  font-size: 14px;
  color: #1e293b;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  line-height: 1.5;
  background: #ffffff;
}
.custom-table tbody tr {
  transition: all 0.2s ease;
}
.custom-table tbody tr:hover td {
  background: #f8fafc;
}
.custom-table tbody tr:last-child td {
  border-bottom: none;
}

.col-no {
  width: 5%;
  text-align: center;
  font-weight: 700;
  color: #64748b !important;
}
.col-nomor {
  width: 18%;
}
.col-tanggal {
  width: 15%;
}
.col-tujuan {
  width: 25%;
}
.col-perihal {
  width: auto;
}
.col-aksi {
  width: 10%;
  text-align: center;
}

/* ACTION CONTROLS */
.action-group {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
}
.btn-aksi {
  width: 38px;
  height: 38px;
  border-radius: 12px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  font-size: 15px;
  transition: 0.2s;
  border: none;
  cursor: pointer;
}
.btn-detail,
.btn-edit {
  background: #ecfdf5;
  color: var(--primary);
}
.btn-detail:hover,
.btn-edit:hover {
  background: var(--primary);
  color: white;
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(5, 150, 105, 0.3);
}

.table-card::-webkit-scrollbar {
  width: 6px;
}
.table-card::-webkit-scrollbar-track {
  background: #f8fafc;
  border-radius: 10px;
}
.table-card::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}
.table-card::-webkit-scrollbar-thumb:hover {
  background: var(--primary);
}

.pagination-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  padding: 15px 20px;
  border-top: 1px solid #e2e8f0;
  background: #ffffff;
  border-bottom-left-radius: 20px;
  border-bottom-right-radius: 20px;
}
.pg-info {
  font-size: 13px;
  color: #64748b;
  font-weight: 600;
}
.pg-controls {
  display: flex;
  gap: 6px;
  align-items: center;
}
.pg-btn {
  min-width: 36px;
  height: 36px;
  padding: 0 10px;
  border: 1px solid #e2e8f0;
  background: #fff;
  color: #475569;
  border-radius: 10px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  transition: 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.pg-btn:hover:not(:disabled) {
  background: var(--primary-soft, #ecfdf5);
  border-color: var(--primary, #059669);
  color: var(--primary-dark, #047857);
}
.pg-btn.active {
  background: var(--primary, #059669);
  border-color: var(--primary, #059669);
  color: #fff;
  box-shadow: 0 4px 10px rgba(5, 150, 105, 0.25);
}
.pg-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

/* === MODAL LAYOUT === */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(15, 23, 42, 0.75);
  backdrop-filter: blur(8px);
  z-index: 100000;
  display: flex;
  justify-content: center;
  align-items: center;
  opacity: 0;
  visibility: hidden;
  transition: 0.3s;
}
.modal-overlay.active {
  opacity: 1;
  visibility: visible;
}
.modal-box-large {
  background: white;
  width: 95%;
  max-width: 1050px;
  height: 88vh;
  border-radius: 24px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
  transform: scale(0.95);
  transition: 0.3s;
}
.modal-overlay.active .modal-box-large {
  transform: scale(1);
}

.modal-header-preview {
  background: #ffffff;
  color: #0f172a;
  padding: 20px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #f1f5f9;
}
.modal-header-preview h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 800;
  display: flex;
  align-items: center;
  gap: 10px;
  color: var(--primary);
}

.btn-close-modal {
  background: #f1f5f9;
  border: none;
  color: #ef4444;
  font-size: 20px;
  width: 35px;
  height: 35px;
  border-radius: 50%;
  cursor: pointer;
  transition: 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}
.btn-close-modal:hover {
  background: #ef4444;
  color: white;
  transform: rotate(90deg);
}

.modal-body-split {
  display: flex;
  flex-grow: 1;
  overflow: hidden;
}
.split-left {
  flex: 1.3;
  background: #f8fafc;
  padding: 25px;
  display: flex;
  flex-direction: column;
  gap: 15px;
  border-right: 1px solid #e2e8f0;
}
.split-right {
  flex: 1;
  background: white;
  padding: 30px;
  overflow-y: auto;
}

.preview-box {
  flex: 1;
  background: white;
  border-radius: 16px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
}
.file-action {
  display: flex;
  gap: 15px;
}

#btnDownload {
  background: linear-gradient(
    135deg,
    var(--primary) 0%,
    var(--primary-dark) 100%
  );
  color: white;
  text-decoration: none;
  font-weight: 700;
  transition: all 0.2s;
}
#btnDownload:hover {
  background: linear-gradient(
    135deg,
    var(--primary-light) 0%,
    var(--primary) 100%
  );
  box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
}

.info-group {
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 1px solid #f1f5f9;
}
.info-group label {
  display: block;
  font-size: 12px;
  font-weight: 800;
  color: #94a3b8;
  text-transform: uppercase;
  margin-bottom: 8px;
  letter-spacing: 0.5px;
}
.info-group p {
  margin: 0;
  font-size: 15px;
  font-weight: 600;
  color: #1e293b;
  line-height: 1.4;
}

.alert-arsip {
  background: #ecfdf5;
  border: 1px dashed var(--primary);
  padding: 15px;
  border-radius: 12px;
  margin-top: 25px;
}
.alert-arsip p {
  margin: 0;
  font-size: 13px;
  color: var(--primary-dark);
  font-weight: 600;
  line-height: 1.5;
}

.btn-hapus {
  background: #fef2f2;
  color: #ef4444;
  border: 1px dashed #6ee7b7;
  padding: 15px;
  border-radius: 12px;
  cursor: pointer;
  font-weight: 700;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: 0.3s;
  width: 100%;
  margin-top: 20px;
}
.btn-hapus:hover {
  background: #ef4444;
  color: white;
  border-style: solid;
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(239, 68, 68, 0.2);
}

/* === MOBILE RESPONSIVE FIX === */
@media (max-width: 1100px) {
  .action-right {
    flex-wrap: wrap;
  }
  .input-icon input[type="text"] {
    flex: 1 1 200px;
    width: auto !important;
  }
}

@media (max-width: 900px) {
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  .header-actions {
    width: 100%;
  }
  .summary {
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
}

@media (max-width: 768px) {
  .surat-keluar-body {
    padding: 95px 15px 20px !important;
    overflow-x: hidden !important;
  }
  .wrapper {
    width: 100% !important;
    max-width: 100vw !important;
    padding: 0 !important;
    box-sizing: border-box !important;
  }
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  .summary {
    grid-template-columns: 1fr;
  }
  .action-bar {
    flex-direction: column;
    align-items: stretch;
    padding: 20px;
    gap: 15px;
  }
  .action-left,
  .action-right {
    flex-direction: column;
    width: 100%;
    gap: 10px;
  }
  .btn-primary,
  .btn-print {
    width: 100%;
    justify-content: center;
    box-sizing: border-box;
  }
  .input-icon {
    width: 100%;
  }
  .input-icon input[type="text"] {
    width: 100% !important;
    box-sizing: border-box;
  }
  .table-card {
    width: 100%;
    box-sizing: border-box;
    padding: 0;
    border-radius: 12px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  .custom-table {
    min-width: 800px;
  }
  .modal-body-split {
    flex-direction: column;
  }
  .split-left {
    min-height: 400px;
  }
}

/* === HP KECIL (font & kotak lebih kecil) === */
@media (max-width: 480px) {
  .surat-keluar-body {
    padding: 90px 12px 20px !important;
  }
  .header-text h2 {
    font-size: 21px !important;
  }
  .header-text p {
    font-size: 12px !important;
  }
  .summary-card {
    padding: 18px 20px !important;
  }
  .summary-info h2 {
    font-size: 30px !important;
  }
  .summary-info span {
    font-size: 11px !important;
  }
  .summary-icon {
    width: 52px !important;
    height: 52px !important;
    font-size: 22px !important;
  }
  .btn-primary,
  .btn-print {
    font-size: 13px !important;
    padding: 12px 18px !important;
  }
  .input-icon input {
    font-size: 13px !important;
  }
  .custom-table th {
    font-size: 11px !important;
    padding: 13px 14px !important;
  }
  .custom-table td {
    font-size: 13px !important;
    padding: 13px 14px !important;
  }
  .modal-box-large {
    height: 92vh;
    border-radius: 18px;
  }
  .modal-header-preview {
    padding: 16px 20px;
  }
  .modal-header-preview h3 {
    font-size: 15px;
  }
  .split-left,
  .split-right {
    padding: 18px;
  }
}

/* === ANIMASI: baris tabel muncul halus === */
@keyframes skRowIn {
  from {
    opacity: 0;
    transform: translateX(-8px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
.custom-table tbody tr {
  animation: skRowIn 0.35s ease forwards;
}
.custom-table tbody tr:hover td {
  background: var(--primary-soft, #ecfdf5) !important;
}

@media (max-width: 360px) {
  .surat-keluar-body {
    padding: 85px 10px 15px !important;
  }
  .header-text h2 {
    font-size: 18px !important;
  }
  .summary-info h2 {
    font-size: 26px !important;
  }
  .pg-btn {
    min-width: 28px;
    height: 28px;
    font-size: 11px;
    padding: 0 6px;
  }
}
</style>

<style>
/* Print: area cetak hanya tampil saat dipindah ke body (lihat fungsi cetakRekap) */

/* ===== STYLING AREA CETAK (global, biar tetap kena saat dipindah ke body) ===== */
.area-cetak-sk {
  display: none;
}
.kop-cetak-sk {
  text-align: center;
  margin-bottom: 14px;
}
.kop-cetak-sk h2 {
  margin: 0;
  font-size: 18px;
}
.kop-cetak-sk p {
  margin: 2px 0;
  font-size: 13px;
}
.kop-cetak-sk h3 {
  margin: 10px 0 6px;
  font-size: 15px;
}
.kop-cetak-sk hr {
  border: none;
  border-top: 2px solid #000;
}
.info-cetak-sk {
  display: flex;
  gap: 20px;
  font-size: 12px;
  margin-bottom: 10px;
}
.tabel-cetak-sk {
  width: 100%;
  border-collapse: collapse;
}
.tabel-cetak-sk th,
.tabel-cetak-sk td {
  border: 1px solid #333;
  padding: 6px 8px;
  font-size: 12px;
  text-align: left;
}
.tabel-cetak-sk th {
  background: #e2e8f0;
  font-weight: 700;
}
.ttd-cetak-sk {
  margin-top: 30px;
  width: 250px;
  margin-left: auto;
  text-align: center;
  font-size: 13px;
}
.ttd-space-sk {
  height: 60px;
}
.ttd-nama-sk {
  border-top: 1px solid #000;
  padding-top: 4px;
  font-weight: 700;
}

@media print {
  @page {
    size: A4 portrait;
    margin: 12mm;
  }

  /* Saat mode cetak aktif, sembunyikan app utama */
  body.printing-sk > #app {
    display: none !important;
  }

  body.printing-sk .area-cetak-sk {
    display: block !important;
  }

  .tabel-cetak-sk {
    width: 100%;
    border-collapse: collapse;
  }
  .tabel-cetak-sk thead {
    display: table-header-group;
  }
  .tabel-cetak-sk tr {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
  }
  .ttd-cetak-sk {
    page-break-inside: avoid !important;
    margin-top: 30px;
  }
}
</style>
