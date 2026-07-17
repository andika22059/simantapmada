<template>
  <Topbar />
  <div class="rekap-body">
    <div class="rekap-wrapper">
      <!-- HEADER NAVY DOTTED (disembunyikan saat cetak) -->
      <div class="page-header-box fade-up no-print">
        <div class="header-text">
          <h2><i class="fa-solid fa-chart-column"></i> Rekap Bulanan</h2>
          <p>Ringkasan aktivitas administrasi desa per bulan.</p>
        </div>
        <button class="btn-back-glass" @click="kembali">
          <i class="fa-solid fa-arrow-left"></i> Dashboard
        </button>
      </div>

      <!-- FILTER BULAN/TAHUN + CETAK (disembunyikan saat cetak) -->
      <div class="filter-bar fade-up delay-1 no-print">
        <div class="filter-group">
          <label>Bulan</label>
          <select v-model="bulan" @change="fetchRekap">
            <option value="semua">Semua Bulan</option>
            <option v-for="(n, i) in namaBulan" :key="i" :value="i + 1">
              {{ n }}
            </option>
          </select>
        </div>
        <div class="filter-group">
          <label>Tahun</label>
          <select v-model="tahun" @change="fetchRekap">
            <option v-for="t in daftarTahun" :key="t" :value="t">
              {{ t }}
            </option>
          </select>
        </div>
        <button class="btn-cetak" @click="cetak">
          <i class="fa-solid fa-print"></i> Cetak / PDF
        </button>
      </div>

      <!-- AREA CETAK -->
      <div id="area-cetak" class="rekap-content fade-up delay-2">
        <!-- Kop cetak (hanya muncul saat print) -->
        <div class="kop-cetak print-only">
          <h2>PEMERINTAH DESA MACANAN</h2>
          <p>Kecamatan Loceret, Kabupaten Nganjuk</p>
          <h3>
            REKAP BULANAN — {{ data.nama_bulan?.toUpperCase() }}
            {{ data.tahun }}
          </h3>
          <hr />
        </div>

        <div v-if="loading" class="loading-state">
          <i class="fa-solid fa-spinner fa-spin"></i> Memuat rekap...
        </div>

        <template v-else>
          <!-- Judul periode -->
          <div class="periode-badge no-print">
            <i class="fa-solid fa-calendar-days"></i> Periode:
            <strong>{{ data.nama_bulan }} {{ data.tahun }}</strong>
          </div>

          <!-- RINGKASAN KUNCI -->
          <div class="rekap-section">
            <h3 class="section-head">
              <i class="fa-solid fa-gauge-high"></i> Ringkasan Kunci
            </h3>
            <div class="ringkas-grid">
              <div class="ringkas-card"><span class="rk-num">{{ data.pengajuan?.total ?? 0 }}</span><span class="rk-label">Total Pengajuan</span></div>
              <div class="ringkas-card"><span class="rk-num text-green">{{ data.pengajuan?.selesai ?? 0 }}</span><span class="rk-label">Selesai</span></div>
              <div class="ringkas-card"><span class="rk-num text-amber">{{ data.pengajuan?.diproses ?? 0 }}</span><span class="rk-label">Diproses</span></div>
              <div class="ringkas-card"><span class="rk-num text-red">{{ data.pengajuan?.dikembalikan ?? 0 }}</span><span class="rk-label">Dikembalikan</span></div>
              <div class="ringkas-card"><span class="rk-num">{{ data.surat?.masuk ?? 0 }}</span><span class="rk-label">Surat Masuk</span></div>
              <div class="ringkas-card"><span class="rk-num">{{ data.surat?.keluar ?? 0 }}</span><span class="rk-label">Surat Keluar</span></div>
              <div class="ringkas-card"><span class="rk-num text-teal">{{ data.surat?.didisposisi ?? 0 }}</span><span class="rk-label">Didisposisi</span></div>
              <div class="ringkas-card"><span class="rk-num">{{ data.arsip ?? 0 }}</span><span class="rk-label">Arsip Baru</span></div>
              <div class="ringkas-card"><span class="rk-num">{{ data.aset ?? 0 }}</span><span class="rk-label">Aset Baru</span></div>
            </div>
          </div>

          <!-- GRAFIK BATANG SEDERHANA -->
          <div class="rekap-section">
            <h3 class="section-head">
              <i class="fa-solid fa-chart-simple"></i> Grafik Ringkasan
            </h3>
            <div class="chart-box">
              <div
                class="chart-row"
                v-for="item in chartData"
                :key="item.label"
              >
                <span class="chart-label">{{ item.label }}</span>
                <div class="chart-track">
                  <div
                    class="chart-fill"
                    :style="{
                      width: chartWidth(item.value) + '%',
                      background: item.color,
                    }"
                  >
                    <span class="chart-val" v-if="item.value > 0">{{
                      item.value
                    }}</span>
                  </div>
                  <span class="chart-val-out" v-if="item.value === 0">0</span>
                </div>
              </div>
            </div>
          </div>

          <!-- REKAP RINGKAS (angka saja) -->
          <div class="rekap-section">
            <h3 class="section-head">
              <i class="fa-solid fa-table-list"></i> Rekap Ringkas
            </h3>
            <table class="rekap-table ringkas-table">
              <thead>
                <tr><th>Modul / Kategori</th><th class="col-jml">Jumlah</th></tr>
              </thead>
              <tbody>
                <tr class="grp"><td colspan="2">Pelayanan Surat</td></tr>
                <tr><td>Total Pengajuan</td><td class="col-jml">{{ data.pengajuan?.total ?? 0 }}</td></tr>
                <tr><td>Selesai</td><td class="col-jml">{{ data.pengajuan?.selesai ?? 0 }}</td></tr>
                <tr><td>Diproses</td><td class="col-jml">{{ data.pengajuan?.diproses ?? 0 }}</td></tr>
                <tr><td>Dikembalikan</td><td class="col-jml">{{ data.pengajuan?.dikembalikan ?? 0 }}</td></tr>
                <tr class="grp"><td colspan="2">Persuratan</td></tr>
                <tr><td>Surat Masuk</td><td class="col-jml">{{ data.surat?.masuk ?? 0 }}</td></tr>
                <tr><td>Didisposisi</td><td class="col-jml">{{ data.surat?.didisposisi ?? 0 }}</td></tr>
                <tr><td>Surat Keluar</td><td class="col-jml">{{ data.surat?.keluar ?? 0 }}</td></tr>
                <tr class="grp"><td colspan="2">Arsip & Inventaris</td></tr>
                <tr><td>Arsip Baru</td><td class="col-jml">{{ data.arsip ?? 0 }}</td></tr>
                <tr><td>Aset Baru</td><td class="col-jml">{{ data.aset ?? 0 }}</td></tr>
              </tbody>
            </table>
          </div>
                </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import Topbar from "../../components/Topbar.vue";
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();
const BASE = `${import.meta.env.VITE_API_URL}/api`;

const namaBulan = [
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

const now = new Date();
const bulan = ref(now.getMonth() + 1);
const tahun = ref(now.getFullYear());
const daftarTahun = computed(() => {
  const t = now.getFullYear();
  return [t - 2, t - 1, t, t + 1];
});

const data = ref({});
const loading = ref(true);

const tglCetak = computed(() =>
  new Date().toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  }),
);

// Data untuk grafik batang
const chartData = computed(() => {
  const d = data.value;
  return [
    { label: "Pengajuan", value: d.pengajuan?.total ?? 0, color: "#059669" },
    { label: "Surat Masuk", value: d.surat?.masuk ?? 0, color: "#0f766e" },
    { label: "Disposisi", value: d.surat?.didisposisi ?? 0, color: "#14b8a6" },
    { label: "Surat Keluar", value: d.surat?.keluar ?? 0, color: "#0ea5e9" },
    { label: "Arsip", value: d.arsip ?? 0, color: "#6366f1" },
    { label: "Aset", value: d.aset ?? 0, color: "#d97706" },
  ];
});
const maxChart = computed(() =>
  Math.max(1, ...chartData.value.map((c) => c.value)),
);
const chartWidth = (val) => {
  if (val === 0) return 0;
  // minimal 8% biar tetap kelihatan, maksimal 100%
  return Math.max(8, Math.round((val / maxChart.value) * 100));
};

// Daftar detail tiap modul
const listPengajuan = computed(() => data.value.list?.pengajuan || []);
const listSuratMasuk = computed(() => data.value.list?.surat_masuk || []);
const listSuratKeluar = computed(() => data.value.list?.surat_keluar || []);
const listArsip = computed(() => data.value.list?.arsip || []);
const listAset = computed(() => data.value.list?.aset || []);

// Tentukan dashboard asal berdasarkan role (dari localStorage)
const kembali = () => {
  let role = "";
  try {
    role = JSON.parse(localStorage.getItem("user") || "{}").role || "";
  } catch (e) {
    role = "";
  }
  if (role === "kades") router.push("/kades");
  else if (role === "sekdes") router.push("/sekdes");
  else router.back();
};

const fetchRekap = async () => {
  loading.value = true;
  try {
    const res = await axios.get(`${BASE}/dashboard/rekap-bulanan`, {
      params: { bulan: bulan.value, tahun: tahun.value },
    });
    data.value = res.data?.data || {};
  } catch (e) {
    console.error("Gagal memuat rekap:", e);
    data.value = {};
  } finally {
    loading.value = false;
  }
};

const formatTanggal = (iso) => {
  if (!iso) return "-";
  return new Date(iso).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

const cetak = () => {
  const d = data.value || {};
  const peng = d.pengajuan || {};
  const sr = d.surat || {};
  const esc = (s) => (s ?? "-").toString().replace(/</g, "&lt;");
  const tgl = (iso) => formatTanggal(iso);
  const periode = `${d.nama_bulan || namaBulan[bulan.value - 1]} ${tahun.value}`;

  const row2 = (label, val) => `<tr><td>${label}</td><td class="c">${val ?? 0}</td></tr>`;
  const grp = (label) => `<tr class="grp"><td colspan="2"><b>${label}</b></td></tr>`;

  const inner = `
    <div class="kop">
      <h3>PEMERINTAH KABUPATEN NGANJUK</h3>
      <h3>KECAMATAN LOCERET</h3>
      <h2>KANTOR DESA MACANAN</h2>
      <p>Jl. Panglima Sudirman No. 15, Kode Pos 64471</p>
    </div>
    <hr class="garis" />
    <h3 class="judul">LAPORAN REKAPITULASI BULANAN<br /><small>Periode: ${periode}</small></h3>
    <div class="ringkasan"><strong>Ringkasan:</strong> Pelayanan total ${peng.total ?? 0} (selesai ${peng.selesai ?? 0}, diproses ${peng.diproses ?? 0}, dikembalikan ${peng.dikembalikan ?? 0}). Surat masuk ${sr.masuk ?? 0} (disposisi ${sr.didisposisi ?? 0}), surat keluar ${sr.keluar ?? 0}. Arsip ${d.arsip ?? 0}, aset ${d.aset ?? 0}.</div>
    <table>
      <thead><tr><th style="text-align:left">Modul / Kategori</th><th style="width:130px">Jumlah</th></tr></thead>
      <tbody>
        ${grp("Pelayanan Surat")}
        ${row2("Total Pengajuan", peng.total)}
        ${row2("Selesai", peng.selesai)}
        ${row2("Diproses", peng.diproses)}
        ${row2("Dikembalikan", peng.dikembalikan)}
        ${grp("Persuratan")}
        ${row2("Surat Masuk", sr.masuk)}
        ${row2("Didisposisi", sr.didisposisi)}
        ${row2("Surat Keluar", sr.keluar)}
        ${grp("Arsip & Inventaris")}
        ${row2("Arsip Baru", d.arsip)}
        ${row2("Aset Baru", d.aset)}
      </tbody>
    </table>
    <div class="ttd">
      <p>Macanan, ${tglCetak.value}</p>
      <p>Mengetahui,</p>
      <div class="ttd-space"></div>
      <p class="nama">( ........................... )</p>
    </div>`;

  const w = window.open("", "_blank", "width=1000,height=720");
  if (!w) {
    alert("Izinkan popup untuk mencetak laporan ini.");
    return;
  }
  w.document.write(`<!DOCTYPE html><html lang="id"><head><meta charset="utf-8" />
    <title>Rekap Bulanan ${periode}</title>
    <style>
      * { box-sizing: border-box; }
      body { font-family: "Times New Roman", serif; color: #000; margin: 22px 26px; }
      .kop { text-align: center; line-height: 1.25; }
      .kop h3 { margin: 0; font-size: 13pt; }
      .kop h2 { margin: 2px 0; font-size: 17pt; letter-spacing: 1px; }
      .kop p { margin: 2px 0 0; font-size: 10pt; }
      .garis { border: none; border-top: 3px double #000; margin: 6px 0 12px; }
      .judul { text-align: center; margin: 0 0 12px; font-size: 13pt; text-decoration: underline; }
      .judul small { font-size: 10pt; font-weight: normal; }
      .ringkasan { font-size: 10pt; margin: 0 0 14px; text-align: justify; }
      .sec { margin: 14px 0 6px; font-size: 11pt; }
      .kosong { font-size: 10pt; font-style: italic; color: #444; margin: 0 0 6px; }
      table { width: 100%; border-collapse: collapse; font-size: 10pt; margin-bottom: 4px; }
      th, td { border: 1px solid #000; padding: 4px 6px; vertical-align: top; }
      thead th { background: #e5e7eb; text-align: center; }
      td.c { text-align: center; }
      .ttd { width: 280px; margin: 26px 0 0 auto; text-align: center; font-size: 10pt; }
      .ttd-space { height: 64px; }
      .ttd .nama { font-weight: bold; text-decoration: underline; }
      @page { size: A4; margin: 14mm; }
    </style></head>
    <body onload="window.focus(); window.print();">${inner}
    <scr` + `ipt>window.onafterprint = function(){ window.close(); };</scr` + `ipt>
    </body></html>`);
  w.document.close();
};

onMounted(() => {
  const token = localStorage.getItem("token");
  if (token) axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  fetchRekap();
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

/* ===== Ringkasan kunci ===== */
.ringkas-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(118px, 1fr)); gap: 12px; }
.ringkas-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 10px; text-align: center; transition: 0.25s; }
.ringkas-card:hover { transform: translateY(-3px); box-shadow: 0 10px 22px rgba(15, 23, 42, 0.07); }
.ringkas-card .rk-num { display: block; font-size: 26px; font-weight: 800; color: #0f172a; line-height: 1; }
.ringkas-card .rk-label { display: block; margin-top: 6px; font-size: 12px; color: #64748b; font-weight: 600; }
.ringkas-table .col-jml { text-align: right; width: 130px; font-weight: 700; }
.ringkas-table .grp td { background: #f1f5f9; font-weight: 800; color: #0f766e; }

.rekap-body {
  font-family: "Plus Jakarta Sans", sans-serif;
  background: var(--bg-page, #f8fafc);
  min-height: 100vh;
  padding: 100px 20px 50px;
  box-sizing: border-box;
}
.rekap-wrapper {
  max-width: 95%;
  margin: 0 auto;
}

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.fade-up {
  animation: fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.delay-1 {
  animation-delay: 0.1s;
  opacity: 0;
}
.delay-2 {
  animation-delay: 0.2s;
  opacity: 0;
}

/* HEADER */
.page-header-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 15px;
  margin-bottom: 20px;
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
  margin: 0 0 5px 0;
  font-size: 24px;
  font-weight: 800;
  color: #fff;
  display: flex;
  align-items: center;
  gap: 12px;
}
.header-text h2 i {
  color: #34d399;
}
.header-text p {
  margin: 0;
  color: #94a3b8;
  font-size: 14px;
  font-weight: 500;
}
.btn-back-glass {
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.18);
  padding: 12px 22px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: 0.3s;
  font-family: inherit;
}
.btn-back-glass:hover {
  background: #fff;
  color: #1e293b;
  transform: translateY(-2px);
}

/* FILTER BAR */
.filter-bar {
  display: flex;
  gap: 14px;
  align-items: flex-end;
  margin-bottom: 24px;
  flex-wrap: wrap;
}
.filter-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.filter-group label {
  font-size: 12px;
  font-weight: 700;
  color: var(--text-muted, #64748b);
  text-transform: uppercase;
}
.filter-group select {
  border: 2px solid var(--border, #e2e8f0);
  border-radius: 12px;
  padding: 10px 16px;
  font-size: 14px;
  font-weight: 600;
  color: var(--navy, #0f172a);
  background: #fff;
  outline: none;
  cursor: pointer;
  font-family: inherit;
  min-width: 140px;
}
.filter-group select:focus {
  border-color: var(--primary, #059669);
}
.btn-cetak {
  margin-left: auto;
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
  border: none;
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: 0.2s;
  box-shadow: 0 6px 16px rgba(5, 150, 105, 0.3);
  font-family: inherit;
}
.btn-cetak:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 22px rgba(5, 150, 105, 0.4);
}

/* CONTENT */
.rekap-content {
  background: #fff;
  border: 1px solid var(--border, #e2e8f0);
  border-radius: 18px;
  padding: 30px;
}
.loading-state {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
  font-weight: 600;
}
.periode-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--primary-soft, #ecfdf5);
  color: var(--primary-dark, #047857);
  padding: 8px 16px;
  border-radius: 50px;
  font-size: 13px;
  font-weight: 700;
  margin-bottom: 24px;
}

.rekap-section {
  margin-bottom: 28px;
}
.section-head {
  font-size: 15px;
  font-weight: 800;
  color: var(--navy, #0f172a);
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 0 0 14px;
  padding-bottom: 10px;
  border-bottom: 2px solid #f1f5f9;
}
.section-head i {
  color: var(--primary, #059669);
}
.rekap-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
}
.rekap-card {
  background: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 14px;
  padding: 18px;
  text-align: center;
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.rc-num {
  font-size: 30px;
  font-weight: 800;
  color: var(--navy, #0f172a);
  line-height: 1;
}
.text-green {
  color: #059669;
}
.text-amber {
  color: #d97706;
}
.text-red {
  color: #dc2626;
}
.text-teal {
  color: #0f766e;
}
.rc-label {
  font-size: 12px;
  color: var(--text-muted, #64748b);
  font-weight: 600;
}

/* TABLE */
.rekap-table {
  width: 100%;
  border-collapse: collapse;
}
.rekap-table th {
  text-align: left;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--text-muted, #64748b);
  font-weight: 800;
  padding: 12px 16px;
  background: #f8fafc;
  border-bottom: 2px solid #e2e8f0;
}
.rekap-table td {
  padding: 12px 16px;
  font-size: 14px;
  color: var(--text-body, #334155);
  border-bottom: 1px solid #f1f5f9;
}
.col-no {
  width: 50px;
  text-align: center;
}
.td-empty {
  text-align: center;
  color: #94a3b8;
  padding: 24px;
}
.nomor-surat {
  font-family: "Courier New", monospace;
  font-size: 12px;
  font-weight: 700;
  color: #047857;
  background: #ecfdf5;
  padding: 3px 8px;
  border-radius: 6px;
}
.tag-mini {
  background: #f0fdfa;
  color: #0f766e;
  padding: 3px 10px;
  border-radius: 50px;
  font-size: 12px;
  font-weight: 700;
}
.text-dim {
  color: #cbd5e1;
}

/* GRAFIK BATANG */
.chart-box {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.chart-row {
  display: flex;
  align-items: center;
  gap: 14px;
}
.chart-label {
  width: 110px;
  flex-shrink: 0;
  font-size: 13px;
  font-weight: 700;
  color: var(--text-body, #334155);
  text-align: right;
}
.chart-track {
  flex: 1;
  background: #f1f5f9;
  border-radius: 8px;
  height: 26px;
  display: flex;
  align-items: center;
  position: relative;
}
.chart-fill {
  height: 100%;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  min-width: 26px;
  transition: width 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.chart-val {
  color: #fff;
  font-size: 12px;
  font-weight: 800;
  padding-right: 10px;
}
.chart-val-out {
  position: absolute;
  left: 10px;
  font-size: 12px;
  font-weight: 800;
  color: #94a3b8;
}

/* PRINT */
.print-only {
  display: none;
}
.kop-cetak {
  text-align: center;
  margin-bottom: 20px;
}
.kop-cetak h2 {
  margin: 0;
  font-size: 18px;
}
.kop-cetak p {
  margin: 2px 0;
  font-size: 13px;
}
.kop-cetak h3 {
  margin: 10px 0 6px;
  font-size: 15px;
}
.kop-cetak hr {
  border: none;
  border-top: 2px solid #000;
}

@media print {
  .no-print {
    display: none !important;
  }
  .print-only {
    display: block !important;
  }
  .rekap-body {
    padding: 0;
    background: #fff;
  }
  .rekap-content {
    border: none;
    border-radius: 0;
    padding: 0;
  }
  .rekap-card {
    border: 1px solid #ccc;
    padding: 10px;
  }
  .rc-num {
    font-size: 22px;
  }
  .rekap-section {
    margin-bottom: 16px;
    page-break-inside: avoid;
  }
  .section-head {
    font-size: 13px;
    margin-bottom: 8px;
  }
  .chart-track {
    height: 20px;
  }
  .rekap-grid {
    gap: 8px;
  }
}

/* RESPONSIVE */
@media (max-width: 1100px) {
  .header-actions {
    flex-wrap: wrap;
    gap: 10px;
  }
}

@media (max-width: 900px) {
  .page-header-box {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  .header-actions {
    width: 100%;
  }
}

@media (max-width: 768px) {
  .rekap-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (max-width: 480px) {
  .rekap-body {
    padding: 90px 14px 30px;
  }
  .header-text h2 {
    font-size: 20px;
  }
  .btn-cetak {
    margin-left: 0;
    width: 100%;
    justify-content: center;
  }
  .rekap-content {
    padding: 18px;
  }
}

@media (max-width: 360px) {
  .header-text h2 {
    font-size: 17px;
  }
}
</style>

<style>
/* GLOBAL print: sembunyikan Topbar & elemen lain di luar area rekap saat cetak */
@media print {
  body * {
    visibility: hidden;
  }
  #area-cetak,
  #area-cetak * {
    visibility: visible;
  }
  #area-cetak {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
}
</style>
