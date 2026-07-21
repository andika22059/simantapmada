<template>
  <div class="vue-list-body">
    <div class="container">
      <div class="page-header no-print">
        <div class="header-text">
          <h2><i class="fa-solid fa-inbox text-emerald"></i> Surat Masuk</h2>
          <p>Pengelolaan dan pencatatan arsip surat masuk instansi desa.</p>
        </div>
        <div class="header-actions">
          <button class="btn-back-outline" @click="goBack">
            <i class="fa-solid fa-arrow-left"></i> Kembali
          </button>
          <button class="btn-print-report" @click="goToTambah">
            <i class="fa-solid fa-plus"></i> Tambah Surat Masuk
          </button>
        </div>
      </div>

      <div class="summary-container no-print">
        <div class="summary-card card-blue">
          <div class="summary-info">
            <span>Total Arsip</span>
            <h2>{{ total_surat }}</h2>
          </div>
          <div class="summary-icon">
            <i class="fa-solid fa-folder-open"></i>
          </div>
        </div>
        <div class="summary-card card-emerald">
          <div class="summary-info">
            <span>Masuk Bulan Ini</span>
            <h2>{{ bulan_ini }}</h2>
          </div>
          <div class="summary-icon">
            <i class="fa-solid fa-calendar-days"></i>
          </div>
        </div>
        <div class="summary-card card-purple">
          <div class="summary-info">
            <span>Masuk Tahun Ini</span>
            <h2>{{ tahun_ini }}</h2>
          </div>
          <div class="summary-icon">
            <i class="fa-solid fa-calendar-check"></i>
          </div>
        </div>
      </div>

      <div class="table-card no-print">
        <div class="table-header-actions">
          <div class="filter-group">
            <select v-model="filterBulan" class="filter-select">
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

            <select v-model="filterTahun" class="filter-select">
              <option value="">-- Semua Tahun --</option>
              <option value="2024">2024</option>
              <option value="2025">2025</option>
              <option value="2026">2026</option>
              <option value="2027">2027</option>
            </select>

            <button @click="cetakLaporan" class="btn-cetak">
              <i class="fa-solid fa-print"></i> Cetak Rekap
            </button>
          </div>

          <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Cari no surat, pengirim, perihal..."
            />
          </div>
        </div>

        <div class="table-responsive">
          <table class="modern-table">
            <thead>
              <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal Surat</th>
                <th width="20%">No. Surat</th>
                <th width="20%">Pengirim</th>
                <th width="25%">Perihal</th>
                <th width="15%" class="text-center">Arsip</th>
              </tr>
            </thead>
            <tbody v-if="isLoading">
              <tr>
                <td colspan="6" class="text-center" style="padding: 30px">
                  Memuat data dari database...
                </td>
              </tr>
            </tbody>
            <tbody v-else>
              <tr v-if="paginatedData.length === 0">
                <td colspan="6" class="empty-state">
                  <i
                    class="fa-solid fa-folder-open"
                    style="font-size: 40px; color: #cbd5e1; margin-bottom: 10px"
                  ></i
                  ><br />
                  Tidak ada data surat yang sesuai filter.
                </td>
              </tr>
              <tr v-for="(row, index) in paginatedData" :key="row.id || index" :style="{ animationDelay: (index % 12) * 40 + 'ms' }">
                <td>{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
                <td class="font-semibold">
                  {{ formatDate(row.tanggal_surat) }}
                </td>
                <td class="font-bold font-mono">{{ row.no_surat }}</td>
                <td>{{ row.pengirim }}</td>
                <td>{{ row.perihal }}</td>
                <td class="action-cells">
                  <button
                    @click="openPreview(row)"
                    class="btn-action view"
                    title="Lihat Berkas PDF"
                  >
                    <i class="fa-solid fa-file-pdf"></i> Lihat File
                  </button>
                  <button
                    v-if="row.status_disposisi === 'Belum'"
                    @click="editSurat(row)"
                    class="btn-action edit"
                    title="Edit Surat (belum didisposisi)"
                  >
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

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
              @click="setPage(p)"
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

    <div
      v-if="isPreviewOpen"
      class="modal-overlay no-print"
      @click.self="closePreview"
    >
      <div class="modal-card">
        <div class="modal-header">
          <h3>
            <i class="fa-solid fa-eye" style="color: #10b981"></i> Live Preview
            Dokumen
          </h3>
          <button class="btn-close" @click="closePreview">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
        <div class="modal-body modal-body-split">
          <div class="preview-col">
            <iframe
              v-if="selectedSurat && selectedSurat.fileUrl"
              :src="selectedSurat.fileUrl"
              class="preview-iframe"
            ></iframe>
            <div v-else class="empty-state" style="padding: 100px 0">
              <i
                class="fa-solid fa-file-circle-xmark"
                style="font-size: 50px; color: #cbd5e1"
              ></i>
              <p style="margin-top: 15px">File tidak ditemukan di server!</p>
            </div>
          </div>

          <!-- TIMELINE TRACKING DISPOSISI -->
          <div class="track-col">
            <div class="track-title">
              <i class="fa-solid fa-route"></i> Lacak Surat
            </div>
            <div class="track-status-badge" :class="badgeStatusClass">
              {{ selectedSurat?.status_disposisi || "Belum" }}
            </div>
            <div class="timeline">
              <div
                v-for="(step, i) in trackingSteps"
                :key="i"
                class="tl-item"
                :class="{ done: step.done, pending: !step.done }"
              >
                <div class="tl-marker">
                  <i
                    :class="
                      step.done ? 'fa-solid fa-check' : 'fa-regular fa-clock'
                    "
                  ></i>
                </div>
                <div class="tl-content">
                  <div class="tl-judul">{{ step.judul }}</div>
                  <div class="tl-oleh">
                    <i class="fa-solid fa-user-tie"></i> {{ step.oleh }}
                  </div>
                  <div v-if="step.waktu" class="tl-waktu">
                    <i class="fa-regular fa-clock"></i> {{ step.waktu }}
                  </div>
                  <div class="tl-info">{{ step.info }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const router = useRouter();

// STATE
const suratList = ref([]);
const isLoading = ref(true);
const searchQuery = ref("");
const currentPage = ref(1);
const itemsPerPage = ref(10);
const isPreviewOpen = ref(false);
const selectedSurat = ref(null);

// 🔥 STATE FILTER
const filterBulan = ref("");
const filterTahun = ref("");

// FUNGSI NAVIGASI
const goBack = () => {
  router.push("/admin");
};
const goToTambah = () => {
  router.push("/admin/surat-masuk/tambah");
};

// Edit surat masuk (hanya yang belum didisposisi)
const editSurat = (row) => {
  if (row.status_disposisi !== "Belum") return;
  router.push(`/admin/surat-masuk/edit/${row.id}`);
};

// FUNGSI CETAK
// Cetak rekap surat masuk langsung via jendela cetak (self-contained),
// tanpa route /cetak-laporan terpisah. Ikut filter yang aktif.
const cetakLaporan = () => {
  const rows = filteredData.value || [];
  if (rows.length === 0) {
    Swal.fire({ icon: "info", title: "Tidak Ada Data", text: "Tidak ada surat masuk yang sesuai filter saat ini.", confirmButtonColor: "#059669" });
    return;
  }
  const esc = (s) => (s ?? "-").toString().replace(/</g, "&lt;");
  const tgl = (iso) =>
    iso
      ? new Date(iso).toLocaleDateString("id-ID", {
          day: "numeric",
          month: "short",
          year: "numeric",
        })
      : "-";
  const baris = rows
    .map(
      (r, i) => `
      <tr>
        <td style="text-align:center">${i + 1}</td>
        <td>${esc(r.no_surat)}<br/><small>${tgl(r.tanggal_surat)}</small></td>
        <td>${tgl(r.tanggal_diterima || r.created_at)}</td>
        <td>${esc(r.pengirim)}</td>
        <td>${esc(r.perihal)}</td>
      </tr>`,
    )
    .join("");
  const tglCetak = new Date().toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
  const inner = `
    <div class="kop">
      <h3>PEMERINTAH KABUPATEN NGANJUK</h3>
      <h3>KECAMATAN LOCERET</h3>
      <h2>DESA MACANAN</h2>
      <p>Jl. Panglima Sudirman No. 15, Kode Pos 64471</p>
    </div>
    <hr class="garis" />
    <h4 class="judul">LAPORAN REKAPITULASI SURAT MASUK<br /><small>Periode: ${periodeLabel.value}</small></h4>
    <table>
      <thead>
        <tr><th style="width:6%">No</th><th style="width:22%">Nomor &amp; Tgl Surat</th><th style="width:14%">Tgl Diterima</th><th style="width:22%">Pengirim</th><th>Perihal</th></tr>
      </thead>
      <tbody>${baris}</tbody>
    </table>
    <p class="total">Total: ${rows.length} surat masuk</p>
    <div class="ttd">
      <p>Macanan, ${tglCetak}<br/>Sekretaris Desa</p>
      <div class="ttd-space"></div>
      <p class="nama">(............................)</p>
    </div>`;
  const w = window.open("", "_blank", "width=1000,height=720");
  if (!w) {
    Swal.fire({ icon: "warning", title: "Popup Diblokir", text: "Izinkan popup untuk mencetak laporan ini.", confirmButtonColor: "#059669" });
    return;
  }
  w.document.write(`<!DOCTYPE html><html lang="id"><head><meta charset="utf-8" />
    <title>Laporan Surat Masuk</title>
    <style>
      * { box-sizing: border-box; }
      body { font-family: "Times New Roman", serif; color: #000; margin: 25px 30px; }
      .kop { text-align: center; line-height: 1.25; }
      .kop h3 { margin: 0; font-size: 14pt; }
      .kop h2 { margin: 2px 0; font-size: 18pt; letter-spacing: 1px; }
      .kop p { margin: 2px 0 0; font-size: 10pt; }
      .garis { border: none; border-top: 3px double #000; margin: 6px 0 14px; }
      .judul { text-align: center; margin: 0 0 14px; font-size: 13pt; text-decoration: underline; }
      .judul small { font-size: 10pt; font-weight: normal; }
      table { width: 100%; border-collapse: collapse; font-size: 11pt; }
      th, td { border: 1px solid #000; padding: 6px 8px; vertical-align: top; }
      thead th { background: #e5e7eb; text-align: center; }
      .total { margin: 12px 0 0; font-weight: bold; }
      .ttd { width: 280px; margin: 30px 0 0 auto; text-align: center; }
      .ttd-space { height: 70px; }
      .ttd .nama { font-weight: bold; text-decoration: underline; }
      @page { size: A4 landscape; margin: 15mm; }
    </style></head>
    <body onload="window.focus(); window.print();">${inner}
    <scr` + `ipt>window.onafterprint = function(){ window.close(); };</scr` + `ipt>
    </body></html>`);
  w.document.close();
};

const getNamaBulan = (angka) => {
  const bulan = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
  ];
  return bulan[parseInt(angka) - 1] || "";
};

// 🔥 LABEL PERIODE UNTUK JUDUL CETAK
const periodeLabel = computed(() => {
  if (!filterBulan.value && !filterTahun.value) return "Semua Periode";
  const bulanText = filterBulan.value
    ? getNamaBulan(filterBulan.value)
    : "Semua Bulan";
  const tahunText = filterTahun.value ? filterTahun.value : "Semua Tahun";
  return `${bulanText} ${tahunText}`;
});

// FUNGSI TARIK DATA
const fetchSurat = async () => {
  isLoading.value = true;
  try {
    const res = await axios.get(`${import.meta.env.VITE_API_URL}/api/surat-masuk`);
    if (res.data && res.data.data) {
      suratList.value = res.data.data;
    } else {
      suratList.value = [];
    }
  } catch (error) {
    console.error("Gagal tarik data:", error);
    suratList.value = [];
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchSurat();
});

// FUNGSI TANGGAL AMAN
const formatDate = (dateString) => {
  if (!dateString) return "-";
  return new Date(dateString).toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
};

// HITUNGAN OTOMATIS AMAN
const total_surat = computed(() => suratList.value.length);

const bulan_ini = computed(() => {
  const thisMonth = new Date().getMonth();
  const thisYear = new Date().getFullYear();
  return suratList.value.filter((s) => {
    if (!s.tanggal_surat && !s.created_at) return false;
    const d = new Date(s.tanggal_surat || s.created_at);
    return d.getMonth() === thisMonth && d.getFullYear() === thisYear;
  }).length;
});

const tahun_ini = computed(() => {
  const thisYear = new Date().getFullYear();
  return suratList.value.filter((s) => {
    if (!s.tanggal_surat && !s.created_at) return false;
    const d = new Date(s.tanggal_surat || s.created_at);
    return d.getFullYear() === thisYear;
  }).length;
});

// 🔥 FILTER AMAN (Search + Bulan + Tahun)
const filteredData = computed(() => {
  let result = suratList.value;

  // 1. Proses Filter Pencarian Teks
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    result = result.filter((row) => {
      const no_surat = row.no_surat ? row.no_surat.toLowerCase() : "";
      const pengirim = row.pengirim ? row.pengirim.toLowerCase() : "";
      const perihal = row.perihal ? row.perihal.toLowerCase() : "";
      return (
        no_surat.includes(q) || pengirim.includes(q) || perihal.includes(q)
      );
    });
  }

  // 2. Proses Filter Bulan & Tahun
  if (filterBulan.value || filterTahun.value) {
    result = result.filter((row) => {
      if (!row.tanggal_surat) return false;
      const date = new Date(row.tanggal_surat);
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const year = String(date.getFullYear());

      let match = true;
      if (filterBulan.value && month !== filterBulan.value) match = false;
      if (filterTahun.value && year !== filterTahun.value) match = false;
      return match;
    });
  }

  return result;
});

const totalPages = computed(
  () => Math.ceil(filteredData.value.length / itemsPerPage.value) || 1,
);
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredData.value.slice(start, start + itemsPerPage.value);
});

// Nomor halaman ditampilkan maksimal 5 (gaya seragam)
const nomorHalaman = computed(() => {
  const total = totalPages.value;
  const now = currentPage.value;
  let start = Math.max(1, now - 2);
  let end = Math.min(total, start + 4);
  start = Math.max(1, end - 4);
  const arr = [];
  for (let i = start; i <= end; i++) arr.push(i);
  return arr;
});
const infoMulai = computed(() =>
  filteredData.value.length === 0
    ? 0
    : (currentPage.value - 1) * itemsPerPage.value + 1,
);
const infoAkhir = computed(() =>
  Math.min(currentPage.value * itemsPerPage.value, filteredData.value.length),
);

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};
const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--;
};
const setPage = (page) => {
  currentPage.value = page;
};

// MODAL PDF
const openPreview = (row) => {
  selectedSurat.value = {
    ...row,
    fileUrl: row.file_surat
      ? `${import.meta.env.VITE_API_URL}/storage/${row.file_surat}`
      : null,
  };
  isPreviewOpen.value = true;
};

// === TRACKING DISPOSISI (timeline, berhenti di tahap terakhir yang dicapai) ===
const formatWaktu = (iso) => {
  if (!iso) return "";
  const d = new Date(iso);
  if (isNaN(d)) return "";
  const tgl = d.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
  const jam = d.toLocaleTimeString("id-ID", {
    hour: "2-digit",
    minute: "2-digit",
  });
  return `${tgl} • ${jam} WIB`;
};

const trackingSteps = computed(() => {
  const s = selectedSurat.value;
  if (!s) return [];

  const steps = [];

  // Tahap 1: Surat Masuk (selalu tercapai)
  steps.push({
    judul: "Surat Diterima",
    oleh: "Admin / Operator Desa",
    waktu: formatWaktu(s.tanggal_terima || s.created_at),
    info: `Surat dari ${s.pengirim || "-"} diinput ke sistem`,
    done: true,
  });

  // Tahap 2: Disposisi Sekdes
  const sudahDisposisi =
    s.status_disposisi === "Didisposisi" || s.status_disposisi === "Selesai";
  steps.push({
    judul: "Disposisi Sekretaris Desa",
    oleh: s.didisposisi_oleh || "Sekretaris Desa",
    waktu: formatWaktu(s.tgl_disposisi),
    info: sudahDisposisi
      ? `Diteruskan ke: ${s.disposisi_ke || "-"}${
          s.instruksi ? ` — "${s.instruksi}"` : ""
        }${s.perlu_atensi_kades ? " (perlu atensi Kepala Desa)" : ""}`
      : "Menunggu disposisi dari Sekretaris Desa",
    done: sudahDisposisi,
  });

  // Tahap 3: Atensi Kades (hanya kalau perlu atensi)
  if (s.perlu_atensi_kades) {
    const sudahAtensi = s.status_disposisi === "Selesai" && s.tgl_atensi;
    steps.push({
      judul: "Atensi & Tindak Lanjut Kepala Desa",
      oleh: "Kepala Desa",
      waktu: formatWaktu(s.tgl_atensi),
      info: sudahAtensi
        ? `Diteruskan ke: ${s.diteruskan_ke || "-"}${
            s.arahan_kades ? ` — "${s.arahan_kades}"` : ""
          }`
        : "Menunggu tindak lanjut Kepala Desa",
      done: sudahAtensi,
    });
  }

  return steps;
});

const closePreview = () => {
  isPreviewOpen.value = false;
  selectedSurat.value = null;
};

const badgeStatusClass = computed(() => {
  const s = selectedSurat.value?.status_disposisi;
  if (s === "Selesai") return "badge-selesai";
  if (s === "Didisposisi") return "badge-proses";
  return "badge-belum";
});
</script>

<style scoped>
/* ==========================================================================
   CSS SUPER SULTAN: SURAT MASUK DASHBOARD (FIX LAYOUT & TOMBOL PREMIUM)
   ========================================================================== */
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.vue-list-body {
  background-color: transparent;
  font-family: "Plus Jakarta Sans", sans-serif;
  color: #1e293b;
  padding: 10px;
}

.container {
  width: 100%;
  max-width: 100%;
  margin: 0;
  padding: 0;
}

/* === 1. TOP PAGE HEADER BAR (DARK EXECUTIVE SLATE) === */
.page-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
  padding: 24px 35px !important;
  border-radius: 16px !important;
  box-shadow:
    0 12px 24px -4px rgba(15, 23, 42, 0.12),
    0 4px 12px -2px rgba(15, 23, 42, 0.04) !important;
  display: flex !important;
  justify-content: space-between !important;
  align-items: center !important;
  margin-bottom: 25px !important;
  border: 1px solid rgba(255, 255, 255, 0.06) !important;
}

.header-text h2 {
  color: #ffffff !important;
  font-size: 24px !important;
  font-weight: 800 !important;
  margin: 0 0 6px 0 !important;
  letter-spacing: -0.5px !important;
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
}

.header-text h2 i {
  color: #10b981 !important;
  text-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
}

.header-text p {
  color: #94a3b8 !important;
  margin: 0 !important;
  font-size: 13px !important;
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
  padding: 10px 18px !important;
  border-radius: 10px !important;
  font-size: 13px !important;
  font-weight: 600 !important;
  cursor: pointer !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 8px !important;
  transition: all 0.25s ease !important;
}

.btn-back-outline:hover {
  background-color: rgba(255, 255, 255, 0.05) !important;
  border-color: #64748b !important;
  color: #ffffff !important;
  transform: translateY(-1px);
}

.btn-print-report {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
  color: #ffffff !important;
  border: none !important;
  padding: 11px 22px !important;
  border-radius: 10px !important;
  font-size: 13px !important;
  font-weight: 700 !important;
  cursor: pointer !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 8px !important;
  box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3) !important;
  transition: all 0.25s ease !important;
}

.btn-print-report:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 6px 20px rgba(16, 185, 129, 0.45) !important;
}

/* === 2. METRIC SUMMARY CARDS === */
.summary-container {
  display: grid !important;
  grid-template-columns: repeat(3, 1fr) !important;
  gap: 20px !important;
  margin-bottom: 25px !important;
}

.summary-card {
  padding: 24px 28px !important;
  border-radius: 14px !important;
  display: flex !important;
  justify-content: space-between !important;
  align-items: center !important;
  color: #ffffff !important;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
  position: relative !important;
  overflow: hidden !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.summary-card:hover {
  transform: translateY(-4px) !important;
  box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.1) !important;
}

.summary-card.card-blue {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
}
.summary-card.card-emerald {
  background: linear-gradient(135deg, #10b981 0%, #047857 100%) !important;
}
.summary-card.card-purple {
  background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%) !important;
}

.summary-info span {
  font-size: 13px !important;
  font-weight: 600 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.05em !important;
  opacity: 0.85 !important;
  display: block !important;
  margin-bottom: 4px !important;
}

.summary-info h2 {
  font-size: 36px !important;
  font-weight: 800 !important;
  margin: 0 !important;
  letter-spacing: -1px !important;
}

.summary-icon {
  font-size: 48px !important;
  opacity: 0.22 !important;
  transition: all 0.3s ease !important;
}

.summary-card:hover .summary-icon {
  transform: scale(1.1) rotate(5deg) !important;
  opacity: 0.35 !important;
}

.table-card {
  background: #ffffff !important;
  border-radius: 14px !important;
  box-shadow:
    0 4px 6px -1px rgba(0, 0, 0, 0.03),
    0 2px 4px -1px rgba(0, 0, 0, 0.02) !important;
  border: 1px solid #e2e8f0 !important;
  overflow: hidden !important;
}

.table-header-actions {
  padding: 18px 24px !important;
  border-bottom: 1px solid #f1f5f9 !important;
  display: flex !important;
  justify-content: space-between !important;
  align-items: center !important;
  background-color: #fafafa !important;
  gap: 20px !important;
}

.filter-group {
  display: flex !important;
  gap: 12px !important;
  align-items: center !important;
}

.filter-select {
  background: #ffffff !important;
  border: 1.5px solid #cbd5e1 !important;
  color: #334155 !important;
  padding: 10px 16px !important;
  border-radius: 10px !important;
  font-size: 13px !important;
  font-weight: 600 !important;
  outline: none !important;
  cursor: pointer !important;
  transition: all 0.2s ease !important;
}

.filter-select:focus {
  border-color: #10b981 !important;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
}

/* 🔥 CSS TOMBOL CETAK REKAP SULTAN (EMERALD GRADIENT GLOW) */
.btn-cetak {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
  color: #ffffff !important;
  border: none !important;
  padding: 11px 22px !important;
  border-radius: 10px !important;
  font-size: 13px !important;
  font-weight: 700 !important;
  cursor: pointer !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 8px !important;
  box-shadow: 0 4px 14px rgba(16, 185, 129, 0.2) !important;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.btn-cetak:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4) !important;
}

.btn-cetak:active {
  transform: translateY(0) !important;
}

/* Search Box Kanan */
.search-box {
  display: flex !important;
  align-items: center !important;
  background: #ffffff !important;
  border: 1.5px solid #cbd5e1 !important;
  padding: 0 14px !important;
  border-radius: 10px !important;
  width: 320px !important;
  transition: all 0.2s ease !important;
}

.search-box:focus-within {
  border-color: #10b981 !important;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
}

.search-box i {
  color: #94a3b8 !important;
  font-size: 14px !important;
}

.search-box input {
  border: none !important;
  background: transparent !important;
  width: 100% !important;
  padding: 11px 8px !important;
  outline: none !important;
  font-size: 13px !important;
  font-weight: 500 !important;
  color: #1e293b !important;
}

/* === 4. MODERN TABLE VIEW === */
.table-responsive {
  width: 100% !important;
  overflow-x: auto !important;
}

.modern-table {
  width: 100% !important;
  min-width: 950px !important;
  border-collapse: collapse !important;
  white-space: nowrap !important;
}

.modern-table th {
  background: #f8fafc !important;
  color: #475569 !important;
  font-size: 12px !important;
  font-weight: 700 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.05em !important;
  padding: 16px 20px !important;
  border-bottom: 2px solid #e2e8f0 !important;
  text-align: left !important;
}

.modern-table td {
  padding: 16px 20px !important;
  border-bottom: 1px solid #f1f5f9 !important;
  font-size: 13px !important;
  color: #334155 !important;
  vertical-align: middle !important;
}

.modern-table tbody tr {
  transition: background-color 0.2s ease !important;
}

.modern-table tbody tr:hover {
  background-color: #f8fafc !important;
}

.font-mono {
  font-family: "Courier New", Courier, monospace !important;
  font-size: 14px !important;
}
.font-bold {
  font-weight: 700 !important;
  color: #0f172a !important;
}
.font-semibold {
  font-weight: 600 !important;
  color: #1e293b !important;
}

/* === 🔥 5. CSS TOMBOL "LIHAT FILE" DI DALAM TABEL (GLOW EMERALD SAKTI) === */
.action-cells {
  text-align: center !important;
}

.btn-action.view {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
  color: #ffffff !important;
  border: none !important;
  padding: 8px 18px !important;
  border-radius: 8px !important;
  font-weight: 700 !important;
  font-size: 12px !important;
  cursor: pointer !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
  box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2) !important;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.btn-action.view:hover {
  transform: translateY(-1px) !important;
  box-shadow: 0 6px 15px rgba(16, 185, 129, 0.35) !important;
}

.btn-action.view i {
  font-size: 13px !important;
}

.btn-action.edit {
  background: #fff7ed !important;
  color: #ea580c !important;
  border: 1px solid #fed7aa !important;
  padding: 8px 16px !important;
  border-radius: 8px !important;
  font-weight: 700 !important;
  font-size: 12px !important;
  cursor: pointer !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
  margin-left: 8px !important;
  transition: all 0.2s ease !important;
}
.btn-action.edit:hover {
  background: #ea580c !important;
  color: #ffffff !important;
  border-color: #ea580c !important;
}

/* === 6. PAGINATION === */
.pagination-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  padding: 15px 20px;
  border-top: 1px solid #e2e8f0;
  background: white;
  border-bottom-left-radius: 20px;
  border-bottom-right-radius: 20px;
  position: sticky;
  bottom: 0;
  z-index: 5;
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
  min-width: 34px;
  height: 34px;
  padding: 0 8px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 700;
  color: #475569;
  font-size: 13px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  font-family: inherit;
}
.pg-btn:hover:not(:disabled):not(.active) {
  background: #f1f5f9;
  border-color: #cbd5e1;
}
.pg-btn.active {
  background: #059669;
  color: #ffffff;
  border-color: #059669;
}
.pg-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

/* === 7. MODAL LIVE PREVIEW PDF === */
.modal-overlay {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  width: 100vw !important;
  height: 100vh !important;
  background: rgba(15, 23, 42, 0.4) !important;
  backdrop-filter: blur(6px) !important;
  z-index: 99999 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}

.modal-card {
  background: #ffffff !important;
  width: 90% !important;
  max-width: 1100px !important;
  border-radius: 16px !important;
  overflow: hidden !important;
  display: flex !important;
  flex-direction: column !important;
  max-height: 88vh !important;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
  border: 1px solid rgba(255, 255, 255, 0.2) !important;
}

.modal-header {
  padding: 18px 24px !important;
  display: flex !important;
  justify-content: space-between !important;
  align-items: center !important;
  border-bottom: 1px solid #e2e8f0 !important;
  background: #f8fafc !important;
}

.modal-header h3 {
  margin: 0 !important;
  font-size: 16px !important;
  font-weight: 800 !important;
  color: #0f172a !important;
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
}

.btn-close {
  background: #e2e8f0 !important;
  border: none !important;
  width: 30px !important;
  height: 30px !important;
  border-radius: 50% !important;
  cursor: pointer !important;
  color: #475569 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  transition: all 0.2s ease !important;
}

.btn-close:hover {
  background: #fecaca !important;
  color: #dc2626 !important;
}
.modal-body {
  padding: 0 !important;
  height: 550px !important;
  background: #e2e8f0 !important;
}
.preview-iframe {
  width: 100% !important;
  height: 100% !important;
  border: none !important;
}

/* === SPLIT LAYOUT: preview + tracking === */
.modal-body-split {
  display: flex !important;
  height: 560px !important;
  padding: 0 !important;
  background: #fff !important;
}
.preview-col {
  flex: 1.4;
  background: #e2e8f0;
  min-width: 0;
}
.track-col {
  flex: 1;
  max-width: 420px;
  padding: 20px;
  overflow-y: auto;
  border-left: 1px solid #e2e8f0;
  background: #fff;
}
.track-title {
  font-size: 15px;
  font-weight: 800;
  color: #0f172a;
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
}
.track-title i {
  color: #10b981;
}
.track-status-badge {
  display: inline-block;
  padding: 5px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  margin-bottom: 20px;
}
.badge-belum {
  background: #fef3c7;
  color: #b45309;
}
.badge-proses {
  background: #dbeafe;
  color: #1d4ed8;
}
.badge-selesai {
  background: #d1fae5;
  color: #047857;
}

/* Timeline */
.timeline {
  position: relative;
  padding-left: 8px;
}
.tl-item {
  position: relative;
  padding-left: 34px;
  padding-bottom: 24px;
}
.tl-item:not(:last-child)::before {
  content: "";
  position: absolute;
  left: 12px;
  top: 26px;
  bottom: 0;
  width: 2px;
  background: #e2e8f0;
}
.tl-item.done:not(:last-child)::before {
  background: #10b981;
}
.tl-marker {
  position: absolute;
  left: 0;
  top: 0;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  z-index: 1;
}
.tl-item.done .tl-marker {
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
  box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
}
.tl-item.pending .tl-marker {
  background: #f1f5f9;
  color: #94a3b8;
  border: 2px solid #e2e8f0;
}
.tl-judul {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
}
.tl-item.pending .tl-judul {
  color: #94a3b8;
}
.tl-oleh,
.tl-waktu {
  font-size: 12px;
  color: #64748b;
  margin-top: 3px;
  display: flex;
  align-items: center;
  gap: 6px;
}
.tl-oleh i,
.tl-waktu i {
  font-size: 10px;
  color: #94a3b8;
}
.tl-info {
  font-size: 12.5px;
  color: #475569;
  margin-top: 6px;
  line-height: 1.5;
  background: #f8fafc;
  padding: 8px 10px;
  border-radius: 8px;
}
.tl-item.pending .tl-info {
  color: #94a3b8;
  font-style: italic;
}

/* Responsive: tumpuk vertikal di layar kecil */
@media (max-width: 768px) {
  .modal-body-split {
    flex-direction: column;
    height: auto !important;
    max-height: 75vh;
    overflow-y: auto;
  }
  .preview-col {
    height: 300px;
  }
  .track-col {
    max-width: none;
    border-left: none;
    border-top: 1px solid #e2e8f0;
  }
}
.empty-state {
  text-align: center !important;
  padding: 40px 20px !important;
  color: #64748b !important;
  font-weight: 500 !important;
}

/* === BREAKPOINTS RESPONSIF === */
@media (max-width: 1100px) {
  .table-header-actions {
    flex-wrap: wrap !important;
    gap: 12px !important;
  }
  .search-box {
    flex: 1 1 200px !important;
    width: auto !important;
    box-sizing: border-box !important;
  }
}

@media (max-width: 900px) {
  .page-header {
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 15px !important;
    padding: 20px !important;
  }
  .header-actions {
    width: 100% !important;
    flex-wrap: wrap !important;
    gap: 10px !important;
  }
  .btn-back-outline,
  .btn-print-report {
    flex: 1 1 auto !important;
    justify-content: center !important;
  }
  .summary-container {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 15px !important;
  }
}

@media (max-width: 768px) {
  .vue-list-body {
    padding: 15px !important;
  }
  .header-text h2 {
    font-size: 20px !important;
  }
  .header-text p {
    font-size: 12px !important;
  }
  .btn-back-outline,
  .btn-print-report {
    width: 100% !important;
    justify-content: center !important;
    box-sizing: border-box !important;
  }
  .summary-container {
    grid-template-columns: 1fr !important;
  }
  .table-header-actions {
    flex-direction: column !important;
    align-items: stretch !important;
    gap: 12px !important;
    padding: 15px !important;
  }
  .filter-group {
    flex-direction: column !important;
    align-items: stretch !important;
    gap: 10px !important;
  }
  .filter-select,
  .btn-cetak {
    width: 100% !important;
    box-sizing: border-box !important;
  }
  
}

@media (max-width: 480px) {
  .summary-info h2 {
    font-size: 28px !important;
    letter-spacing: -0.5px !important;
  }
  .summary-info span {
    font-size: 11px !important;
  }
  .modern-table th {
    font-size: 11px !important;
    padding: 12px 14px !important;
  }
  .modern-table td {
    font-size: 12px !important;
    padding: 12px 14px !important;
  }
  .pagination-container {
    flex-direction: column !important;
    gap: 10px !important;
    padding: 12px 15px !important;
  }
  .pg-info {
    font-size: 12px !important;
    text-align: center !important;
  }
  .pg-btn {
    min-width: 32px !important;
    height: 32px !important;
    font-size: 12px !important;
  }
}

@media (max-width: 360px) {
  .page-header {
    padding: 15px !important;
  }
  .header-text h2 {
    font-size: 17px !important;
  }
  .pg-btn {
    min-width: 28px !important;
    height: 28px !important;
    font-size: 11px !important;
    padding: 0 6px !important;
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
