<template>
  <Topbar />
  <div class="rekap-body">
    <div class="rekap-wrapper">
      <!-- HEADER -->
      <div class="page-header-box fade-up">
        <div class="header-text">
          <h2>
            <i class="fa-solid fa-file-invoice"></i> Rekapitulasi Pelayanan
          </h2>
          <p>Cetak laporan bulanan untuk diserahkan kepada Kepala Desa.</p>
        </div>
        <button class="btn-back-glass" @click="kembali">
          <i class="fa-solid fa-arrow-left"></i> Dashboard
        </button>
      </div>

      <!-- FILTER + AKSI -->
      <div class="filter-bar fade-up delay-1">
        <div class="filter-inputs">
          <div class="filter-item">
            <label>Bulan</label>
            <select v-model.number="bulan" @change="fetchRekap">
              <option v-for="(n, i) in namaBulan" :key="i" :value="i + 1">
                {{ n }}
              </option>
            </select>
          </div>
          <div class="filter-item">
            <label>Tahun</label>
            <select v-model.number="tahun" @change="fetchRekap">
              <option v-for="t in daftarTahun" :key="t" :value="t">{{ t }}</option>
            </select>
          </div>
          <button class="btn-tampilkan" @click="fetchRekap">
            <i class="fa-solid fa-magnifying-glass"></i> Tampilkan
          </button>
        </div>
        <div class="aksi-cetak">
          <button class="btn-excel" @click="exportExcel">
            <i class="fa-solid fa-file-excel"></i> Export Excel
          </button>
          <button class="btn-print" @click="cetak">
            <i class="fa-solid fa-print"></i> Cetak PDF/Print
          </button>
        </div>
      </div>

      <!-- PREVIEW LAPORAN -->
      <div class="rekap-container fade-up delay-2">
        <div class="kop-surat">
          <h3>PEMERINTAH KABUPATEN NGANJUK</h3>
          <h3>KECAMATAN LOCERET</h3>
          <h2>KANTOR DESA MACANAN</h2>
          <p>Jl. Panglima Sudirman No. 15, Kode Pos: 64471</p>
        </div>

        <div class="judul-laporan">
          <h3>LAPORAN REKAPITULASI PELAYANAN SURAT WARGA</h3>
          <span>Periode: {{ data.nama_bulan || namaBulan[bulan - 1] }} {{ tahun }}</span>
        </div>

        <div class="table-responsive">
          <table class="table-rekap">
            <thead>
              <tr>
                <th class="c-no">No</th>
                <th class="c-tgl">Tanggal</th>
                <th class="c-jenis">Jenis Layanan</th>
                <th class="c-nomor">Nomor Surat</th>
                <th class="c-status">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="5" class="td-info">
                  <i class="fa-solid fa-spinner fa-spin"></i> Memuat data...
                </td>
              </tr>
              <tr v-else-if="listPengajuan.length === 0">
                <td colspan="5" class="td-info">
                  <i class="fa-solid fa-inbox"></i> Tidak ada pengajuan pada
                  periode ini.
                </td>
              </tr>
              <tr v-for="(row, i) in listPengajuan" :key="row.id">
                <td class="c-no">{{ i + 1 }}</td>
                <td class="c-tgl">{{ formatTanggal(row.created_at) }}</td>
                <td class="c-jenis">{{ row.jenis_surat || "-" }}</td>
                <td class="c-nomor">{{ row.nomor_surat || "-" }}</td>
                <td class="c-status">
                  <span class="badge-status" :class="badgeClass(row.status)">{{
                    row.status || "-"
                  }}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="ringkasan-box">
          <strong>Ringkasan Laporan:</strong>
          <ul>
            <li>Total Pengajuan: <b>{{ ringkasan.total }}</b> Berkas</li>
            <li>
              Selesai / Disetujui:
              <b style="color: #059669">{{ ringkasan.selesai }}</b> Berkas
            </li>
            <li>
              Sedang Diproses:
              <b style="color: #d97706">{{ ringkasan.diproses }}</b> Berkas
            </li>
            <li>
              Dikembalikan:
              <b style="color: #dc2626">{{ ringkasan.dikembalikan }}</b> Berkas
            </li>
          </ul>
        </div>

        <div class="ttd-area">
          <div class="ttd-box">
            <p>Mengetahui,<br /><strong>Kepala Desa Macanan</strong></p>
            <div class="ttd-space"></div>
            <p class="ttd-name">( ........................... )</p>
          </div>
          <div class="ttd-box">
            <p>Macanan, {{ tglCetak }}<br /><strong>Sekretaris Desa</strong></p>
            <div class="ttd-space"></div>
            <p class="ttd-name">( ........................... )</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Topbar from "../../components/Topbar.vue";
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

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
  const y = now.getFullYear();
  return [y + 1, y, y - 1, y - 2];
});

const data = ref({});
const loading = ref(true);

const listPengajuan = computed(() => data.value.list?.pengajuan || []);
const ringkasan = computed(() => ({
  total: data.value.pengajuan?.total ?? 0,
  selesai: data.value.pengajuan?.selesai ?? 0,
  diproses: data.value.pengajuan?.diproses ?? 0,
  dikembalikan: data.value.pengajuan?.dikembalikan ?? 0,
}));

const tglCetak = computed(() =>
  new Date().toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  }),
);

const formatTanggal = (iso) => {
  if (!iso) return "-";
  return new Date(iso).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

const badgeClass = (status) => {
  const map = {
    Selesai: "b-selesai",
    Diajukan: "b-diajukan",
    Diverifikasi: "b-verif",
    Dikembalikan: "b-kembali",
  };
  return map[status] || "b-diajukan";
};

const kembali = () => router.push("/sekdes");

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
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: "Tidak bisa memuat data rekap.",
      confirmButtonColor: "#059669",
    });
  } finally {
    loading.value = false;
  }
};

// ===== CETAK (jendela baru, output bersih) =====
const cetak = () => {
  if (listPengajuan.value.length === 0) {
    Swal.fire({
      icon: "info",
      title: "Tidak Ada Data",
      text: "Tidak ada data untuk dicetak pada periode ini.",
      confirmButtonColor: "#059669",
    });
    return;
  }
  const esc = (s) => (s ?? "-").toString().replace(/</g, "&lt;");
  const baris = listPengajuan.value
    .map(
      (row, idx) => `
      <tr>
        <td style="text-align:center">${idx + 1}</td>
        <td>${formatTanggal(row.created_at)}</td>
        <td>${esc(row.jenis_surat)}</td>
        <td>${esc(row.nomor_surat)}</td>
        <td style="text-align:center">${esc(row.status)}</td>
      </tr>`,
    )
    .join("");
  const r = ringkasan.value;
  const inner = `
    <div class="kop">
      <h3>PEMERINTAH KABUPATEN NGANJUK</h3>
      <h3>KECAMATAN LOCERET</h3>
      <h2>KANTOR DESA MACANAN</h2>
      <p>Jl. Panglima Sudirman No. 15, Kode Pos 64471</p>
    </div>
    <hr class="garis" />
    <h4 class="judul">LAPORAN REKAPITULASI PELAYANAN SURAT WARGA<br />
      <small>Periode: ${data.value.nama_bulan || namaBulan[bulan.value - 1]} ${tahun.value}</small></h4>
    <table>
      <thead>
        <tr><th style="width:6%">No</th><th style="width:18%">Tanggal</th><th style="width:34%">Jenis Layanan</th><th style="width:24%">Nomor Surat</th><th style="width:18%">Status</th></tr>
      </thead>
      <tbody>${baris}</tbody>
    </table>
    <div class="ringkasan">
      <strong>Ringkasan:</strong> Total ${r.total} berkas &nbsp;|&nbsp;
      Selesai ${r.selesai} &nbsp;|&nbsp; Diproses ${r.diproses} &nbsp;|&nbsp;
      Dikembalikan ${r.dikembalikan}
    </div>
    <div class="ttd-row">
      <div class="ttd">
        <p>Mengetahui,<br/>Kepala Desa Macanan</p>
        <div class="ttd-space"></div>
        <p class="nama">( ........................... )</p>
      </div>
      <div class="ttd">
        <p>Macanan, ${tglCetak.value}<br/>Sekretaris Desa</p>
        <div class="ttd-space"></div>
        <p class="nama">( ........................... )</p>
      </div>
    </div>`;

  const w = window.open("", "_blank", "width=1000,height=720");
  if (!w) {
    Swal.fire({
      icon: "warning",
      title: "Popup Diblokir",
      text: "Izinkan popup untuk mencetak laporan ini.",
      confirmButtonColor: "#059669",
    });
    return;
  }
  w.document.write(`<!DOCTYPE html><html lang="id"><head><meta charset="utf-8" />
    <title>Rekap Pelayanan ${tahun.value}</title>
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
      table { width: 100%; border-collapse: collapse; font-size: 11pt; table-layout: fixed; }
      th, td { border: 1px solid #000; padding: 6px 8px; vertical-align: top; word-wrap: break-word; }
      thead th { background: #e5e7eb; text-align: center; }
      .ringkasan { margin: 14px 0 0; font-size: 11pt; }
      .ttd-row { display: flex; justify-content: space-between; margin-top: 36px; }
      .ttd { width: 45%; text-align: center; font-size: 11pt; }
      .ttd-space { height: 70px; }
      .ttd .nama { font-weight: bold; }
      @page { size: A4; margin: 15mm; }
    </style></head>
    <body onload="window.focus(); window.print();">${inner}
    <scr` + `ipt>window.onafterprint = function(){ window.close(); };</scr` + `ipt>
    </body></html>`);
  w.document.close();
};

// ===== EXPORT EXCEL (.xls, tanpa dependensi tambahan) =====
const exportExcel = () => {
  if (listPengajuan.value.length === 0) {
    Swal.fire({
      icon: "info",
      title: "Tidak Ada Data",
      text: "Tidak ada data untuk diekspor pada periode ini.",
      confirmButtonColor: "#059669",
    });
    return;
  }
  const esc = (s) => (s ?? "-").toString().replace(/</g, "&lt;");
  const baris = listPengajuan.value
    .map(
      (row, idx) =>
        `<tr><td>${idx + 1}</td><td>${formatTanggal(row.created_at)}</td><td>${esc(
          row.jenis_surat,
        )}</td><td>${esc(row.nomor_surat)}</td><td>${esc(row.status)}</td></tr>`,
    )
    .join("");
  const periode = `${data.value.nama_bulan || namaBulan[bulan.value - 1]} ${tahun.value}`;
  const html = `<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="utf-8" /></head>
    <body>
      <h3>LAPORAN REKAPITULASI PELAYANAN SURAT WARGA</h3>
      <p>Periode: ${periode}</p>
      <table border="1">
        <thead>
          <tr><th>No</th><th>Tanggal</th><th>Jenis Layanan</th><th>Nomor Surat</th><th>Status</th></tr>
        </thead>
        <tbody>${baris}</tbody>
      </table>
    </body></html>`;
  const blob = new Blob([html], { type: "application/vnd.ms-excel" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = `Rekap_Pelayanan_${(data.value.nama_bulan || namaBulan[bulan.value - 1])}_${tahun.value}.xls`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
};

onMounted(() => {
  const token = localStorage.getItem("token");
  if (token) axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  fetchRekap();
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

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

/* HEADER */
.page-header-box {
  background: linear-gradient(135deg, #0f172a, #1e293b);
  border-radius: 18px;
  padding: 24px 28px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  color: #fff;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.25);
}
.header-text h2 {
  margin: 0;
  font-size: 20px;
  font-weight: 800;
  display: flex;
  align-items: center;
  gap: 10px;
}
.header-text h2 i {
  color: #34d399;
}
.header-text p {
  margin: 6px 0 0;
  font-size: 13px;
  opacity: 0.8;
}
.btn-back-glass {
  flex-shrink: 0;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  font-family: inherit;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  transition: 0.2s;
}
.btn-back-glass:hover {
  background: rgba(255, 255, 255, 0.2);
}

/* FILTER BAR */
.filter-bar {
  margin-top: 20px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 16px 18px;
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
}
.filter-inputs {
  display: flex;
  align-items: flex-end;
  gap: 12px;
  flex-wrap: wrap;
}
.filter-item {
  display: flex;
  flex-direction: column;
  gap: 5px;
}
.filter-item label {
  font-size: 12px;
  font-weight: 700;
  color: #475569;
}
.filter-item select {
  padding: 9px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 9px;
  font-family: inherit;
  font-size: 13px;
  color: #0f172a;
  background: #fff;
  min-width: 120px;
  cursor: pointer;
}
.btn-tampilkan,
.btn-excel,
.btn-print {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border: none;
  border-radius: 10px;
  font-family: inherit;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  transition: 0.2s;
}
.btn-tampilkan {
  background: #0f172a;
  color: #fff;
}
.btn-tampilkan:hover {
  background: #1e293b;
}
.aksi-cetak {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}
.btn-excel {
  background: #16a34a;
  color: #fff;
}
.btn-print {
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
}
.btn-excel:hover,
.btn-print:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(5, 150, 105, 0.25);
}

/* PREVIEW LAPORAN */
.rekap-container {
  margin-top: 20px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 30px 34px;
  box-shadow: 0 8px 30px rgba(15, 23, 42, 0.07);
}
.kop-surat {
  text-align: center;
  border-bottom: 3px double #0f172a;
  padding-bottom: 10px;
  line-height: 1.3;
}
.kop-surat h3 {
  margin: 0;
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
}
.kop-surat h2 {
  margin: 2px 0;
  font-size: 19px;
  font-weight: 800;
  letter-spacing: 1px;
  color: #0f172a;
}
.kop-surat p {
  margin: 4px 0 0;
  font-size: 11px;
  color: #475569;
}
.judul-laporan {
  text-align: center;
  margin: 20px 0;
}
.judul-laporan h3 {
  margin: 0;
  font-size: 15px;
  font-weight: 800;
  text-decoration: underline;
  color: #0f172a;
}
.judul-laporan span {
  font-size: 12px;
  color: #64748b;
}

/* TABEL — rapi & lurus */
.table-responsive {
  overflow-x: auto;
}
.table-rekap {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
  font-size: 13px;
}
.table-rekap th,
.table-rekap td {
  border: 1px solid #cbd5e1;
  padding: 9px 12px;
  vertical-align: top;
  word-wrap: break-word;
}
.table-rekap thead th {
  background: #0f172a;
  color: #fff;
  font-weight: 700;
  text-align: center;
  font-size: 12px;
}
.table-rekap tbody tr:nth-child(even) {
  background: #f8fafc;
}
.c-no {
  width: 8%;
  text-align: center;
}
.c-tgl {
  width: 18%;
}
.c-jenis {
  width: 34%;
}
.c-nomor {
  width: 22%;
}
.c-status {
  width: 18%;
  text-align: center;
}
.td-info {
  text-align: center;
  color: #64748b;
  padding: 26px;
  font-weight: 600;
}
.badge-status {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  border: 1px solid transparent;
}
.b-selesai {
  background: #ecfdf5;
  color: #059669;
  border-color: #10b98133;
}
.b-diajukan {
  background: #eff6ff;
  color: #2563eb;
  border-color: #3b82f633;
}
.b-verif {
  background: #fefce8;
  color: #ca8a04;
  border-color: #eab30833;
}
.b-kembali {
  background: #fef2f2;
  color: #dc2626;
  border-color: #ef444433;
}

/* RINGKASAN */
.ringkasan-box {
  margin-top: 22px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 16px 20px;
  font-size: 13px;
  color: #0f172a;
}
.ringkasan-box ul {
  margin: 10px 0 0;
  padding-left: 20px;
  line-height: 1.8;
}

/* TTD */
.ttd-area {
  display: flex;
  justify-content: space-between;
  margin-top: 40px;
  gap: 20px;
}
.ttd-box {
  width: 45%;
  text-align: center;
  font-size: 13px;
  color: #0f172a;
}
.ttd-space {
  height: 70px;
}
.ttd-name {
  font-weight: 700;
}

/* ANIM */
.fade-up {
  animation: fadeUp 0.5s ease both;
}
.delay-1 {
  animation-delay: 0.08s;
}
.delay-2 {
  animation-delay: 0.16s;
}
@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(16px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* RESPONSIVE */
@media (max-width: 768px) {
  .rekap-body {
    padding: 90px 12px 40px;
  }
  .page-header-box {
    flex-direction: column;
    align-items: flex-start;
  }
  .filter-bar {
    flex-direction: column;
    align-items: stretch;
  }
  .filter-inputs,
  .aksi-cetak {
    width: 100%;
  }
  .filter-item select {
    width: 100%;
  }
  .btn-tampilkan,
  .btn-excel,
  .btn-print {
    flex: 1;
    justify-content: center;
  }
  .rekap-container {
    padding: 20px 16px;
  }
  .ttd-area {
    flex-direction: column;
    gap: 30px;
  }
  .ttd-box {
    width: 100%;
  }
}

@media (max-width: 400px) {
  .kop-surat h2 {
    font-size: 16px;
  }
  .table-rekap {
    font-size: 11px;
  }
  .table-rekap th,
  .table-rekap td {
    padding: 6px 7px;
  }
}
</style>
