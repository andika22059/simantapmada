<template>
  <div class="vue-list-body">
    <div class="container">
      <div class="page-header no-print">
        <div class="header-text">
          <h2>
            <i class="fa-solid fa-folder-open text-primary"></i> Daftar
            Pengajuan Surat
          </h2>
          <p>Rekapitulasi dan pengelolaan pengajuan surat warga.</p>
        </div>
        <div class="header-actions">
          <button
            @click="router.push('/admin/pelayanan')"
            class="btn-back-outline"
          >
            <i class="fa-solid fa-arrow-left"></i> Kembali
          </button>

          <button @click="bukaModalCetak()" class="btn-print-report">
            <i class="fa-solid fa-print"></i> Cetak Register
          </button>

          <select v-model="statusFilter" class="filter-select">
            <option value="">Semua Status</option>
            <option value="Diajukan">Diajukan</option>
            <option value="Diverifikasi">Diverifikasi</option>
            <option value="Dikembalikan">Dikembalikan</option>
            <option value="Selesai">Selesai</option>
          </select>

          <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Cari nama, jenis, no surat..."
            />
          </div>
        </div>
      </div>

      <div class="table-card no-print">
        <div class="table-responsive">
          <table class="modern-table">
            <thead>
              <tr>
                <th width="5%">No</th>
                <th width="15%">Waktu Pengajuan</th>
                <th width="20%">No. Surat Resmi</th>
                <th width="20%">Nama Pemohon</th>
                <th width="15%">Jenis Surat</th>
                <th width="10%">Status</th>
                <th width="15%" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="paginatedData.length === 0">
                <td colspan="7" class="empty-state">
                  <i class="fa-regular fa-folder-open"></i>
                  <p>Tidak ada data pengajuan surat ditemukan.</p>
                </td>
              </tr>
              <tr v-for="(row, index) in paginatedData" :key="index" :style="{ animationDelay: (index % 12) * 40 + 'ms' }">
                <td>{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
                <td>
                  <div class="date-cell">
                    <i class="fa-regular fa-calendar"></i>
                    {{ row.tanggal }}
                  </div>
                  <div class="time-cell text-muted">{{ row.waktu }}</div>
                </td>
                <td class="font-mono font-bold">{{ row.nomor_surat }}</td>
                <td class="font-semibold">{{ row.nama }}</td>
                <td>
                  <span class="type-badge">{{ row.jenis_surat }}</span>
                </td>
                <td>
                  <span
                    class="status-badge"
                    :class="getStatusClass(row.status)"
                  >
                    {{ row.status }}
                  </span>
                </td>
                <td class="action-cells">
                  <button
                    @click="openPreview(row)"
                    class="btn-action view"
                    title="Lihat Detail Form"
                  >
                    <i class="fa-regular fa-eye"></i>
                  </button>
                  <button
                    @click="editPengajuan(row)"
                    class="btn-action edit"
                    :disabled="row.status !== 'Diajukan'"
                    :title="
                      row.status === 'Diajukan'
                        ? 'Edit Pengajuan'
                        : 'Terkunci — sudah diverifikasi Sekdes'
                    "
                  >
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                  <button
                    @click="kirimWA(row)"
                    class="btn-action wa"
                    title="Hubungi via WhatsApp"
                  >
                    <i class="fa-brands fa-whatsapp"></i>
                  </button>
                  <button
                    v-if="row.status === 'Selesai'"
                    @click="cetakSurat(row)"
                    class="btn-action cetak-surat"
                    title="Cetak Surat Jadi"
                  >
                    <i class="fa-solid fa-print"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="pagination-wrapper no-print">
          <div class="page-info">
            Menampilkan {{ paginatedData.length }} dari
            {{ filteredData.length }} data
          </div>
          <div class="pagination">
            <button @click="prevPage" :disabled="currentPage === 1">
              <i class="fa-solid fa-chevron-left"></i>
            </button>

            <button
              v-for="page in totalPages"
              :key="page"
              @click="setPage(page)"
              :class="{ active: currentPage === page }"
            >
              {{ page }}
            </button>

            <button @click="nextPage" :disabled="currentPage === totalPages">
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>
          <div class="per-page-selector">
            <select v-model="itemsPerPage" class="per-page-select">
              <option :value="5">5 / Halaman</option>
              <option :value="10">10 / Halaman</option>
              <option :value="20">20 / Halaman</option>
              <option :value="50">50 / Halaman</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <Transition name="fade">
      <div
        v-if="isPreviewOpen"
        class="modal-overlay no-print"
        @click.self="closePreview"
      >
        <div class="modal-card pdf-style">
          <div class="modal-header">
            <h3><i class="fa-solid fa-file-lines"></i> Detail Pengajuan</h3>
            <button class="btn-close" @click="closePreview">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
          <div class="modal-body scrollable">
            <!-- Timeline perjalanan status pengajuan -->
            <TimelineStatus :status="selectedRow.status" />

            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">Nama Pemohon</span>
                <span class="detail-value">{{ selectedRow.nama }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Jenis Layanan</span>
                <span class="detail-value">{{ selectedRow.jenis_surat }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Waktu Pengajuan</span>
                <span class="detail-value"
                  >{{ selectedRow.tanggal }} - {{ selectedRow.waktu }}</span
                >
              </div>
              <div class="detail-item">
                <span class="detail-label">Status</span>
                <span
                  class="status-badge"
                  :class="getStatusClass(selectedRow.status)"
                >
                  {{ selectedRow.status }}
                </span>
              </div>
              <div class="detail-item full">
                <span class="detail-label">Nomor WhatsApp</span>
                <span class="detail-value phone-number">{{
                  selectedRow.hp
                }}</span>
              </div>
            </div>

            <div class="file-attachment-section">
              <h4>Lampiran Berkas (Opsional)</h4>

              <div
                v-if="
                  !selectedRow.lampiran || selectedRow.lampiran.length === 0
                "
                class="no-attachment"
              >
                <i class="fa-regular fa-folder-open"></i>
                <span>Tidak ada berkas yang dilampirkan.</span>
              </div>

              <div
                v-else
                v-for="(file, fIndex) in selectedRow.lampiran"
                :key="fIndex"
                class="attachment-box"
              >
                <i class="fa-solid fa-file-pdf"></i>
                <div class="file-info">
                  <span class="filename">{{ getFileName(file) }}</span>
                  <span class="filesize">Berkas dari sistem</span>
                </div>
                <button
                  class="btn-download"
                  @click="downloadPDF(file)"
                  type="button"
                >
                  <i class="fa-solid fa-download"></i> Unduh
                </button>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn-cancel" @click="closePreview">Tutup</button>
          </div>
        </div>
      </div>
    </Transition>
    <CetakLaporan v-model="showCetakModal" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";
import CetakLaporan from "../CetakLaporan.vue";
import TimelineStatus from "../../../components/TimelineStatus.vue";
const router = useRouter();

const showCetakModal = ref(false);

// Base URL backend Laravel (dipakai buat link storage/file)
const API_BASE_URL = `${import.meta.env.VITE_API_URL}`;

// STATE DATA MURNI DARI DATABASE
const dataPelayanan = ref([]);
const isLoading = ref(true);

const searchQuery = ref("");
const statusFilter = ref("");
const currentPage = ref(1);
const itemsPerPage = ref(10);
const isPreviewOpen = ref(false);
const selectedRow = ref({});

// FUNGSI TARIK DATA DARI LARAVEL
const fetchData = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get(`${API_BASE_URL}/api/pelayanan`);

    dataPelayanan.value = response.data.data.map((item, index) => {
      let formData = item.data_pengajuan || {};
      if (typeof formData === "string") {
        try {
          formData = JSON.parse(formData);
        } catch (e) {
          formData = {};
        }
      }

      const detail = formData.detail || formData;
      const pemohon = detail.data_pemohon || formData.data_pemohon || {};

      let tgl = "-";
      let wkt = "-";
      if (item.created_at) {
        const d = new Date(item.created_at);
        tgl = d.toLocaleDateString("id-ID", {
          day: "2-digit",
          month: "short",
          year: "numeric",
        });
        wkt =
          d.toLocaleTimeString("id-ID", {
            hour: "2-digit",
            minute: "2-digit",
          }) + " WIB";
      }

      return {
        id: item.id || `row-${index}`,
        tanggal: tgl,
        waktu: wkt,
        nomor_surat: item.nomor_surat || "-",
        nama:
          pemohon.nama ||
          detail.nama ||
          formData.nama ||
          formData.nama_lengkap ||
          "Nama Tidak Tercatat",
        jenis_surat: item.jenis_surat
          ? item.jenis_surat.toUpperCase()
          : "TIDAK DIKETAHUI",
        hp: pemohon.hp || detail.hp || formData.hp || formData.no_hp || "-",
        status: item.status || "Diajukan",
        lampiran: item.lampiran || [],
      };
    });
  } catch (error) {
    console.error("Gagal narik data dari Laravel:", error);
  } finally {
    await nextTick();
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchData();
});

// FUNGSI ACC UTK UPDATE STATUS & GENERATE NOMOR SURAT PER JENIS
const prosesAccSurat = async (row) => {
  const result = await Swal.fire({
    title: "ACC Surat Ini?",
    text: `Pengajuan ${row.jenis_surat} milik ${row.nama} akan divalidasi dan diberi nomor resmi otomatis.`,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#10b981",
    cancelButtonColor: "#d33",
    confirmButtonText: "Ya, ACC Sekarang!",
    cancelButtonText: "Batal",
  });

  if (result.isConfirmed) {
    Swal.fire({
      title: "Sedang Memproses...",
      text: "Laravel sedang menyusun format nomor urut per jenis surat...",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

    try {
      const response = await axios.put(
        `${API_BASE_URL}/api/pelayanan/${row.id}/status`,
        {
          status: "Selesai",
        },
      );

      await Swal.fire({
        icon: "success",
        title: "Berhasil Terbit!",
        html: `Nomor Surat Resmi Baru:<br><b style="font-size: 16px; color:#10b981;">${response.data.data.nomor_surat}</b>`,
        confirmButtonColor: "#10b981",
      });
      fetchData();
    } catch (error) {
      console.error(error);
      Swal.fire("Proses Gagal!", "Terjadi kesalahan saat ACC.", "error");
    }
  }
};

// LOGIKA FILTER DAN PAGINASI
const filteredData = computed(() => {
  let result = dataPelayanan.value;
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    result = result.filter(
      (row) =>
        row.nama?.toLowerCase().includes(q) ||
        row.jenis_surat?.toLowerCase().includes(q) ||
        row.nomor_surat?.toLowerCase().includes(q),
    );
  }
  if (statusFilter.value) {
    result = result.filter((row) => row.status === statusFilter.value);
  }
  return result;
});

const totalPages = computed(
  () => Math.ceil(filteredData.value.length / itemsPerPage.value) || 1,
);

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredData.value.slice(start, end);
});

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};
const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--;
};
const setPage = (page) => {
  currentPage.value = page;
};

const getStatusClass = (status) => {
  if (status === "Diajukan") return "bg-orange";
  if (status === "Diverifikasi") return "bg-blue";
  if (status === "Dikembalikan") return "bg-red";
  if (status === "Selesai") return "bg-green";
  return "bg-gray";
};

watch([statusFilter, searchQuery, itemsPerPage], () => {
  currentPage.value = 1;
});

const openPreview = (row) => {
  selectedRow.value = row;
  isPreviewOpen.value = true;
};
const closePreview = () => {
  isPreviewOpen.value = false;
};
const downloadPDF = (filePath) => {
  if (!filePath) {
    Swal.fire("Gagal", "Berkas tidak ditemukan.", "error");
    return;
  }
  window.open(`${API_BASE_URL}/storage/${filePath}`, "_blank");
};

const getFileName = (filePath) => {
  if (!filePath) return "Berkas.pdf";
  return filePath.split("/").pop();
};

const kirimWA = (row) => {
  if (!row.hp || row.hp === "-") {
    Swal.fire(
      "Kosong Bang!",
      "Warga ini tidak mencantumkan nomor HP.",
      "warning",
    );
    return;
  }
  const pesan = `Halo Sdr/i *${row.nama}*,\n\nSurat permohonan Anda dengan Nomor *${row.nomor_surat}* telah SELESAI diproses.\n\nSilakan datang ke Kantor Desa untuk mengambil surat fisik.\n\nTerima Kasih,\nPemerintah Desa Macanan.`;
  let nope = row.hp;
  if (nope.startsWith("0")) {
    nope = "62" + nope.slice(1);
  }
  window.open(
    `https://wa.me/${nope}?text=${encodeURIComponent(pesan)}`,
    "_blank",
  );
};

// Cetak surat jadi (buka halaman template surat di tab baru)
const cetakSurat = (row) => {
  if (row.status !== "Selesai") {
    Swal.fire(
      "Belum Bisa",
      "Surat hanya bisa dicetak setelah disetujui Kepala Desa.",
      "info",
    );
    return;
  }
  // Buka di tab yang sama supaya sesi (token) tetap kebawa.
  router.push(`/surat/cetak/${row.id}`);
};

// EDIT pengajuan — hanya boleh selama status masih "Diajukan".
const editPengajuan = (row) => {
  if (row.status !== "Diajukan") {
    Swal.fire(
      "Terkunci",
      "Pengajuan ini sudah diverifikasi Sekdes, jadi tidak bisa diedit lagi.",
      "info",
    );
    return;
  }
  router.push({ path: "/admin/pelayanan/tambah", query: { edit: row.id } });
};
const bukaModalCetak = () => {
  if (filteredData.value.length === 0) {
    Swal.fire("Data Kosong", "Tidak ada data yang bisa dicetak.", "warning");
    return;
  }

  // 1. Susun konfigurasi untuk komponen CetakLaporan
  const konfigurasiCetak = {
    judul: "BUKU REGISTER PELAYANAN SURAT",
    periode: "Tahun 2026",
    kolom: [
      { label: "NO", key: "index", tipe: "index", width: "5%" },
      {
        label: "TANGGAL",
        key: "tanggal",
        subKey: "waktu",
        tipe: "dua_baris",
        width: "15%",
      },
      { label: "NO SURAT RESMI", key: "nomor_surat", width: "22%" },
      { label: "NAMA PEMOHON", key: "nama", width: "23%" },
      { label: "JENIS LAYANAN", key: "jenis_surat", width: "20%" },
      { label: "STATUS", key: "status", tipe: "status", width: "15%" },
    ],
    // Lempar data yang sudah di-filter biar cetak sesuai pencarian di layar
    data: filteredData.value,
  };

  // 2. Simpan ke localStorage (dibaca otomatis oleh CetakLaporan.vue; konsisten antar-tab)
  localStorage.setItem(
    "cetakLaporanConfig",
    JSON.stringify(konfigurasiCetak),
  );

  // 3. Buka Modal
  showCetakModal.value = true;
};
</script>

<style scoped>
/* ==========================================================================
   COMBINED CSS: HEADER (ASLI LU LAMA) + TABEL (BARU ANTI-PENYET)
   ========================================================================== */

.vue-list-body {
  background-color: transparent;
  font-family: inherit;
  color: #333;
  padding: 15px;
}

.container {
  width: 100%;
  max-width: 100%;
  margin: 0;
  padding: 0;
}

/* =========================================
   🔥 1. CSS BOX HEADER KEMBALI 100% PAKAI PUNYA LU (DARK PREMIUM)
   ========================================= */
.page-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
  padding: 30px 40px !important;
  border-radius: 20px !important;
  box-shadow:
    0 20px 25px -5px rgba(0, 0, 0, 0.1),
    0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
  display: flex !important;
  justify-content: space-between !important;
  align-items: center !important;
  margin-bottom: 30px !important;
  border: 1px solid rgba(255, 255, 255, 0.05) !important;
}

.header-text h2 {
  color: #ffffff !important;
  font-size: 24px !important;
  font-weight: 800 !important;
  margin: 0 0 8px 0 !important;
  letter-spacing: -0.5px !important;
}

.header-text p {
  color: #94a3b8 !important;
  margin: 0 !important;
  font-size: 14px !important;
  font-weight: 500 !important;
}

.header-actions {
  display: flex !important;
  gap: 12px !important;
  align-items: center !important;
}

.btn-back-outline {
  background-color: transparent !important;
  border: 2px solid #334155 !important;
  color: #cbd5e1 !important;
  padding: 10px 20px !important;
  border-radius: 12px !important;
  font-size: 14px !important;
  font-weight: 600 !important;
  cursor: pointer !important;
}

.btn-back-outline:hover {
  background-color: rgba(255, 255, 255, 0.05) !important;
  border-color: #64748b !important;
  color: #ffffff !important;
}

.btn-print-report {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
  color: #ffffff !important;
  border: none !important;
  padding: 12px 24px !important;
  border-radius: 12px !important;
  font-size: 14px !important;
  font-weight: 700 !important;
  cursor: pointer !important;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2) !important;
}

.btn-print-report:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3) !important;
}

.filter-select {
  background-color: #1e293b !important;
  border: 2px solid #334155 !important;
  color: #ffffff !important;
  padding: 10px 16px !important;
  border-radius: 12px !important;
  font-size: 14px !important;
  font-weight: 600 !important;
  outline: none !important;
  cursor: pointer !important;
}

.search-box {
  background-color: #1e293b !important;
  border: 2px solid #334155 !important;
  border-radius: 12px !important;
  display: flex !important;
  align-items: center !important;
  padding: 0 16px !important;
  width: 280px !important;
}

.search-box i {
  color: #64748b !important;
}

.search-box input {
  background: transparent !important;
  border: none !important;
  color: #ffffff !important;
  padding: 12px 10px !important;
  width: 100% !important;
  outline: none !important;
  font-size: 14px !important;
}

/* =========================================
   🔥 2. CSS AREA TABEL TETEP BARU (FIX TOTAL ANTI PENYET & JEJER)
   ========================================= */
.table-card {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
  overflow: hidden;
  margin-top: 15px;
}

.table-responsive {
  width: 100%;
  overflow-x: auto;
}

.modern-table {
  width: 100% !important;
  min-width: 950px !important; /* Kunci pertahanan tabel */
  border-collapse: collapse !important;
  white-space: nowrap !important; /* Teks gak boleh meliuk ke bawah */
}

.modern-table th {
  background: #f8fafc !important;
  color: #475569 !important;
  font-size: 12px !important;
  font-weight: 700 !important;
  text-transform: uppercase !important;
  padding: 14px 20px !important;
  border-bottom: 2px solid #e2e8f0 !important;
  text-align: left !important;
}

.modern-table td {
  padding: 14px 20px !important;
  border-bottom: 1px solid #f1f5f9 !important;
  font-size: 13px !important;
  color: #334155 !important;
  vertical-align: middle !important;
}

.modern-table th.text-center,
.modern-table td.action-cells {
  text-align: center !important;
}

.modern-table tbody tr:hover {
  background: #f8fafc !important;
}

.date-cell {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  font-weight: 600 !important;
  color: #1e293b !important;
}

.time-cell {
  font-size: 11px !important;
  color: #64748b !important;
  margin-top: 3px !important;
  padding-left: 20px !important;
}

.font-mono {
  font-family: monospace !important;
  font-size: 14px !important;
}
.font-bold {
  font-weight: bold !important;
}
.font-semibold {
  font-weight: 600 !important;
}

.type-badge {
  background: #f1f5f9 !important;
  color: #475569 !important;
  padding: 4px 8px !important;
  border-radius: 4px !important;
  font-size: 11px !important;
  font-weight: 600 !important;
  border: 1px solid #e2e8f0 !important;
}

.status-badge {
  padding: 5px 12px !important;
  border-radius: 20px !important;
  font-size: 11px !important;
  font-weight: 700 !important;
  display: inline-block !important;
}

.bg-blue {
  background: #eff6ff !important;
  color: #2563eb !important;
}
.bg-orange {
  background: #fff7ed !important;
  color: #ea580c !important;
}
.bg-green {
  background: #f0fdf4 !important;
  color: #16a34a !important;
}
.bg-purple {
  background: #faf5ff !important;
  color: #9333ea !important;
}
.bg-red {
  background: #fef2f2 !important;
  color: #dc2626 !important;
}
.bg-gray {
  background: #f1f5f9 !important;
  color: #64748b !important;
}

/* === BENTENG AKSI TOMBOL (BERJEJER SEJAJAR CO) === */
.action-cells {
  display: flex !important;
  flex-direction: row !important;
  flex-wrap: nowrap !important;
  gap: 8px !important;
  justify-content: center !important;
  align-items: center !important;
}

.btn-action {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  width: 32px !important;
  height: 32px !important;
  flex: 0 0 32px !important;
  border-radius: 6px !important;
  border: none !important;
  cursor: pointer !important;
  font-size: 13px !important;
  padding: 0 !important;
  margin: 0 !important;
  transition: 0.2s !important;
}

.btn-action.view {
  background: #eff6ff !important;
  color: #2563eb !important;
}
.btn-action.view:hover {
  background: #2563eb !important;
  color: #ffffff !important;
}

.btn-action.edit {
  background: #fff7ed !important;
  color: #ea580c !important;
}
.btn-action.edit:hover:not(:disabled) {
  background: #ea580c !important;
  color: #ffffff !important;
}
.btn-action:disabled {
  opacity: 0.4 !important;
  cursor: not-allowed !important;
}

.btn-action.wa {
  background: #f0fdf4 !important;
  color: #16a34a !important;
}
.btn-action.wa:hover {
  background: #16a34a !important;
  color: #ffffff !important;
}

.btn-action.acc {
  background: #fdf2f8 !important;
  color: #db2777 !important;
}
.btn-action.acc:hover {
  background: #db2777 !important;
  color: #ffffff !important;
}
.btn-action.cetak-surat {
  background: #ecfdf5 !important;
  color: #059669 !important;
}
.btn-action.cetak-surat:hover {
  background: #059669 !important;
  color: #ffffff !important;
}

/* === PAGINATION & MODAL & PRINT === */
.pagination-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  background: #ffffff;
  border-top: 1px solid #e2e8f0;
}
.page-info {
  font-size: 13px;
  color: #64748b;
}
.pagination {
  display: flex;
  gap: 4px;
}
.pagination button {
  min-width: 32px;
  height: 32px;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  color: #475569;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}
.pagination button.active {
  background: #1e293b;
  color: #ffffff;
  border-color: #1e293b;
}
.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.per-page-select {
  padding: 6px 10px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 12px;
  color: #475569;
  background: #ffffff;
}

.empty-state {
  text-align: center;
  padding: 40px !important;
  color: #94a3b8 !important;
}
.empty-state i {
  font-size: 30px;
  margin-bottom: 10px;
  color: #cbd5e1;
}
.empty-state p {
  margin: 0;
  font-size: 13px;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
}
.modal-card.pdf-style {
  background: #ffffff;
  width: 90%;
  max-width: 600px;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  display: flex;
  flex-direction: column;
  max-height: 85vh;
}
.modal-header {
  padding: 15px 20px;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.modal-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: bold;
  color: #1e293b;
}
.btn-close {
  background: transparent;
  border: none;
  font-size: 16px;
  cursor: pointer;
  color: #64748b;
}
.modal-body.scrollable {
  padding: 20px;
  overflow-y: auto;
}
.detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
  margin-bottom: 20px;
}
.detail-item.full {
  grid-column: span 2;
}
.detail-label {
  display: block;
  font-size: 11px;
  color: #64748b;
  text-transform: uppercase;
  font-weight: bold;
  margin-bottom: 4px;
}
.detail-value {
  display: block;
  font-size: 14px;
  color: #1e293b;
  font-weight: 500;
}
.file-attachment-section h4 {
  font-size: 13px;
  margin-bottom: 10px;
  color: #333;
}
.attachment-box {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px;
  border: 1px dashed #cbd5e1;
  border-radius: 8px;
  background: #f8fafc;
  margin-bottom: 8px;
}
.attachment-box i.fa-file-pdf {
  font-size: 22px;
  color: #ef4444;
  flex-shrink: 0;
}
.file-info {
  display: flex;
  flex-direction: column;
  flex: 1;
  min-width: 0;
  overflow: hidden;
}
.file-info .filename {
  font-size: 13px;
  font-weight: 600;
  color: #1e293b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.file-info .filesize {
  font-size: 11px;
  color: #94a3b8;
  margin-top: 2px;
}
.btn-download {
  flex-shrink: 0;
  padding: 8px 14px;
  border-radius: 6px;
  border: none;
  background: #1e293b;
  color: #ffffff;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  white-space: nowrap;
  transition: 0.2s;
}
.btn-download:hover {
  background: #0f172a;
}
.no-attachment {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 14px;
  border-radius: 8px;
  background: #f8fafc;
  color: #94a3b8;
  font-size: 13px;
}
.modal-footer {
  padding: 15px 20px;
  border-top: 1px solid #e2e8f0;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}
.btn-cancel {
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  background: #f1f5f9;
  color: #475569;
}

/* Animasi Muncul dari Bawah */
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-up {
  animation: slideUp 0.6s cubic-bezier(0.22, 1, 0.36, 1);
}

/* Animasi Loading Bar */
@keyframes pulse {
  0% {
    opacity: 0.6;
  }
  50% {
    opacity: 0.3;
  }
  100% {
    opacity: 0.6;
  }
}

.animate-pulse {
  animation: pulse 1.5s infinite;
  background: #f1f5f9;
  height: 50px;
}

/* Animasi Baris Tabel Hover */
.animate-row {
  transition: all 0.3s ease;
}

.animate-row:hover {
  background-color: #f1f5f9 !important;
  transform: scale(1.01);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  cursor: pointer;
} /* --- SUNTIKAN CSS UNTUK SWEETALERT ELEGANT --- */

/* Background Overlay yang Blur */
.swal2-container.swal2-backdrop-show {
  background: rgba(15, 23, 42, 0.4) !important;
  backdrop-filter: blur(8px) !important;
}

/* Kotak Pop-up Glassmorphism */
.swal2-popup.swal2-modal {
  background: rgba(255, 255, 255, 0.95) !important;
  border: 1px solid rgba(255, 255, 255, 0.3) !important;
  border-radius: 20px !important;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
  padding: 20px !important;
}

/* Judul Lebih Elegan */
.swal2-title {
  color: #1e293b !important;
  font-family: "Plus Jakarta Sans", sans-serif !important;
  font-size: 20px !important;
  font-weight: 800 !important;
}

/* Pesan/Teks */
.swal2-html-container {
  color: #475569 !important;
  font-family: "Plus Jakarta Sans", sans-serif !important;
  font-size: 14px !important;
}

/* Tombol Jadi Bulat & Modern */
.swal2-confirm.swal2-styled {
  background: #1e293b !important;
  border-radius: 10px !important;
  padding: 10px 25px !important;
  font-weight: 700 !important;
  box-shadow: none !important;
  transition: 0.3s !important;
}

.swal2-confirm.swal2-styled:hover {
  background: #0f172a !important;
  transform: translateY(-2px) !important;
}

.swal2-cancel.swal2-styled {
  border-radius: 10px !important;
  background: #f1f5f9 !important;
  color: #64748b !important;
  font-weight: 600 !important;
  box-shadow: none !important;
}

/* ===================== RESPONSIVE HP ===================== */
@media (max-width: 1100px) {
  .header-actions {
    flex-wrap: wrap !important;
    justify-content: flex-start !important;
  }
  .search-box {
    width: auto !important;
    flex: 1 1 200px !important;
    min-width: 160px !important;
  }
}

@media (max-width: 900px) {
  .page-header {
    flex-direction: column;
    align-items: stretch;
    gap: 16px;
  }
  .header-actions {
    flex-wrap: wrap;
    gap: 10px;
  }
  .search-box {
    flex: 1 1 100% !important;
    width: 100% !important;
    min-width: 0 !important;
  }
}

@media (max-width: 768px) {
  .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  .modern-table {
    min-width: 760px;
  }
  .header-text h2 {
    font-size: 20px !important;
  }
  .header-text p {
    font-size: 12px !important;
  }
  .btn-back-outline,
  .btn-print-report,
  .filter-select {
    font-size: 13px !important;
    padding: 10px 14px !important;
  }
}

@media (max-width: 480px) {
  .header-actions {
    flex-direction: column;
    align-items: stretch;
  }
  .btn-back-outline,
  .btn-print-report,
  .filter-select,
  .search-box {
    width: 100%;
    box-sizing: border-box;
  }
  .header-text h2 {
    font-size: 18px !important;
  }
  .pagination-wrapper {
    flex-direction: column;
    gap: 10px;
    padding: 12px 15px;
  }
  .page-info {
    font-size: 12px;
    text-align: center;
  }
  .pagination button {
    min-width: 32px;
    height: 32px;
    font-size: 12px;
  }
  .per-page-selector {
    width: 100%;
  }
  .per-page-select {
    width: 100%;
  }
}

@media (max-width: 360px) {
  .header-text h2 {
    font-size: 16px !important;
  }
  .pagination button {
    min-width: 28px;
    height: 28px;
    font-size: 11px;
    padding: 0 6px;
  }
}

/* Baris tabel muncul bertahap (stagger) */
tbody tr[style*="animation-delay"],
tbody tr[style*="animationDelay"] {
  opacity: 0;
  animation: rowFadeIn 0.4s ease forwards;
}
@keyframes rowFadeIn {
  from { opacity: 0; transform: translateX(-10px); }
  to { opacity: 1; transform: translateX(0); }
}
@media (prefers-reduced-motion: reduce) {
  tbody tr[style*="animation-delay"],
  tbody tr[style*="animationDelay"] { opacity: 1; animation: none; }
}
</style>
