<template>
  <div class="vue-topbar-wrapper">
    <nav class="navbar" :class="{ scrolled: isScrolled }">
      <div class="container-nav">
        <div class="nav-brand" @click="router.push(getDashboardLink())">
          <div class="logo-wrapper">
            <img
              src="../assets/img/logoSiMANTAPMADA.png"
              alt="Logo SiMANTAP MADA"
              class="brand-logo-img"
            />
          </div>
          <div class="brand-text">
            <span class="main-title">SiMANTAP</span>
            <span class="sub-title">MADA</span>
          </div>
        </div>

        <ul
          class="nav-links"
          :class="{ active: isMobileMenuOpen }"
          @click="handleMenuClick"
        >
          <!-- HEADER PROFIL (hanya tampil di drawer HP) -->
          <li class="drawer-profile" @click="router.push('/profil')">
            <div class="drawer-avatar">
              <img v-if="fotoUser" :src="fotoUser" alt="Foto" />
              <i v-else class="fa-solid fa-user-tie"></i>
            </div>
            <div class="drawer-profile-meta">
              <span class="drawer-name">{{ namaUser }}</span>
              <span class="drawer-role" :class="roleUser">{{ roleLabel }}</span>
            </div>
            <i class="fa-solid fa-chevron-right drawer-profile-arrow"></i>
          </li>
          <li class="drawer-divider"></li>

          <!-- MENU DINAMIS PER ROLE -->
          <template v-for="(item, idx) in menuRole" :key="idx">
            <!-- Menu biasa (link langsung) -->
            <li v-if="!item.children">
              <router-link
                :to="item.to"
                class="menu-item"
                :exact-active-class="item.exact ? 'active-link' : ''"
                :active-class="item.exact ? '' : 'active-link'"
              >
                <i :class="item.icon"></i> {{ item.label }}
              </router-link>
            </li>

            <!-- Menu dropdown (punya children) -->
            <li v-else class="dropdown">
              <a
                href="#"
                class="menu-item dropdown-toggle"
                @click.prevent="toggleMobileDropdown($event)"
              >
                <i :class="item.icon"></i> {{ item.label }}
                <i class="fa-solid fa-chevron-down arrow-icon"></i>
              </a>
              <ul class="dropdown-menu">
                <li v-for="(sub, si) in item.children" :key="si">
                  <router-link :to="sub.to">
                    <i :class="sub.icon"></i> {{ sub.label }}
                  </router-link>
                </li>
              </ul>
            </li>
          </template>

          <!-- FOOTER LOGOUT (hanya tampil di drawer HP) -->
          <li class="drawer-footer">
            <button
              class="drawer-logout-btn"
              @click="
                isMobileMenuOpen = false;
                isLogoutModalOpen = true;
              "
              type="button"
            >
              <i class="fa-solid fa-power-off"></i> Keluar Sistem
            </button>
          </li>
        </ul>

        <div class="nav-user-controls">
          <div
            class="user-profile-card"
            @click="router.push('/profil')"
            title="Lihat Profil"
          >
            <div class="user-avatar-box">
              <img v-if="fotoUser" :src="fotoUser" alt="Foto" />
              <i v-else class="fa-solid fa-user-tie"></i>
            </div>
            <div class="user-meta-text">
              <span class="user-name">{{ namaUser }}</span>
              <span class="user-role-tag" :class="roleUser">{{
                roleLabel
              }}</span>
            </div>
          </div>

          <button
            class="btn-power-off"
            title="Keluar Sistem"
            @click="isLogoutModalOpen = true"
            type="button"
          >
            <i class="fa-solid fa-power-off"></i>
          </button>

          <div
            class="hamburger"
            :class="{ active: isMobileMenuOpen }"
            @click="isMobileMenuOpen = !isMobileMenuOpen"
          >
            <span></span><span></span><span></span>
          </div>
        </div>
      </div>
    </nav>

    <div
      class="mobile-overlay"
      :class="{ active: isMobileMenuOpen }"
      @click="isMobileMenuOpen = false"
    ></div>

    <div
      class="logout-overlay"
      :class="{ active: isLogoutModalOpen }"
      @click.self="isLogoutModalOpen = false"
    >
      <div class="modal-box-logout">
        <div class="logout-icon-wrapper">
          <i class="fa-solid fa-right-from-bracket"></i>
        </div>
        <h3>Konfirmasi Keluar</h3>
        <p>Apakah Anda yakin ingin keluar dari sistem <b>SiMANTAP</b>?</p>
        <div class="logout-actions">
          <button
            type="button"
            class="btn-cancel-logout"
            @click="isLogoutModalOpen = false"
          >
            Batal
          </button>
          <button
            type="button"
            class="btn-confirm-logout"
            @click="executeLogout"
          >
            Ya, Keluar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const router = useRouter();
const route = useRoute();

const isScrolled = ref(false);
const isMobileMenuOpen = ref(false);
const isLogoutModalOpen = ref(false);

// 🔥 Ambil data user yang sedang login dari localStorage
const userData = ref(null);
onMounted(() => {
  const raw = localStorage.getItem("user");
  if (raw) {
    try {
      userData.value = JSON.parse(raw);
    } catch (e) {
      userData.value = null;
    }
  }
});

// Nama & role yang ditampilkan di navbar
const namaUser = computed(() => userData.value?.nama_lengkap || "Pengguna");
const roleUser = computed(() => userData.value?.role || "admin");
const roleLabel = computed(() =>
  (userData.value?.role || "admin").toUpperCase(),
);
const fotoUser = computed(() => userData.value?.foto || null);

// Arahkan logo navbar ke dashboard sesuai role
const getDashboardLink = () => {
  const tujuan = {
    admin: "/admin",
    sekdes: "/sekdes",
    kades: "/kades",
    warga: "/warga",
    developer: "/dev",
  };
  return tujuan[userData.value?.role] || "/admin";
};

// ====== MENU DINAMIS PER ROLE ======
const dashboardItem = computed(() => ({
  to: getDashboardLink(),
  icon: "fa-solid fa-chart-pie",
  label: "Dashboard",
  exact: true,
}));

const menuRole = computed(() => {
  const role = userData.value?.role || "admin";
  const dash = dashboardItem.value;

  // Menu read-only Arsip & Aset (dipakai sekdes & kades)
  const arsipAset = [
    {
      to: "/admin/arsip/list",
      icon: "fa-solid fa-box-archive",
      label: "Arsip Digital",
    },
    {
      to: "/admin/aset/list",
      icon: "fa-solid fa-boxes-stacked",
      label: "Aset Desa",
    },
  ];

  if (role === "admin") {
    return [
      dash,
      {
        icon: "fa-solid fa-envelope-open-text",
        label: "Pelayanan Masyarakat",
        children: [
          {
            to: "/admin/pelayanan",
            icon: "fa-solid fa-table-list",
            label: "Pengajuan Surat",
          },
        ],
      },
      {
        icon: "fa-solid fa-folder-open",
        label: "Persuratan",
        children: [
          {
            to: "/admin/surat-masuk",
            icon: "fa-solid fa-inbox",
            label: "Surat Masuk",
          },
          {
            to: "/admin/surat-keluar",
            icon: "fa-solid fa-paper-plane",
            label: "Surat Keluar",
          },
        ],
      },
      // Admin: Arsip & Aset diarahkan ke DASHBOARD (bukan daftar/list),
      // karena admin punya akses kelola penuh lewat halaman dashboard.
      {
        to: "/admin/arsip",
        icon: "fa-solid fa-box-archive",
        label: "Arsip Digital",
      },
      {
        to: "/admin/aset",
        icon: "fa-solid fa-boxes-stacked",
        label: "Aset Desa",
      },
    ];
  }

  if (role === "sekdes") {
    return [
      dash,
      {
        to: "/sekdes/verifikasi",
        icon: "fa-solid fa-clipboard-check",
        label: "Verifikasi Berkas",
      },
      {
        to: "/sekdes/disposisi",
        icon: "fa-solid fa-share-from-square",
        label: "Disposisi Surat",
      },
      {
        to: "/sekdes/rekap",
        icon: "fa-solid fa-chart-column",
        label: "Laporan Rekap",
      },
      ...arsipAset,
    ];
  }

  if (role === "kades") {
    return [
      dash,
      {
        to: "/kades/persetujuan",
        icon: "fa-solid fa-file-signature",
        label: "Persetujuan Surat",
      },
      {
        to: "/kades/atensi",
        icon: "fa-solid fa-bell",
        label: "Tindak Lanjut Disposisi",
      },
      {
        to: "/kades/rekap",
        icon: "fa-solid fa-chart-column",
        label: "Laporan Rekap",
      },
      ...arsipAset,
    ];
  }

  if (role === "developer") {
    return [dash];
  }

  // warga (default)
  return [dash];
});

// Cek apakah path saat ini ada di dalam grup "Pelayanan"
const isPelayananActive = computed(() => {
  return route.path.startsWith("/admin/pelayanan");
});

// Cek apakah path saat ini ada di dalam grup "Log Surat"
const isLogSuratActive = computed(() => {
  return (
    route.path.startsWith("/admin/surat-masuk") ||
    route.path.startsWith("/admin/surat-keluar")
  );
});

const toggleMobileDropdown = (event) => {
  if (window.innerWidth <= 992) {
    const parent = event.currentTarget.parentElement;
    parent.classList.toggle("show-mobile");
  }
};

// Tutup drawer otomatis saat klik link tujuan (bukan dropdown toggle)
const handleMenuClick = (event) => {
  const link = event.target.closest("a");
  if (!link) return;
  // dropdown-toggle hanya membuka/menutup sub-menu, jangan tutup drawer
  if (link.classList.contains("dropdown-toggle")) return;
  // link tujuan (router-link) → tutup drawer
  isMobileMenuOpen.value = false;
};

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20;
};

// 🔥 Logout beneran: panggil API, hapus token & data user, lalu ke /login
const executeLogout = async () => {
  isLogoutModalOpen.value = false;

  const token = localStorage.getItem("token");
  try {
    if (token) {
      await axios.post(
        `${import.meta.env.VITE_API_URL}/api/logout`,
        {},
        { headers: { Authorization: `Bearer ${token}` } },
      );
    }
  } catch (e) {
    // Walau API logout gagal (mis. token sudah kadaluarsa), tetap bersihkan sesi lokal
    console.warn("Logout API gagal, sesi lokal tetap dibersihkan:", e);
  } finally {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    sessionStorage.removeItem("sesi_aktif");
    delete axios.defaults.headers.common["Authorization"];

    await Swal.fire({
      icon: "success",
      title: "Berhasil Keluar",
      text: "Sampai jumpa kembali!",
      showConfirmButton: false,
      timer: 1500,
    });

    router.push("/login");
  }
};

onMounted(() => {
  window.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll);
});
</script>

<style>
/* =====================================================================
   SiMANTAP CORE NAVIGATOR - MEGA PREMIUM GLASSMORPHISM (POPPINS STYLE)
   ===================================================================== */
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap");

/* Cegah scroll horizontal / elemen meluber di layar HP */
html,
body {
  overflow-x: hidden;
  max-width: 100%;
}

/* Elemen khusus drawer HP — disembunyikan di desktop */
.drawer-profile,
.drawer-divider,
.drawer-footer {
  display: none;
}

/* Memaksa seluruh komponen navigasi menggunakan font Poppins kesukaanmu */
.vue-topbar-wrapper,
.navbar,
.dropdown-menu,
.modal-box-logout {
  font-family: "Poppins", sans-serif !important;
}

.navbar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 80px;
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(15px);
  -webkit-backdrop-filter: blur(15px);
  display: flex;
  align-items: center;
  z-index: 9999;
  border-bottom: 1px solid #e2e8f0;
  transition: all 0.3s ease;
}

.navbar.scrolled {
  height: 70px;
  background: rgba(15, 23, 42, 0.9);
  border-bottom-color: rgba(255, 255, 255, 0.1);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.container-nav {
  width: 95%;
  max-width: 1600px;
  margin: 0 auto;
  padding: 0;
  box-sizing: border-box;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.nav-brand {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  user-select: none;
}
.logo-wrapper {
  width: 46px;
  height: 46px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: 0.3s;
  flex: 0 0 auto;
}
.brand-logo-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.brand-text {
  display: flex;
  flex-direction: column;
  line-height: 1.1;
}
.main-title {
  font-size: 19px;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.5px;
  transition: 0.3s;
}
.sub-title {
  font-size: 11px;
  font-weight: 800;
  color: #10b981;
  letter-spacing: 1.5px;
}
.navbar.scrolled .main-title {
  color: #ffffff;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 10px;
  list-style: none;
  margin: 0;
  padding: 0;
}
.menu-item {
  color: #475569;
  padding: 10px 18px;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 8px;
  border-radius: 10px;
  transition: 0.3s;
  white-space: nowrap;
  line-height: 1;
}
.menu-item i {
  font-size: 15px;
}
.navbar.scrolled .menu-item {
  color: #cbd5e1;
}
.menu-item:hover {
  background: rgba(16, 185, 129, 0.08);
  color: #10b981;
}
.navbar.scrolled .menu-item:hover {
  background: rgba(255, 255, 255, 0.05);
  color: #10b981;
}

.active-link {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
  color: white !important;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
}

.dropdown {
  position: relative;
}
.arrow-icon {
  font-size: 11px !important;
  transition: transform 0.3s;
  margin-left: 2px;
  opacity: 0.5;
}
.dropdown:hover .arrow-icon {
  transform: rotate(180deg);
  opacity: 1;
}

.dropdown-menu {
  position: absolute;
  top: calc(100% + 15px);
  left: 50%;
  transform: translateX(-50%) translateY(10px);
  background: #ffffff;
  border: 1px solid #e2e8f0;
  width: 210px;
  border-radius: 14px;
  padding: 8px;
  list-style: none;
  box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08);
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
}
.navbar.scrolled .dropdown-menu {
  background: #1e293b;
  border-color: #334155;
}
.dropdown:hover .dropdown-menu {
  opacity: 1;
  visibility: visible;
  transform: translateX(-50%) translateY(0);
}

.dropdown-menu a {
  padding: 10px 14px;
  color: #334155;
  font-size: 13.5px;
  font-weight: 600;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 10px;
  border-radius: 8px;
  transition: 0.2s;
}
.navbar.scrolled .dropdown-menu a {
  color: #cbd5e1;
}
.dropdown-menu a:hover {
  background: #f1f5f9;
  color: #10b981;
  transform: translateX(3px);
}

.nav-user-controls {
  display: flex;
  align-items: center;
  gap: 15px;
}
.user-profile-card {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  transition: 0.2s;
}
.user-profile-card:hover {
  opacity: 0.8;
}
.user-avatar-box img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}
.user-avatar-box {
  width: 38px;
  height: 38px;
  background: #f1f5f9;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #475569;
  font-size: 16px;
  border: 2px solid #e2e8f0;
  overflow: hidden;
}
.navbar.scrolled .user-avatar-box {
  background: #334155;
  color: #cbd5e1;
  border-color: #475569;
}

.user-meta-text {
  display: flex;
  flex-direction: column;
  line-height: 1.2;
}
.user-name {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
}
.navbar.scrolled .user-name {
  color: #ffffff;
}
.user-role-tag {
  font-size: 9.5px;
  font-weight: 800;
  padding: 2px 6px;
  border-radius: 4px;
  width: max-content;
  margin-top: 2px;
}
.user-role-tag.admin {
  background: #eff6ff;
  color: #2563eb;
}
.user-role-tag.sekdes {
  background: #f0fdf4;
  color: #059669;
}
.user-role-tag.kades {
  background: #fffbeb;
  color: #b45309;
}
.user-role-tag.warga {
  background: #f1f5f9;
  color: #475569;
}
.user-role-tag.developer {
  background: #faf5ff;
  color: #9333ea;
}

.btn-power-off {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #ef4444;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  cursor: pointer;
  transition: 0.3s;
}
.btn-power-off:hover {
  background: #ef4444;
  color: white;
}

.hamburger {
  display: none;
  flex-direction: column;
  gap: 5px;
  cursor: pointer;
  width: 24px;
}
.hamburger span {
  width: 100%;
  height: 3px;
  background: #0f172a;
  border-radius: 2px;
  transition: 0.3s;
}
.navbar.scrolled .hamburger span {
  background: white;
}

.logout-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(5px);
  z-index: 10000;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  visibility: hidden;
  transition: 0.3s;
}
.logout-overlay.active {
  opacity: 1;
  visibility: visible;
}
.modal-box-logout {
  background: white;
  padding: 35px;
  border-radius: 20px;
  text-align: center;
  width: 90%;
  max-width: 400px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
  transform: scale(0.95);
  transition: 0.3s;
}
.logout-overlay.active .modal-box-logout {
  transform: scale(1);
}
.logout-icon-wrapper {
  width: 60px;
  height: 60px;
  background: #fef2f2;
  color: #ef4444;
  font-size: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
}
.modal-box-logout h3 {
  font-size: 18px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 10px;
}
.modal-box-logout p {
  font-size: 14px;
  color: #64748b;
  margin-bottom: 25px;
}
.logout-actions {
  display: flex;
  gap: 12px;
}
.btn-cancel-logout,
.btn-confirm-logout {
  flex: 1;
  padding: 12px;
  border-radius: 10px;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: 0.2s;
}
.btn-cancel-logout {
  background: white;
  border: 2px solid #e2e8f0;
  color: #475569;
}
.btn-confirm-logout {
  background: #ef4444;
  border: none;
  color: white;
}

.mobile-overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background: rgba(15, 23, 42, 0.4);
  z-index: 999;
  backdrop-filter: blur(2px);
}
.mobile-overlay.active {
  display: block;
}

@media (max-width: 1180px) {
  .hamburger {
    display: flex;
  }
  .nav-links {
    position: fixed;
    top: 0;
    right: 0;
    width: 300px;
    max-width: 86vw;
    height: 100vh;
    background: linear-gradient(165deg, #1e293b 0%, #0f172a 100%);
    flex-direction: column;
    align-items: stretch;
    padding: 24px 18px 24px;
    box-shadow: -20px 0 60px rgba(0, 0, 0, 0.4);
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    transform: translateX(100%);
    z-index: 9998;
    gap: 6px;
    box-sizing: border-box;
    overflow-y: auto;
  }
  .nav-links.active {
    transform: translateX(0);
  }

  /* === HEADER PROFIL DRAWER === */
  .drawer-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    margin-bottom: 6px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    cursor: pointer;
    transition: background 0.2s ease;
    backdrop-filter: blur(10px);
  }
  .drawer-profile:hover {
    background: rgba(255, 255, 255, 0.1);
  }
  .drawer-avatar {
    width: 48px;
    height: 48px;
    min-width: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, #10b981, #059669);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 20px;
    overflow: hidden;
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
  }
  .drawer-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .drawer-profile-meta {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
  }
  .drawer-name {
    color: #fff;
    font-weight: 700;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .drawer-role {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #34d399;
    background: rgba(52, 211, 153, 0.15);
    padding: 2px 8px;
    border-radius: 6px;
    width: fit-content;
  }
  .drawer-profile-arrow {
    color: #64748b;
    font-size: 12px;
  }
  .drawer-divider {
    height: 1px;
    background: rgba(255, 255, 255, 0.08);
    margin: 8px 4px 10px;
    list-style: none;
  }

  /* === MENU ITEM DI DRAWER === */
  .nav-links .menu-item {
    width: 100%;
    padding: 12px 15px;
    border-radius: 12px;
    color: #cbd5e1 !important;
    font-weight: 600;
    font-size: 13.5px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.2s ease;
  }
  .nav-links .menu-item i {
    color: #34d399;
    width: 18px;
    text-align: center;
    font-size: 14px;
  }
  .nav-links .menu-item:hover {
    background: rgba(255, 255, 255, 0.07);
    color: #fff !important;
  }
  .nav-links .menu-item.active-link {
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff !important;
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
  }
  .nav-links .menu-item.active-link i {
    color: #fff;
  }
  .arrow-icon {
    margin-left: auto;
  }
  .dropdown {
    width: 100%;
  }
  .dropdown-menu {
    position: relative;
    top: 0;
    left: 0;
    transform: none;
    width: 100%;
    opacity: 1;
    visibility: visible;
    display: none;
    box-shadow: none;
    background: rgba(0, 0, 0, 0.2);
    margin-top: 4px;
    border: none;
    border-radius: 12px;
    padding: 4px;
  }
  /* Matikan dropdown hover desktop di HP (cegah nembus keluar drawer) */
  .dropdown:hover .dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: none;
    display: none;
  }
  .dropdown.show-mobile:hover .dropdown-menu,
  .dropdown.show-mobile .dropdown-menu {
    display: block;
  }
  .dropdown-menu li a {
    color: #94a3b8 !important;
    padding: 11px 16px;
    border-radius: 10px;
    font-size: 13px;
  }
  .dropdown-menu li a:hover {
    background: rgba(255, 255, 255, 0.06);
    color: #fff !important;
  }
  .dropdown.show-mobile .dropdown-menu {
    display: block;
  }
  .dropdown.show-mobile .arrow-icon {
    transform: rotate(180deg);
  }

  /* === FOOTER LOGOUT DRAWER === */
  .drawer-footer {
    display: block;
    margin-top: auto;
    padding-top: 16px;
    list-style: none;
  }
  .drawer-logout-btn {
    width: 100%;
    padding: 14px;
    border-radius: 14px;
    border: 1px solid rgba(239, 68, 68, 0.3);
    background: rgba(239, 68, 68, 0.12);
    color: #fca5a5;
    font-weight: 700;
    font-size: 14px;
    font-family: inherit;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.2s ease;
  }
  .drawer-logout-btn:hover {
    background: #ef4444;
    color: #fff;
    border-color: #ef4444;
    box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
  }

  /* Sembunyikan profil card & power-off lama di topbar (sudah pindah ke drawer) */
  .user-profile-card,
  .btn-power-off {
    display: none !important;
  }
}

@media (max-width: 480px) {
  .container-nav {
    padding: 0 14px;
  }
  .main-title {
    font-size: 14px;
  }
  .sub-title {
    font-size: 10px;
  }
  .user-name {
    display: none;
  }
}

@media (max-width: 360px) {
  .main-title {
    font-size: 13px;
  }
  .logo-wrapper {
    width: 30px;
    height: 30px;
    font-size: 13px;
  }
}
</style>
