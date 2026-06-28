<template>
  <Topbar />
  <div class="lap-body">
    <div class="lap-wrapper">
      <div class="page-header-box fade-up no-print">
        <div class="header-text">
          <h2>
            <i class="fa-solid fa-chart-pie"></i> Rangkuman Statistik Terpadu
          </h2>
          <p>
            Data analitik real-time Aset, Arsip, dan Pelayanan Desa Macanan.
          </p>
        </div>
        <div class="header-actions">
          <button class="btn-back-glass" @click="kembali">
            <i class="fa-solid fa-arrow-left"></i> Dashboard
          </button>
          <button class="btn-cetak" @click="cetak">
            <i class="fa-solid fa-print"></i> Cetak PDF
          </button>
        </div>
      </div>

      <div id="area-cetak">
        <div class="kop-cetak print-only">
          <h2>PEMERINTAH DESA MACANAN</h2>
          <p>Kecamatan Loceret, Kabupaten Nganjuk</p>
          <h3>LAPORAN STATISTIK TERPADU</h3>
          <hr />
        </div>

        <div v-if="loading" class="loading-state">
          <i class="fa-solid fa-spinner fa-spin"></i> Memuat data statistik...
        </div>

        <template v-else>
          <div class="summary-grid fade-up delay-1">
            <div class="sum-card">
              <div class="sum-icon icon-emerald">
                <i class="fa-solid fa-boxes-stacked"></i>
              </div>
              <div class="sum-info">
                <h4>{{ total.aset }}</h4>
                <span>Total Aset</span>
              </div>
            </div>
            <div class="sum-card">
              <div class="sum-icon icon-navy">
                <i class="fa-solid fa-folder-open"></i>
              </div>
              <div class="sum-info">
                <h4>{{ total.arsip }}</h4>
                <span>Total Dokumen Arsip</span>
              </div>
            </div>
            <div class="sum-card">
              <div class="sum-icon icon-teal">
                <i class="fa-solid fa-users-gear"></i>
              </div>
              <div class="sum-info">
                <h4>{{ total.pengajuan }}</h4>
                <span>Total Permohonan Layanan</span>
              </div>
            </div>
          </div>

          <div class="tabel-grid fade-up delay-2">
            <div class="stat-tabel" v-for="t in tabelData" :key="t.judul">
              <div class="st-title">
                <i :class="t.ikon" :style="{ color: t.warna }"></i>
                {{ t.judul }}
              </div>
              <table class="st-table">
                <thead>
                  <tr>
                    <th>Kategori</th>
                    <th class="ta-right">Jumlah</th>
                    <th class="ta-right">Persen</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="t.rows.length === 0">
                    <td colspan="3" class="st-empty">Belum ada data.</td>
                  </tr>
                  <template v-else>
                    <tr v-for="r in t.rows" :key="r.label">
                      <td>{{ r.label }}</td>
                      <td class="ta-right st-num">{{ r.jml }}</td>
                      <td class="ta-right st-persen">
                        {{ persen(r.jml, t.rows) }}
                      </td>
                    </tr>
                    <tr class="st-total">
                      <td>Total</td>
                      <td class="ta-right">{{ totalRows(t.rows) }}</td>
                      <td class="ta-right">100%</td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import Topbar from "../../components/Topbar.vue";
import { ref, reactive, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();
const BASE = `${import.meta.env.VITE_API_URL}/api`;

const loading = ref(true);
const total = reactive({ aset: 0, arsip: 0, pengajuan: 0 });

const rawData = reactive({
  aset_jenis: [],
  aset_kondisi: [],
  arsip_kategori: [],
  pengajuan_status: [],
});

const totalRows = (rows) => rows.reduce((a, b) => a + Number(b.jml || 0), 0);

const persen = (jml, rows) => {
  const t = totalRows(rows);
  return t ? Math.round((jml / t) * 100) + "%" : "0%";
};

const tabelData = computed(() => [
  {
    judul: "Komposisi Aset Berdasarkan Jenis",
    ikon: "fa-solid fa-layer-group",
    warna: "#059669",
    rows: rawData.aset_jenis,
  },
  {
    judul: "Kondisi Fisik Aset Inventaris",
    ikon: "fa-solid fa-heart-pulse",
    warna: "#d97706",
    rows: rawData.aset_kondisi,
  },
  {
    judul: "Distribusi Kategori E-Arsip",
    ikon: "fa-solid fa-box-archive",
    warna: "#0ea5e9",
    rows: rawData.arsip_kategori,
  },
  {
    judul: "Status Pengajuan Layanan Surat",
    ikon: "fa-solid fa-envelope-open-text",
    warna: "#6366f1",
    rows: rawData.pengajuan_status,
  },
]);

const fetchData = async () => {
  loading.value = true;
  try {
    const res = await axios.get(`${BASE}/dashboard/statistik-laporan`);
    const d = res.data?.data || {};
    total.aset = d.total?.aset ?? 0;
    total.arsip = d.total?.arsip ?? 0;
    total.pengajuan = d.total?.pengajuan ?? 0;

    const norm = (arr) =>
      (arr || []).map((x) => ({
        label: x.label || "Lainnya",
        jml: Number(x.jml),
      }));

    rawData.aset_jenis = norm(d.aset_jenis);
    rawData.aset_kondisi = norm(d.aset_kondisi);
    rawData.arsip_kategori = norm(d.arsip_kategori);
    rawData.pengajuan_status = norm(d.pengajuan_status);
  } catch (e) {
    console.error("Gagal memuat laporan:", e);
  } finally {
    loading.value = false;
  }
};

const kembali = () => {
  let role = "";
  try {
    role = JSON.parse(localStorage.getItem("user") || "{}").role || "";
  } catch (e) {
    role = "";
  }
  const tujuan = {
    admin: "/admin",
    sekdes: "/sekdes",
    kades: "/kades",
    developer: "/dev",
  };
  if (tujuan[role]) router.push(tujuan[role]);
  else router.back();
};

const cetak = () => window.print();

onMounted(() => {
  const token = localStorage.getItem("token");
  if (token) axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  fetchData();
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.lap-body {
  font-family: "Plus Jakarta Sans", sans-serif;
  background: var(--bg-page, #f8fafc);
  min-height: 100vh;
  padding: 100px 20px 50px;
  box-sizing: border-box;
}
.lap-wrapper {
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

.page-header-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 15px;
  margin-bottom: 24px;
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
.header-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}
.btn-back-glass,
.btn-cetak {
  padding: 12px 22px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: 0.3s;
  border: none;
  font-family: inherit;
}
.btn-back-glass {
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.18);
}
.btn-back-glass:hover {
  background: #fff;
  color: #1e293b;
  transform: translateY(-2px);
}
.btn-cetak {
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
  box-shadow: 0 6px 16px rgba(5, 150, 105, 0.3);
}
.btn-cetak:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 22px rgba(5, 150, 105, 0.4);
}

.loading-state {
  text-align: center;
  padding: 60px;
  color: #94a3b8;
  font-weight: 600;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  margin-bottom: 24px;
}
.sum-card {
  background: #fff;
  border: 1px solid var(--border, #e2e8f0);
  border-radius: 14px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  transition: 0.3s;
}
.sum-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 25px rgba(15, 23, 42, 0.07);
}
.sum-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 19px;
  flex-shrink: 0;
}
.icon-emerald {
  background: #ecfdf5;
  color: #059669;
}
.icon-navy {
  background: #f1f5f9;
  color: #1e293b;
}
.icon-teal {
  background: #f0fdfa;
  color: #0f766e;
}
.sum-info h4 {
  margin: 0;
  font-size: 23px;
  font-weight: 800;
  color: var(--navy, #0f172a);
  line-height: 1;
}
.sum-info span {
  font-size: 12px;
  color: var(--text-muted, #64748b);
  font-weight: 600;
}

.tabel-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}
.stat-tabel {
  background: #fff;
  border: 1px solid var(--border, #e2e8f0);
  border-radius: 16px;
  padding: 20px;
}
.st-title {
  font-size: 14px;
  font-weight: 800;
  color: var(--navy, #0f172a);
  margin-bottom: 14px;
  padding-bottom: 12px;
  border-bottom: 2px solid #f1f5f9;
  display: flex;
  align-items: center;
  gap: 8px;
}
.st-table {
  width: 100%;
  border-collapse: collapse;
}
.st-table th {
  text-align: left;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--text-muted, #64748b);
  font-weight: 800;
  padding: 10px 12px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}
.st-table td {
  padding: 10px 12px;
  font-size: 14px;
  color: var(--text-body, #334155);
  border-bottom: 1px solid #f1f5f9;
}
.ta-right {
  text-align: right;
}
.st-num {
  font-weight: 700;
  color: var(--navy, #0f172a);
}
.st-persen {
  color: var(--primary, #059669);
  font-weight: 700;
}
.st-empty {
  text-align: center;
  color: #94a3b8;
  padding: 20px;
}
.st-total td {
  font-weight: 800;
  color: var(--navy, #0f172a);
  background: #f8fafc;
  border-top: 2px solid #e2e8f0;
  border-bottom: none;
}

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
  @page {
    size: A4 portrait;
    margin: 12mm;
  }
  .no-print {
    display: none !important;
  }
  .print-only {
    display: block !important;
  }
  .lap-body {
    padding: 0;
    background: #fff;
  }
  .summary-grid {
    grid-template-columns: repeat(3, 1fr) !important;
    gap: 8px;
    margin-bottom: 16px;
  }
  .sum-card {
    padding: 10px;
    border: 1px solid #ccc;
  }
  .sum-info h4 {
    font-size: 19px;
  }
  .tabel-grid {
    gap: 12px;
  }
  .stat-tabel {
    border: 1px solid #ccc;
    padding: 12px;
    page-break-inside: avoid;
  }
  .st-table td,
  .st-table th {
    padding: 7px 10px;
    font-size: 12px;
  }
}

@media (max-width: 1100px) {
  .header-actions {
    flex-wrap: wrap;
    gap: 10px;
  }
}

@media (max-width: 880px) {
  .tabel-grid {
    grid-template-columns: 1fr;
  }
}
@media (max-width: 560px) {
  .summary-grid {
    grid-template-columns: 1fr;
  }
}
@media (max-width: 480px) {
  .lap-body {
    padding: 90px 14px 30px;
  }
  .header-text h2 {
    font-size: 20px;
  }
}

@media (max-width: 360px) {
  .header-text h2 {
    font-size: 17px;
  }
}
</style>

<style>
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
