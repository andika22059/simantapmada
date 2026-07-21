<template>
  <Topbar />
  <div class="sekdes-body">
    <div class="sekdes-wrapper">
      <!-- HEADER NAVY DOTTED -->
      <div class="page-header-box fade-up">
        <div class="header-text">
          <h2>
            <i class="fa-solid fa-user-shield"></i> Dashboard Sekretaris Desa
          </h2>
          <p>
            {{ sapaan }}, <strong>{{ namaUser }}</strong
            >. Fokus pada validasi pengajuan dan disposisi surat hari ini.
          </p>
        </div>
        <div class="header-actions">
          <button class="btn-glass" @click="router.push('/sekdes/rekap')">
            <i class="fa-solid fa-file-invoice"></i> Rekap Laporan
          </button>
          <button
            class="btn-emerald"
            @click="router.push('/sekdes/verifikasi')"
          >
            <i class="fa-solid fa-clipboard-check"></i> Mulai Verifikasi
          </button>
        </div>
      </div>

      <!-- KARTU STATISTIK -->
      <div class="stat-cards fade-up delay-1">
        <a
          href="#"
          @click.prevent="router.push('/sekdes/verifikasi')"
          class="card-stat"
        >
          <div class="cs-icon icon-emerald">
            <i class="fa-solid fa-folder-open"></i>
          </div>
          <div class="cs-info">
            <h3><AngkaNaik :nilai="stat.menunggu_verifikasi" /></h3>
            <span>Verifikasi Berkas</span>
          </div>
          <div class="cs-arrow"><i class="fa-solid fa-chevron-right"></i></div>
        </a>

        <a
          href="#"
          @click.prevent="router.push('/sekdes/disposisi')"
          class="card-stat"
        >
          <div class="cs-icon icon-teal">
            <i class="fa-solid fa-envelope-open-text"></i>
          </div>
          <div class="cs-info">
            <h3><AngkaNaik :nilai="stat.menunggu_disposisi" /></h3>
            <span>Disposisi Surat</span>
          </div>
          <div class="cs-arrow"><i class="fa-solid fa-chevron-right"></i></div>
        </a>

        <a
          href="#"
          @click.prevent="router.push('/sekdes/rekap')"
          class="card-stat"
        >
          <div class="cs-icon icon-navy">
            <i class="fa-solid fa-chart-column"></i>
          </div>
          <div class="cs-info">
            <h3><AngkaNaik :nilai="stat.selesai" /></h3>
            <span>Rekap Bulanan</span>
          </div>
          <div class="cs-arrow"><i class="fa-solid fa-chevron-right"></i></div>
        </a>
      </div>

      <div class="dashboard-grid fade-up delay-2">
        <!-- KOLOM KIRI -->
        <div class="grid-left">
          <!-- ANTREAN VERIFIKASI WARGA -->
          <div class="content-box">
            <div class="box-header">
              <h3>
                <i class="fa-solid fa-user-check icon-emerald-text"></i>
                Antrean Verifikasi Berkas
              </h3>
              <a href="#" @click.prevent="router.push('/sekdes/verifikasi')"
                >Lihat Semua</a
              >
            </div>
            <div class="box-body">
              <table class="table-modern">
                <thead>
                  <tr>
                    <th>Tanggal</th>
                    <th>Jenis Surat</th>
                    <th>Status</th>
                    <th class="ta-right">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="loadingPengajuan">
                    <tr v-for="n in 4" :key="'skp-' + n" class="skeleton-row">
                      <td v-for="c in 4" :key="c"><span class="skeleton-bar"></span></td>
                    </tr>
                  </template>
                  <tr v-else-if="antreanVerifikasi.length === 0">
                    <td colspan="4" class="td-empty">
                      <i class="fa-solid fa-mug-hot"></i> Tidak ada antrean
                      validasi.
                    </td>
                  </tr>
                  <tr v-for="row in antreanVerifikasi" :key="row.id">
                    <td>{{ formatTanggal(row.created_at) }}</td>
                    <td>
                      <span class="badge-layanan">{{ row.jenis_surat }}</span>
                    </td>
                    <td>
                      <span class="badge-status badge-diajukan">{{
                        row.status
                      }}</span>
                    </td>
                    <td class="ta-right">
                      <button
                        class="btn-sm btn-outline-emerald"
                        @click="router.push('/sekdes/verifikasi')"
                      >
                        Proses
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- ANTREAN DISPOSISI SURAT MASUK -->
          <div class="content-box" style="margin-top: 24px">
            <div class="box-header">
              <h3>
                <i class="fa-solid fa-file-import icon-teal-text"></i>
                Surat Masuk (Menunggu Disposisi)
              </h3>
              <a href="#" @click.prevent="router.push('/sekdes/disposisi')"
                >Lihat Semua</a
              >
            </div>
            <div class="box-body">
              <table class="table-modern">
                <thead>
                  <tr>
                    <th>Tgl Terima</th>
                    <th>Pengirim</th>
                    <th>Perihal</th>
                    <th class="ta-right">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="loadingSurat">
                    <tr v-for="n in 4" :key="'sks-' + n" class="skeleton-row">
                      <td v-for="c in 4" :key="c"><span class="skeleton-bar"></span></td>
                    </tr>
                  </template>
                  <tr v-else-if="antreanDisposisi.length === 0">
                    <td colspan="4" class="td-empty">
                      <i class="fa-solid fa-inbox"></i> Tidak ada surat menunggu
                      disposisi.
                    </td>
                  </tr>
                  <tr v-for="row in antreanDisposisi" :key="row.id">
                    <td>
                      {{ formatTanggal(row.tanggal_terima || row.created_at) }}
                    </td>
                    <td>
                      <strong>{{ row.pengirim }}</strong>
                    </td>
                    <td class="td-perihal">{{ row.perihal }}</td>
                    <td class="ta-right">
                      <button
                        class="btn-sm btn-outline-teal"
                        @click="router.push('/sekdes/disposisi')"
                      >
                        Disposisi
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- KOLOM KANAN: PANEL NOTIFIKASI -->
        <div class="grid-right">
          <div class="notification-panel">
            <div class="panel-header">
              <h3><i class="fa-solid fa-bell"></i> Peringatan Sistem</h3>
            </div>
            <div class="panel-body">
              <ul class="timeline">
                <li
                  v-if="stat.menunggu_verifikasi > 0"
                  class="timeline-item unread"
                >
                  <div class="tl-icon icon-emerald">
                    <i class="fa-solid fa-user-clock"></i>
                  </div>
                  <div class="tl-content">
                    <strong>Pengajuan Menunggu</strong>
                    <p>
                      Ada {{ stat.menunggu_verifikasi }} pengajuan warga yang
                      perlu diverifikasi.
                    </p>
                    <span class="tl-time">Penting</span>
                  </div>
                </li>

                <li
                  v-if="stat.menunggu_disposisi > 0"
                  class="timeline-item unread"
                >
                  <div class="tl-icon icon-teal">
                    <i class="fa-solid fa-envelope-open-text"></i>
                  </div>
                  <div class="tl-content">
                    <strong>Surat Perlu Disposisi</strong>
                    <p>
                      Ada {{ stat.menunggu_disposisi }} surat masuk yang perlu
                      didisposisi.
                    </p>
                    <span class="tl-time">Penting</span>
                  </div>
                </li>

                <li v-if="stat.dikembalikan > 0" class="timeline-item">
                  <div class="tl-icon icon-amber">
                    <i class="fa-solid fa-rotate-left"></i>
                  </div>
                  <div class="tl-content">
                    <strong>Pengajuan Dikembalikan</strong>
                    <p>
                      {{ stat.dikembalikan }} pengajuan dikembalikan & menunggu
                      perbaikan warga.
                    </p>
                  </div>
                </li>

                <li class="timeline-item">
                  <div class="tl-icon icon-navy">
                    <i class="fa-solid fa-circle-info"></i>
                  </div>
                  <div class="tl-content">
                    <strong>Sistem SIMANTAP</strong>
                    <p>
                      Gunakan tombol Rekap Laporan untuk mencetak data bulanan.
                    </p>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Topbar from "../../components/Topbar.vue";
import AngkaNaik from "../../components/AngkaNaik.vue";
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const router = useRouter();
const BASE = `${import.meta.env.VITE_API_URL}/api`;

// User dari localStorage
const userData = ref(null);
const namaUser = computed(() => userData.value?.nama_lengkap || "Sekretaris");
const sapaan = computed(() => {
  const jam = new Date().getHours();
  if (jam >= 5 && jam < 11) return "Selamat pagi";
  if (jam >= 11 && jam < 15) return "Selamat siang";
  if (jam >= 15 && jam < 18) return "Selamat sore";
  return "Selamat malam";
});

// Statistik
const stat = ref({
  menunggu_verifikasi: 0,
  sudah_diverifikasi: 0,
  dikembalikan: 0,
  selesai: 0,
  menunggu_disposisi: 0,
  sudah_disposisi: 0,
});

// Antrean
const antreanVerifikasi = ref([]);
const antreanDisposisi = ref([]);
const loadingPengajuan = ref(true);
const loadingSurat = ref(true);

const fetchStatistik = async () => {
  try {
    const res = await axios.get(`${BASE}/dashboard/statistik-sekdes`);
    if (res.data?.data) stat.value = res.data.data;
  } catch (e) {
    console.error("Gagal memuat statistik sekdes:", e);
  }
};

const fetchAntreanVerifikasi = async () => {
  loadingPengajuan.value = true;
  try {
    const res = await axios.get(`${BASE}/pelayanan`);
    const semua = res.data?.data || [];
    // Hanya yang berstatus "Diajukan" (perlu verifikasi), ambil 3 teratas
    antreanVerifikasi.value = semua
      .filter((p) => p.status === "Diajukan")
      .slice(0, 3);
  } catch (e) {
    console.error("Gagal memuat antrean verifikasi:", e);
    antreanVerifikasi.value = [];
  } finally {
    loadingPengajuan.value = false;
  }
};

const fetchAntreanDisposisi = async () => {
  loadingSurat.value = true;
  try {
    const res = await axios.get(`${BASE}/surat-masuk`);
    const semua = res.data?.data || [];
    // Hanya yang status disposisinya "Belum", ambil 3 teratas
    antreanDisposisi.value = semua
      .filter((s) => (s.status_disposisi || "Belum") === "Belum")
      .slice(0, 3);
  } catch (e) {
    console.error("Gagal memuat antrean disposisi:", e);
    antreanDisposisi.value = [];
  } finally {
    loadingSurat.value = false;
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

const infoSegera = () => {
  Swal.fire({
    icon: "info",
    title: "Segera Hadir",
    text: "Fitur Rekap Laporan sedang disiapkan.",
    confirmButtonColor: "#059669",
  });
};

onMounted(() => {
  const raw = localStorage.getItem("user");
  if (raw) {
    try {
      userData.value = JSON.parse(raw);
    } catch (e) {
      userData.value = null;
    }
  }
  const token = localStorage.getItem("token");
  if (token) axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

  fetchStatistik();
  fetchAntreanVerifikasi();
  fetchAntreanDisposisi();
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.sekdes-body {
  font-family: "Plus Jakarta Sans", sans-serif;
  background: var(--bg-page, #f8fafc);
  min-height: 100vh;
  padding: 100px 20px 50px;
  box-sizing: border-box;
}
.sekdes-wrapper {
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

/* HEADER NAVY DOTTED */
.page-header-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 15px;
  margin-bottom: 28px;
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
.header-text p strong {
  color: #e2e8f0;
}
.header-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}
.btn-glass,
.btn-emerald {
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
.btn-glass {
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.18);
}
.btn-glass:hover {
  background: #fff;
  color: #1e293b;
  transform: translateY(-2px);
}
.btn-emerald {
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
  box-shadow: 0 8px 20px rgba(5, 150, 105, 0.3);
}
.btn-emerald:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 25px rgba(5, 150, 105, 0.4);
}

/* KARTU STATISTIK */
.stat-cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-bottom: 28px;
}
.card-stat {
  background: #fff;
  border: 1px solid var(--border, #e2e8f0);
  border-radius: 18px;
  padding: 22px;
  display: flex;
  align-items: center;
  gap: 16px;
  text-decoration: none;
  transition:
    transform 0.3s cubic-bezier(0.16, 1, 0.3, 1),
    box-shadow 0.3s;
  position: relative;
  overflow: hidden;
}
.card-stat:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(15, 23, 42, 0.08);
}
.cs-icon {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  flex-shrink: 0;
}
.icon-emerald {
  background: #ecfdf5;
  color: #059669;
}
.icon-teal {
  background: #f0fdfa;
  color: #0f766e;
}
.icon-navy {
  background: #f1f5f9;
  color: #1e293b;
}
.icon-amber {
  background: #fffbeb;
  color: #d97706;
}
.cs-info h3 {
  margin: 0;
  font-size: 28px;
  font-weight: 800;
  color: var(--navy, #0f172a);
  line-height: 1;
}
.cs-info span {
  font-size: 13px;
  color: var(--text-muted, #64748b);
  font-weight: 600;
}
.cs-arrow {
  margin-left: auto;
  color: #cbd5e1;
  transition: 0.3s;
}
.card-stat:hover .cs-arrow {
  color: var(--primary, #059669);
  transform: translateX(4px);
}

/* GRID */
.dashboard-grid {
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 24px;
  align-items: start;
}

/* CONTENT BOX */
.content-box {
  background: #fff;
  border: 1px solid var(--border, #e2e8f0);
  border-radius: 18px;
  overflow: hidden;
}
.box-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px 24px;
  border-bottom: 1px solid #f1f5f9;
}
.box-header h3 {
  margin: 0;
  font-size: 15px;
  font-weight: 800;
  color: var(--navy, #0f172a);
  display: flex;
  align-items: center;
  gap: 10px;
}
.icon-emerald-text {
  color: #059669;
}
.icon-teal-text {
  color: #0f766e;
}
.box-header a {
  font-size: 13px;
  color: var(--primary, #059669);
  font-weight: 700;
  text-decoration: none;
}
.box-header a:hover {
  text-decoration: underline;
}
.box-body {
  padding: 0;
}

/* TABLE */
.table-modern {
  width: 100%;
  border-collapse: collapse;
}
.table-modern th {
  text-align: left;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--text-muted, #64748b);
  font-weight: 800;
  padding: 14px 24px;
  background: #f8fafc;
}
.table-modern td {
  padding: 14px 24px;
  font-size: 14px;
  color: var(--text-body, #334155);
  border-bottom: 1px solid #f1f5f9;
}
.table-modern tbody tr:last-child td {
  border-bottom: none;
}
.table-modern tbody tr {
  transition: background 0.2s;
}
.table-modern tbody tr:hover td {
  background: var(--primary-soft, #ecfdf5);
}
.ta-right {
  text-align: right;
}
.td-perihal {
  max-width: 220px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.td-empty {
  text-align: center;
  padding: 30px;
  color: #94a3b8;
  font-weight: 500;
}

.badge-layanan {
  background: #ecfdf5;
  color: #047857;
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 12px;
  font-weight: 700;
}
.badge-status {
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 12px;
  font-weight: 700;
}
.badge-diajukan {
  background: #fffbeb;
  color: #d97706;
}

.btn-sm {
  padding: 7px 16px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  border: 1px solid;
  background: #fff;
  transition: 0.2s;
  font-family: inherit;
}
.btn-outline-emerald {
  border-color: #059669;
  color: #059669;
}
.btn-outline-emerald:hover {
  background: #059669;
  color: #fff;
}
.btn-outline-teal {
  border-color: #0f766e;
  color: #0f766e;
}
.btn-outline-teal:hover {
  background: #0f766e;
  color: #fff;
}

/* PANEL NOTIFIKASI */
.notification-panel {
  background: #fff;
  border: 1px solid var(--border, #e2e8f0);
  border-radius: 18px;
  overflow: hidden;
  position: sticky;
  top: 100px;
}
.panel-header {
  padding: 18px 24px;
  background: linear-gradient(135deg, #1e293b, #0f172a);
  color: #fff;
}
.panel-header h3 {
  margin: 0;
  font-size: 15px;
  font-weight: 800;
  display: flex;
  align-items: center;
  gap: 10px;
}
.panel-header h3 i {
  color: #34d399;
}
.panel-body {
  padding: 18px;
}
.timeline {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.timeline-item {
  display: flex;
  gap: 12px;
  padding: 14px;
  border-radius: 12px;
  background: #f8fafc;
  border: 1px solid #f1f5f9;
}
.timeline-item.unread {
  background: #fff;
  border-color: var(--primary-border, #a7f3d0);
  box-shadow: 0 4px 12px rgba(5, 150, 105, 0.06);
}
.tl-icon {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  flex-shrink: 0;
}
.tl-content strong {
  font-size: 13px;
  color: var(--navy, #0f172a);
  display: block;
  margin-bottom: 3px;
}
.tl-content p {
  margin: 0;
  font-size: 12px;
  color: var(--text-muted, #64748b);
  line-height: 1.5;
}
.tl-time {
  display: inline-block;
  margin-top: 6px;
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
  color: #d97706;
  background: #fffbeb;
  padding: 2px 8px;
  border-radius: 50px;
}

/* RESPONSIVE */
@media (max-width: 1100px) {
  .header-actions {
    flex-wrap: wrap;
    gap: 10px;
  }
}

@media (max-width: 980px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
  .notification-panel {
    position: static;
  }
}
@media (max-width: 768px) {
  .stat-cards {
    grid-template-columns: 1fr;
  }
  .page-header-box {
    flex-direction: column;
    align-items: flex-start;
  }
  .header-actions {
    width: 100%;
  }
  .btn-glass,
  .btn-emerald {
    flex: 1;
    justify-content: center;
  }
}
@media (max-width: 480px) {
  .sekdes-body {
    padding: 90px 14px 30px;
  }
  .header-text h2 {
    font-size: 20px;
  }
  .table-modern th,
  .table-modern td {
    padding: 12px 16px;
    font-size: 13px;
  }
  .td-perihal {
    max-width: 120px;
  }
}

@media (max-width: 360px) {
  .header-text h2 {
    font-size: 17px;
  }
}

/* ===== Skeleton loading ===== */
.skeleton-row td { padding: 14px 16px; }
.skeleton-bar {
  display: block; height: 14px; border-radius: 7px;
  background: linear-gradient(90deg, #eef2f7 25%, #e2e8f0 37%, #eef2f7 63%);
  background-size: 400% 100%; animation: skeletonShimmer 1.4s ease infinite;
}
@keyframes skeletonShimmer { 0% { background-position: 100% 50%; } 100% { background-position: 0 50%; } }
</style>
