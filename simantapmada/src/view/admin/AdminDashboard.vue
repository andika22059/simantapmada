<template>
  <Topbar />
  <div class="dashboard-body">
    <div class="bg-blob blob-1"></div>
    <div class="bg-blob blob-2"></div>
    <div class="bg-blob blob-3"></div>

    <div class="main-content">
      <div v-if="route.path === '/admin' || route.path === '/admin/'">
        <div class="admin-hero fade-in-up">
          <div class="greet-content">
            <div class="greet-text">
              <h1>{{ sapaan }}, {{ namaUser }}! 👋</h1>
              <p>
                Pusat kendali informasi. Pantau dan kelola seluruh arsip serta
                aset desa.
              </p>
            </div>
            <div
              style="
                display: flex;
                gap: 15px;
                align-items: center;
                margin-top: 20px;
                flex-wrap: wrap;
              "
            >
              <div class="greet-date" style="margin-top: 0">
                <i class="fa-regular fa-calendar-days"></i>
                <span>{{ tanggalHariIni }}</span>
              </div>
            </div>
          </div>
          <div class="hero-bg-icon">
            <i class="fa-solid fa-server float-anim"></i>
          </div>
        </div>

        <div class="section-head fade-in-up delay-1">
          <h2><i class="fa-solid fa-bolt text-primary"></i> Pintasan Cepat</h2>
        </div>

        <div class="shortcut-area fade-in-up delay-2">
          <a
            href="#"
            @click.prevent="router.push('/admin/pelayanan/tambah')"
            class="btn-shortcut s-orange"
          >
            <div class="icon-box"><i class="fa-solid fa-plus"></i></div>
            <div class="text-box"><small>Layanan</small>Buat Pengajuan</div>
            <div class="hover-arrow">
              <i class="fa-solid fa-chevron-right"></i>
            </div>
          </a>
          <a
            href="#"
            @click.prevent="router.push('/admin/surat-masuk/tambah')"
            class="btn-shortcut s-blue"
          >
            <div class="icon-box"><i class="fa-solid fa-inbox"></i></div>
            <div class="text-box"><small>Arsip</small>Input Surat Masuk</div>
            <div class="hover-arrow">
              <i class="fa-solid fa-chevron-right"></i>
            </div>
          </a>
          <a
            href="#"
            @click.prevent="router.push('/admin/surat-keluar/tambah')"
            class="btn-shortcut s-green"
          >
            <div class="icon-box"><i class="fa-solid fa-paper-plane"></i></div>
            <div class="text-box"><small>Arsip</small>Catat Surat Keluar</div>
            <div class="hover-arrow">
              <i class="fa-solid fa-chevron-right"></i>
            </div>
          </a>
          <a
            href="#"
            @click.prevent="router.push('/admin/aset/tambah')"
            class="btn-shortcut s-purple"
          >
            <div class="icon-box"><i class="fa-solid fa-box"></i></div>
            <div class="text-box"><small>Aset</small>Input Barang</div>
            <div class="hover-arrow">
              <i class="fa-solid fa-chevron-right"></i>
            </div>
          </a>
          <a
            href="#"
            @click.prevent="router.push('/admin/pengaturan')"
            class="btn-shortcut s-navy"
          >
            <div class="icon-box"><i class="fa-solid fa-gear"></i></div>
            <div class="text-box"><small>Surat</small>Pengaturan &amp; TTD</div>
            <div class="hover-arrow">
              <i class="fa-solid fa-chevron-right"></i>
            </div>
          </a>
        </div>

        <div
          class="section-head fade-in-up delay-3"
          style="
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
          "
        >
          <h2>
            <i class="fa-solid fa-chart-pie icon-green"></i> Ringkasan Data
          </h2>
          <a href="#" @click.prevent="bukaLaporan" class="btn-laporan">
            <i class="fa-solid fa-chart-column"></i> Buka Laporan Analitik
          </a>
        </div>

        <div class="dash-cards fade-in-up delay-4">
          <div class="dash-card c-orange">
            <a
              href="#"
              @click.prevent="router.push('/admin/pelayanan/list')"
              class="pulse-ring"
              style="
                width: auto;
                padding: 0 12px;
                border-radius: 20px;
                font-size: 11px;
                text-decoration: none;
                z-index: 10;
              "
            >
              {{ stat.pengajuan_selesai }} Sudah TTD
            </a>
            <a
              href="#"
              @click.prevent="router.push('/admin/pelayanan')"
              class="card-main-link"
            >
              <div class="card-icon animate-float">
                <i class="fa-solid fa-file-signature"></i>
              </div>
              <h3>{{ stat.pengajuan_diproses }}</h3>
              <span>Sedang Diproses</span>
              <div class="card-bg-icon">
                <i class="fa-solid fa-file-signature"></i>
              </div>
            </a>
          </div>

          <div class="dash-card c-blue">
            <a
              href="#"
              @click.prevent="router.push('/admin/surat-masuk')"
              class="card-main-link"
            >
              <div class="card-icon animate-float">
                <i class="fa-solid fa-inbox"></i>
              </div>
              <h3>{{ stat.surat_masuk }}</h3>
              <span>Surat Masuk</span>
              <div class="card-bg-icon"><i class="fa-solid fa-inbox"></i></div>
            </a>
          </div>

          <div class="dash-card c-green">
            <a
              href="#"
              @click.prevent="router.push('/admin/surat-keluar')"
              class="card-main-link"
            >
              <div class="card-icon animate-float">
                <i class="fa-solid fa-paper-plane"></i>
              </div>
              <h3>{{ stat.surat_keluar }}</h3>
              <span>Surat Keluar</span>
              <div class="card-bg-icon">
                <i class="fa-solid fa-paper-plane"></i>
              </div>
            </a>
          </div>

          <div class="dash-card c-teal-full">
            <a
              href="#"
              @click.prevent="router.push('/admin/arsip')"
              class="card-main-link"
            >
              <div class="card-icon animate-float">
                <i class="fa-solid fa-box-archive"></i>
              </div>
              <h3>{{ stat.arsip }}</h3>
              <span>Arsip Digital</span>
              <div class="card-bg-icon">
                <i class="fa-solid fa-box-archive"></i>
              </div>
            </a>
          </div>

          <div class="dash-card c-purple">
            <a
              href="#"
              @click.prevent="router.push('/admin/aset')"
              class="card-main-link"
            >
              <div class="card-icon animate-float">
                <i class="fa-solid fa-warehouse"></i>
              </div>
              <h3>{{ stat.aset }}</h3>
              <span>Inventaris Aset</span>
              <div class="card-bg-icon">
                <i class="fa-solid fa-warehouse"></i>
              </div>
            </a>
          </div>
        </div>

        <div class="section-title fade-in-up delay-4">
          <h3 class="title-online">
            <i
              class="fa-solid fa-mobile-screen-button"
              style="color: #3b82f6"
            ></i>
            Layanan Mandiri Warga (Online)
          </h3>
        </div>
        <div class="online-cards fade-in-up delay-4">
          <div class="card-online card-coming-soon">
            <div class="o-icon">
              <i class="fa-solid fa-envelope-open-text"></i>
            </div>
            <div>
              <h3>Cek Antrean</h3>
              <span>Segera Hadir...</span>
            </div>
            <div class="label-coming-soon">COMING SOON</div>
          </div>
          <div class="card-online card-coming-soon">
            <div class="o-icon"><i class="fa-solid fa-boxes-packing"></i></div>
            <div>
              <h3>Fitur Baru</h3>
              <span>Segera Hadir...</span>
            </div>
            <div class="label-coming-soon">COMING SOON</div>
          </div>
          <div class="card-online card-coming-soon">
            <div class="o-icon"><i class="fa-solid fa-bullhorn"></i></div>
            <div>
              <h3>Lapor Pak Kades</h3>
              <span>Segera Hadir...</span>
            </div>
            <div class="label-coming-soon">COMING SOON</div>
          </div>
        </div>

        <div class="section-head fade-in-up delay-5 mt-50">
          <h2>
            <i class="fa-solid fa-clock-rotate-left text-warning"></i> Log
            Aktivitas Terbaru
          </h2>
        </div>
        <div class="table-wrapper fade-in-up delay-5">
          <div class="table-scroll custom-scrollbar">
            <table class="custom-table animated-table">
              <thead>
                <tr>
                  <th class="col-time">Waktu</th>
                  <th class="col-category">Kategori</th>
                  <th>Keterangan</th>
                  <th class="col-status">Oleh</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loadingAktivitas">
                  <td
                    colspan="4"
                    style="text-align: center; padding: 30px; color: #94a3b8"
                  >
                    <i class="fa-solid fa-spinner fa-spin"></i> Memuat
                    aktivitas...
                  </td>
                </tr>

                <tr v-else-if="aktivitas.length === 0">
                  <td
                    colspan="4"
                    style="text-align: center; padding: 30px; color: #94a3b8"
                  >
                    <i class="fa-solid fa-inbox"></i> Belum ada aktivitas
                    tercatat.
                  </td>
                </tr>

                <tr v-for="log in aktivitas" :key="log.id">
                  <td>
                    <div class="date-box">
                      <span class="d-date">{{
                        formatTanggal(log.created_at)
                      }}</span>
                      <span class="d-time"
                        ><i class="fa-regular fa-clock"></i>
                        {{ formatJam(log.created_at) }}</span
                      >
                    </div>
                  </td>
                  <td>
                    <span class="badge" :class="badgeKategori(log.modul)">{{
                      log.modul
                    }}</span>
                  </td>
                  <td class="fw-500">
                    {{ log.deskripsi }}
                    <template v-if="log.referensi"
                      ><br /><small>No: {{ log.referensi }}</small></template
                    >
                  </td>
                  <td>
                    <span class="status-text">{{ log.pelaku }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="modal-overlay" :class="{ active: showModalSurat }">
          <div class="modal-box-large zoom-in">
            <div class="modal-header-simple">
              <h3>Detail Surat</h3>
              <button class="btn-close-simple" @click="showModalSurat = false">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>
            <div class="modal-body-split">
              <div class="split-left">
                <div class="file-viewer">
                  <i class="fa-solid fa-circle-notch fa-spin loader-large"></i>
                </div>
              </div>
              <div class="split-right">
                <div class="info-group">
                  <label>Asal Surat</label>
                  <p>Kecamatan</p>
                </div>
                <div class="info-group">
                  <label>Nomor Surat</label>
                  <p class="text-mono-blue">123/KEC/2026</p>
                </div>
                <div class="info-group">
                  <label>Perihal</label>
                  <p>Undangan Rapat</p>
                </div>
                <div class="info-group">
                  <label>Tanggal Arsip</label>
                  <p>14 Jun 2026</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-overlay" :class="{ active: showModalPelayanan }">
          <div class="modal-box-simple zoom-in">
            <div class="modal-header-simple">
              <h3>
                <i class="fa-solid fa-list-check icon-green"></i> Status
                Pengajuan
              </h3>
              <button
                class="btn-close-simple"
                @click="showModalPelayanan = false"
              >
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>
            <div class="modal-body-simple-grid">
              <div class="info-card">
                <div class="card-head">
                  <i class="fa-solid fa-user"></i> Data Pemohon
                </div>
                <div class="d-row">
                  <span class="d-lbl">Kode</span
                  ><span class="d-val text-mono-green">P-20260614</span>
                </div>
                <div class="d-row">
                  <span class="d-lbl">Nama</span
                  ><span class="d-val">SITI AMINAH</span>
                </div>
                <div class="d-row">
                  <span class="d-lbl">NIK</span
                  ><span class="d-val">3500000000000001</span>
                </div>
                <div class="d-row row-last">
                  <span class="d-lbl">Alamat</span
                  ><span class="d-val">Jl. Desa Macanan</span>
                </div>
              </div>
              <div class="info-card">
                <div class="card-head">
                  <i class="fa-solid fa-bars-staggered"></i> Timeline Proses
                </div>
                <div class="timeline-box">
                  <div class="track-item active">
                    <div class="track-line"></div>
                    <div class="track-icon">
                      <i class="fa-solid fa-inbox"></i>
                    </div>
                    <div class="track-content">
                      <h5>Pengajuan Masuk</h5>
                      <p>Data diterima sistem.</p>
                    </div>
                  </div>
                  <div class="track-item active">
                    <div class="track-line"></div>
                    <div class="track-icon">
                      <i class="fa-solid fa-file-circle-check"></i>
                    </div>
                    <div class="track-content">
                      <h5>Verifikasi Berkas</h5>
                      <p>Cek oleh Sekretaris Desa.</p>
                    </div>
                  </div>
                  <div class="track-item">
                    <div class="track-icon">
                      <i class="fa-solid fa-signature"></i>
                    </div>
                    <div class="track-content">
                      <h5>Selesai & TTD</h5>
                      <p>Surat diterbitkan.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <RouterView />
    </div>
  </div>
</template>

<script setup>
import Topbar from "../../components/Topbar.vue";
import { ref, computed, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const router = useRouter();
const route = useRoute();

const showModalSurat = ref(false);
const showModalPelayanan = ref(false);

// 🔥 Ambil nama user yang login dari localStorage
const userData = ref(null);
const raw = localStorage.getItem("user");
if (raw) {
  try {
    userData.value = JSON.parse(raw);
  } catch (e) {
    userData.value = null;
  }
}
const namaUser = computed(() => userData.value?.nama_lengkap || "Pengguna");

// Sapaan sesuai jam
const sapaan = computed(() => {
  const jam = new Date().getHours();
  if (jam >= 5 && jam < 11) return "Selamat Pagi";
  if (jam >= 11 && jam < 15) return "Selamat Siang";
  if (jam >= 15 && jam < 18) return "Selamat Sore";
  return "Selamat Malam";
});

// Tanggal hari ini (format Indonesia)
const tanggalHariIni = computed(() => {
  return new Date().toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
});

// ==========================================
// 🔥 STATISTIK KARTU (dari API)
// ==========================================
const stat = ref({
  pengajuan_diproses: 0,
  pengajuan_selesai: 0,
  surat_masuk: 0,
  surat_keluar: 0,
  arsip: 0,
  aset: 0,
});

const fetchStatistik = async () => {
  try {
    const res = await axios.get(
      `${import.meta.env.VITE_API_URL}/api/dashboard/statistik`,
    );
    if (res.data?.data) stat.value = res.data.data;
  } catch (e) {
    console.error("Gagal memuat statistik dashboard:", e);
  }
};

// ==========================================
// 🔥 LOG AKTIVITAS TERBARU (dari API)
// ==========================================
const aktivitas = ref([]);
const loadingAktivitas = ref(true);

const fetchAktivitas = async () => {
  loadingAktivitas.value = true;
  try {
    const res = await axios.get(
      `${import.meta.env.VITE_API_URL}/api/dashboard/aktivitas`,
    );
    aktivitas.value = res.data?.data || [];
  } catch (e) {
    console.error("Gagal memuat aktivitas:", e);
    aktivitas.value = [];
  } finally {
    loadingAktivitas.value = false;
  }
};

onMounted(() => {
  fetchStatistik();
  fetchAktivitas();
});

// Helper format tanggal & jam untuk log
const formatTanggal = (iso) => {
  if (!iso) return "-";
  return new Date(iso).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};
const formatJam = (iso) => {
  if (!iso) return "";
  return (
    new Date(iso).toLocaleTimeString("id-ID", {
      hour: "2-digit",
      minute: "2-digit",
    }) + " WIB"
  );
};

// Warna badge per modul
const badgeKategori = (modul) => {
  const map = {
    "Surat Masuk": "badge-soft-blue",
    "Surat Keluar": "badge-soft-green",
    Arsip: "badge-soft-teal",
    Aset: "badge-soft-purple",
    Pelayanan: "badge-soft-orange",
  };
  return map[modul] || "badge-soft-blue";
};

// Tombol "Buka Laporan" → rekap lengkap semua modul
const bukaLaporan = () => router.push("/laporan");
</script>

<style scoped src="../../assets/css/admin/dashboard_admin.css"></style>
