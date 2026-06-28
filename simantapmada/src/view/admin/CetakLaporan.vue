<template>
  <!-- MODAL OVERLAY -->
  <Teleport to="body">
    <div v-if="tampil" class="cetak-overlay" @click.self="tutup">
      <div class="cetak-modal">
        <!-- TOOLBAR (layar saja, hilang saat print) -->
        <div class="toolbar no-print">
          <div class="toolbar-left">
            <i class="fa-solid fa-print" style="color: #10b981"></i>
            <span>Pratinjau Cetak — {{ config.judul || "Laporan" }}</span>
          </div>
          <div class="toolbar-right">
            <button class="btn-cetak" @click="cetak" :disabled="!adaData">
              <i class="fa-solid fa-print"></i> Cetak / Print
            </button>
            <button class="btn-tutup" @click="tutup">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
        </div>

        <!-- PESAN KOSONG -->
        <div v-if="!adaData" class="empty-wrapper no-print">
          <i class="fa-solid fa-file-circle-xmark"></i>
          <p>Tidak ada data laporan untuk dicetak.</p>
          <p class="empty-sub">
            Klik tombol "Cetak Rekap" dari halaman laporan terlebih dahulu.
          </p>
        </div>

        <!-- LEMBAR CETAK -->
        <div v-else class="lembar-cetak">
          <!-- KOP SURAT -->
          <div class="kop">
            <div class="kop-logo-area">
              <img
                :src="logoDesa"
                alt="Logo"
                class="kop-logo"
                onerror="this.style.display = 'none'"
              />
            </div>
            <div class="kop-teks">
              <p class="kop-pemerintah">PEMERINTAH KABUPATEN NGANJUK</p>
              <p class="kop-kecamatan">KECAMATAN LOCERET</p>
              <h1 class="kop-desa">KANTOR DESA MACANAN</h1>
              <p class="kop-alamat">
                Jl. Raya Desa Mada No. 1, Waru, Sidoarjo 61256
                &nbsp;&#124;&nbsp; Telp. (031) 123456 &nbsp;&#124;&nbsp; Email:
                desemada@sidoarjo.go.id
              </p>
            </div>
          </div>
          <div class="kop-garis-tebal"></div>
          <div class="kop-garis-tipis"></div>

          <!-- JUDUL & PERIODE -->
          <div class="judul-wrapper">
            <h2 class="judul">{{ config.judul || "LAPORAN" }}</h2>
            <p class="periode">
              Periode: <strong>{{ config.periode || "Semua Periode" }}</strong>
            </p>
            <p class="total-data">
              Jumlah Data:
              <strong>{{ (config.data || []).length }} Record</strong>
            </p>
          </div>

          <!-- TABEL -->
          <table class="tabel-cetak">
            <thead>
              <tr>
                <th
                  v-for="kol in config.kolom"
                  :key="kol.key"
                  :width="kol.width || null"
                >
                  {{ kol.label }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(row, index) in config.data" :key="index">
                <td
                  v-for="kol in config.kolom"
                  :key="kol.key"
                  :class="kelasKolom(kol)"
                >
                  <!-- Nomor urut -->
                  <template v-if="kol.tipe === 'index'">{{
                    index + 1
                  }}</template>

                  <!-- Dua baris: nomor surat + tanggal -->
                  <template v-else-if="kol.tipe === 'dua_baris'">
                    <span class="baris-atas">{{
                      ambilNilai(row, kol.key) || "-"
                    }}</span
                    ><br />
                    <span class="baris-bawah">{{
                      formatTanggal(ambilNilai(row, kol.subKey))
                    }}</span>
                  </template>

                  <!-- Tanggal tunggal -->
                  <template v-else-if="kol.tipe === 'tanggal'">
                    {{ formatTanggal(ambilNilai(row, kol.key)) }}
                  </template>

                  <!-- Status badge -->
                  <template v-else-if="kol.tipe === 'status'">
                    <span
                      :class="
                        'badge-status badge-' +
                        slugStatus(ambilNilai(row, kol.key))
                      "
                    >
                      {{ ambilNilai(row, kol.key) || "-" }}
                    </span>
                  </template>

                  <!-- Teks biasa / biru -->
                  <template v-else>
                    {{ ambilNilai(row, kol.key) || "-" }}
                  </template>
                </td>
              </tr>

              <tr v-if="!config.data || config.data.length === 0">
                <td :colspan="config.kolom?.length" class="kosong">
                  Tidak ada data pada periode ini.
                </td>
              </tr>
            </tbody>
          </table>

          <!-- TANDA TANGAN -->
          <div class="ttd-wrapper">
            <div class="ttd-blok">
              <p class="ttd-label">Mengetahui,</p>
              <p class="ttd-jabatan">Kepala Desa Mada</p>
              <div class="ttd-ruang"></div>
              <p class="ttd-nama">( ________________________ )</p>
            </div>
            <div class="ttd-blok">
              <p class="ttd-label">Sidoarjo, {{ tanggalCetak }}</p>
              <p class="ttd-jabatan">Sekretaris Desa</p>
              <div class="ttd-ruang"></div>
              <p class="ttd-nama">( ________________________ )</p>
            </div>
          </div>
        </div>
        <!-- /lembar-cetak -->
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
// Logo diambil dari src/img/ (sesuaikan nama file jika berbeda)
import logoDesa from "../../assets/img/logo_desa.png";

// Props: bisa dipakai sebagai komponen modal (v-model) ATAU tetap pakai sessionStorage
const props = defineProps({
  modelValue: { type: Boolean, default: false },
});
const emit = defineEmits(["update:modelValue"]);

const config = ref({ judul: "", periode: "", kolom: [], data: [] });
const tampil = ref(props.modelValue);

// Sync v-model
watch(
  () => props.modelValue,
  (val) => {
    tampil.value = val;
    if (val) bacaConfig();
  },
);

const adaData = computed(
  () =>
    Array.isArray(config.value.kolom) &&
    config.value.kolom.length > 0 &&
    Array.isArray(config.value.data),
);

const tanggalCetak = computed(() =>
  new Date().toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "long",
    year: "numeric",
  }),
);

// Baca config dari localStorage (dibagikan antar-tab; sessionStorage tidak)
const bacaConfig = () => {
  try {
    const raw =
      localStorage.getItem("cetakLaporanConfig") ||
      sessionStorage.getItem("cetakLaporanConfig");
    if (raw) config.value = JSON.parse(raw);
  } catch (e) {
    console.error("Gagal membaca config cetak:", e);
  }
};

onMounted(bacaConfig);

// Helper
const ambilNilai = (row, key) => {
  if (!key) return "";
  return key.split(".").reduce((acc, k) => (acc ? acc[k] : ""), row);
};

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

const kelasKolom = (kol) => {
  if (kol.tipe === "index") return "kolom-index";
  if (kol.tipe === "biru") return "kolom-biru";
  if (kol.tipe === "dua_baris") return "kolom-gabung";
  if (kol.tipe === "status") return "kolom-status";
  return "";
};

const slugStatus = (status) => {
  if (!status) return "default";
  return status.toLowerCase().replace(/\s+/g, "-");
};

const cetak = () => window.print();

const tutup = () => {
  tampil.value = false;
  emit("update:modelValue", false);
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

/* =============================================
   OVERLAY & MODAL
   ============================================= */
.cetak-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.55);
  backdrop-filter: blur(6px);
  z-index: 99999;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: 24px 16px;
  overflow-y: auto;
}

.cetak-modal {
  background: #ffffff;
  width: 100%;
  max-width: 95%;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 25px 60px -12px rgba(15, 23, 42, 0.35);
  display: flex;
  flex-direction: column;
  margin-bottom: 24px;
}

/* =============================================
   TOOLBAR
   ============================================= */
.toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 22px;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  font-family: "Plus Jakarta Sans", sans-serif;
}

.toolbar-left {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #ffffff;
  font-weight: 700;
  font-size: 14px;
}

.toolbar-right {
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-cetak {
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
  border: none;
  padding: 9px 20px;
  border-radius: 9px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-family: "Plus Jakarta Sans", sans-serif;
  box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
  transition: all 0.2s ease;
}
.btn-cetak:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(16, 185, 129, 0.45);
}
.btn-cetak:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-tutup {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: #cbd5e1;
  width: 34px;
  height: 34px;
  border-radius: 8px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  transition: all 0.2s ease;
}
.btn-tutup:hover {
  background: #ef4444;
  color: #fff;
  border-color: #ef4444;
}

/* =============================================
   EMPTY STATE
   ============================================= */
.empty-wrapper {
  padding: 60px 24px;
  text-align: center;
  color: #64748b;
  font-family: "Plus Jakarta Sans", sans-serif;
}
.empty-wrapper i {
  font-size: 48px;
  color: #cbd5e1;
  display: block;
  margin-bottom: 14px;
}
.empty-wrapper p {
  margin: 4px 0;
  font-weight: 600;
}
.empty-sub {
  font-weight: 400 !important;
  font-size: 13px;
}

/* =============================================
   LEMBAR CETAK
   ============================================= */
.lembar-cetak {
  padding: 36px 44px;
  background: #ffffff;
  font-family: "Times New Roman", Times, serif;
  color: #0f172a;
  max-height: 74vh;
  overflow-y: auto;
}

/* KOP */
.kop {
  display: flex;
  align-items: center;
  gap: 18px;
  margin-bottom: 8px;
}
.kop-logo {
  width: 70px;
  height: 70px;
  object-fit: contain;
  flex-shrink: 0;
}
.kop-teks {
  flex: 1;
  text-align: center;
}
.kop-pemerintah {
  margin: 0;
  font-size: 13px;
  font-weight: 400;
  letter-spacing: 0.03em;
}
.kop-kecamatan {
  margin: 0;
  font-size: 14px;
  font-weight: 700;
  letter-spacing: 0.05em;
}
.kop-desa {
  margin: 2px 0 4px 0;
  font-size: 21px;
  font-weight: 900;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: #1f4e96;
}
.kop-alamat {
  margin: 0;
  font-size: 10.5px;
  color: #475569;
  font-style: italic;
}

.kop-garis-tebal {
  border-top: 4px solid #1f4e96;
  margin-top: 8px;
}
.kop-garis-tipis {
  border-top: 1.5px solid #7a1726;
  margin-top: 2px;
  margin-bottom: 20px;
}

/* JUDUL */
.judul-wrapper {
  text-align: center;
  margin-bottom: 18px;
}
.judul {
  margin: 0 0 4px 0;
  font-size: 15px;
  font-weight: 900;
  text-decoration: underline;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #0f172a;
}
.periode {
  margin: 0;
  font-size: 12px;
  color: #334155;
}
.total-data {
  margin: 2px 0 0 0;
  font-size: 12px;
  color: #475569;
}

/* TABEL */
.tabel-cetak {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 36px;
}
.tabel-cetak th,
.tabel-cetak td {
  border: 1px solid #000;
  padding: 7px 9px;
  font-size: 12px;
  vertical-align: top;
  color: #000;
}
.tabel-cetak th {
  background: #1f4e96;
  color: #ffffff;
  text-align: center;
  font-weight: 700;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.tabel-cetak tbody tr:nth-child(even) td {
  background: #f8fafc;
}

.kolom-index {
  text-align: center;
  font-weight: 700;
  color: #1f4e96;
}
.kolom-biru {
  color: #1f4e96;
}
.kolom-gabung .baris-atas {
  font-weight: 700;
  font-family: "Courier New", monospace;
  font-size: 11px;
}
.kolom-gabung .baris-bawah {
  display: inline-block;
  margin-top: 2px;
  font-size: 10.5px;
  color: #b45309;
}

.kolom-status {
  text-align: center;
}
.badge-status {
  display: inline-block;
  padding: 2px 10px;
  border-radius: 99px;
  font-size: 10px;
  font-weight: 700;
  font-family: "Plus Jakarta Sans", sans-serif;
}
.badge-selesai {
  background: #d1fae5;
  color: #065f46;
}
.badge-diproses {
  background: #fef9c3;
  color: #92400e;
}
.badge-diajukan {
  background: #dbeafe;
  color: #1e40af;
}
.badge-ditolak {
  background: #fee2e2;
  color: #991b1b;
}
.badge-default {
  background: #f1f5f9;
  color: #475569;
}

.kosong {
  text-align: center;
  padding: 20px;
  color: #64748b;
  font-style: italic;
}

/* TANDA TANGAN */
.ttd-wrapper {
  display: flex;
  justify-content: space-between;
  margin-top: 44px;
  font-size: 12px;
  font-family: "Times New Roman", Times, serif;
}
.ttd-blok {
  text-align: center;
  min-width: 180px;
}
.ttd-label {
  margin: 0;
  font-size: 12px;
}
.ttd-jabatan {
  margin: 2px 0 0 0;
  font-weight: 700;
  font-size: 12.5px;
}
.ttd-ruang {
  height: 64px;
}
.ttd-nama {
  margin: 0;
  font-size: 12px;
  display: inline-block;
  border-top: 1px solid #0f172a;
  padding-top: 4px;
}

/* === SCREEN RESPONSIVE === */
@media screen and (max-width: 768px) {
  .toolbar {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
    padding: 12px 15px;
  }
  .toolbar-left {
    justify-content: center;
    font-size: 13px;
  }
  .toolbar-right {
    justify-content: space-between;
  }
  .btn-cetak {
    flex: 1;
    justify-content: center;
  }
  .lembar-cetak {
    padding: 20px 24px;
    max-height: 65vh;
  }
}

@media screen and (max-width: 480px) {
  .toolbar {
    padding: 10px 12px;
  }
  .btn-cetak {
    font-size: 13px;
    padding: 10px 16px;
  }
  .kop-desa {
    font-size: 17px;
  }
  .lembar-cetak {
    padding: 15px;
    max-height: 60vh;
  }
}

/* =============================================
   PRINT STYLES
   ============================================= */
@media print {
  @page {
    size: A4 portrait;
    margin: 18mm 16mm;
  }
 
  :global(body > *:not(.cetak-overlay)) {
    display: none !important;
  }

  .no-print {
    display: none !important;
  }

  .cetak-overlay {
    position: static !important;
    background: none !important;
    padding: 0 !important;
    display: block !important;
    overflow: visible !important;
  }
  .cetak-modal {
    box-shadow: none !important;
    border-radius: 0 !important;
    max-width: 100% !important;
  }
  .lembar-cetak {
    max-height: none !important;
    overflow: visible !important;
    padding: 0 !important;
  }
  .tabel-cetak {
    page-break-inside: auto;
  }
  .tabel-cetak tr {
    page-break-inside: avoid;
  }
  .badge-selesai {
    border: 1px solid #065f46;
  }
  .badge-diproses {
    border: 1px solid #92400e;
  }
  .badge-diajukan {
    border: 1px solid #1e40af;
  }
}
</style>
