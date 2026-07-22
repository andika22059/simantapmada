import { createRouter, createWebHistory } from "vue-router";
import {
  mulaiProgressRute,
  selesaiProgressRute,
} from "../assets/js/uploadProgress.js";

const routes = [
  // --- AUTH ---
  {
    path: "/",
    redirect: "/login",
  },
  {
    path: "/login",
    name: "Login",

    component: () => import("../view/auth/loginView.vue"),
  },

  {
    path: "/register",
    name: "Register",

    component: () => import("../view/auth/Register.vue"),
  },
  // --- WARGA ---
  {
    path: "/warga",
    name: "DashboardWarga",
    component: () => import("../view/warga/DashboardWarga.vue"),
    meta: { requiresAuth: true },
  },

  // --- ADMIN / OPERATOR ---
  {
    path: "/admin",
    name: "Admin",
    component: () => import("../view/admin/AdminDashboard.vue"),
    meta: { requiresAuth: true },
    children: [
      {
        path: "pengaturan",
        name: "Pengaturan",
        component: () => import("../view/admin/Pengaturan.vue"),
      },
      {
        path: "pelayanan",
        name: "PelayananDashboard",
        component: () =>
          import("../view/admin/pelayanan/PelayananDashboard.vue"),
      },
      {
        path: "pelayanan/tambah",
        name: "PelayananTambah",
        component: () => import("../view/admin/pelayanan/PelayananTambah.vue"),
      },
      {
        path: "pelayanan/list",
        name: "PelayananList",
        component: () => import("../view/admin/pelayanan/PelayananList.vue"),
      },
      {
        path: "surat-masuk",
        name: "SuratMasuk",
        component: () => import("../view/admin/surat-masuk/SuratMasuk.vue"),
      },
      {
        path: "surat-masuk/tambah",
        name: "TambahSurat",
        component: () => import("../view/admin/surat-masuk/TambahSurat.vue"),
      },
      {
        path: "surat-masuk/edit/:id",
        name: "EditSurat",
        component: () => import("../view/admin/surat-masuk/EditSurat.vue"),
      },
      {
        path: "surat-keluar",
        name: "SuratKeluar",
        component: () => import("../view/admin/surat-keluar/SuratKeluar.vue"),
      },
      {
        path: "surat-keluar/tambah",
        name: "SuratKeluarTambah",
        component: () =>
          import("../view/admin/surat-keluar/SuratKeluarTambah.vue"),
      },
      {
        path: "arsip",
        name: "Arsip",
        component: () => import("../view/admin/arsip/ArsipDashboard.vue"),
      },
      {
        path: "arsip/tambah",
        name: "ArsipTambah",
        component: () => import("../view/admin/arsip/ArsipTambah.vue"),
      },
      {
        path: "arsip/edit/:id",
        name: "ArsipEdit",
        component: () => import("../view/admin/arsip/ArsipTambah.vue"),
      },
      {
        path: "arsip/list",
        name: "ArsipList",
        component: () => import("../view/admin/arsip/ArsipList.vue"),
      },
      {
        path: "aset",
        name: "AsetDashboard",
        component: () => import("../view/admin/aset/AsetDashboard.vue"),
      },
      {
        path: "aset/list",
        name: "AsetList",
        component: () => import("../view/admin/aset/AsetList.vue"),
      },
      {
        path: "aset/tambah",
        name: "AsetTambah",
        component: () => import("../view/admin/aset/AsetTambah.vue"),
      },
      {
        path: "aset/edit/:id",
        name: "AsetEdit",
        component: () => import("../view/admin/aset/AsetEdit.vue"),
      },
    ],
  },

  // --- PROFIL (universal, semua role) ---
  {
    path: "/profil",
    name: "Profil",
    component: () => import("../view/ProfileView.vue"),
    meta: { requiresAuth: true },
  },

  // --- CETAK LAPORAN (TANPA LAYOUT, JADI NAVBAR ADMIN TIDAK IKUT MUNCUL) ---
  {
    path: "/cetak-laporan",
    name: "CetakLaporan",
    component: () => import("../view/admin/CetakLaporan.vue"),
  },

  // --- CETAK SURAT JADI (TANPA LAYOUT) ---
  {
    path: "/surat/cetak/:id",
    name: "CetakSurat",
    component: () => import("../view/admin/pelayanan/CetakSurat.vue"),
    meta: { requiresAuth: true },
  },

  // --- HALAMAN SCAN QR PUBLIK (TANPA LOGIN) ---
  // Dibuka warga setelah scan QR pada label aset / arsip.
  // Tidak ada meta requiresAuth → bebas diakses tanpa token.
  {
    path: "/scan/aset/:id",
    name: "ScanAset",
    component: () => import("../view/publik/ScanAset.vue"),
  },
  {
    path: "/scan/arsip/:id",
    name: "ScanArsip",
    component: () => import("../view/publik/ScanArsip.vue"),
  },

  // --- LAPORAN REKAP LENGKAP (semua modul) ---
  {
    path: "/laporan",
    name: "LaporanRekap",
    component: () => import("../view/rekap/RekapBulanan.vue"),
    meta: { requiresAuth: true },
  },

  // --- SEKRETARIS DESA ---
  {
    path: "/sekdes",
    name: "SekdesDashboard",
    component: () => import("../view/sekdes/SekdesDashboard.vue"),
    meta: { requiresAuth: true },
  },
  {
    path: "/sekdes/verifikasi",
    name: "SekdesVerifikasi",
    component: () => import("../view/sekdes/SekdesVerifikasi.vue"),
    meta: { requiresAuth: true },
  },
  {
    path: "/sekdes/disposisi",
    name: "SekdesDisposisi",
    component: () => import("../view/sekdes/SekdesDisposisi.vue"),
    meta: { requiresAuth: true },
  },
  {
    path: "/sekdes/rekap",
    name: "SekdesRekap",
    component: () => import("../view/rekap/RekapBulanan.vue"),
    meta: { requiresAuth: true },
  },

  // --- KEPALA DESA ---
  {
    path: "/kades",
    name: "KadesDashboard",
    component: () => import("../view/kades/KadesDashboard.vue"),
    meta: { requiresAuth: true },
  },
  {
    path: "/kades/persetujuan",
    name: "KadesPersetujuan",
    component: () => import("../view/kades/KadesPersetujuan.vue"),
    meta: { requiresAuth: true },
  },
  {
    path: "/kades/atensi",
    name: "KadesAtensi",
    component: () => import("../view/kades/KadesAtensi.vue"),
    meta: { requiresAuth: true },
  },
  {
    path: "/kades/rekap",
    name: "KadesRekap",
    component: () => import("../view/rekap/RekapBulanan.vue"),
    meta: { requiresAuth: true },
  },

  // --- DEVELOPER ---
  {
    path: "/dev/akun",
    name: "KelolaAkun",
    component: () => import("../view/dev/KelolaAkun.vue"),
    meta: { requiresAuth: true },
  },
  {
    path: "/dev",
    name: "DeveloperDashboard",
    component: () => import("../view/dev/DeveloperDashboard.vue"),
    meta: { requiresAuth: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// 🔒 PROTEKSI ROUTE (Navigation Guard)
// Cek token + role sebelum membuka halaman.
router.beforeEach((to, from, next) => {
  // Sesi dianggap sah hanya jika ADA token DAN penanda sesi-hidup di tab ini.
  // sessionStorage "sesi_aktif" hilang saat tab/browser ditutup → buka lagi
  // (atau tab bookmark baru) = dipaksa login ulang. Token saja tidak cukup.
  const tokenRaw = localStorage.getItem("token");
  const sesiHidup = !!sessionStorage.getItem("sesi_aktif");
  const token = tokenRaw && sesiHidup ? tokenRaw : null;

  // Ambil role user dari localStorage
  let role = "";
  try {
    role = JSON.parse(localStorage.getItem("user"))?.role || "";
  } catch (e) {
    role = "";
  }

  // 1) Halaman butuh login tapi sesi tidak sah → ke /login
  if (to.meta.requiresAuth && !token) {
    next({ path: "/login", query: { pesan: "belum_login" } });
    return;
  }

  // 2) Sudah login tapi buka /login → arahkan ke dashboard sesuai role
  const tujuanDashboard = {
    admin: "/admin",
    sekdes: "/sekdes",
    kades: "/kades",
    warga: "/warga",
    developer: "/dev",
  };
  if (to.path === "/login" && token) {
    next(tujuanDashboard[role] || "/admin");
    return;
  }

  // 3) PROTEKSI ROLE (cross-access dilarang, kecuali developer)
  // Developer boleh akses semua. Role lain hanya area-nya sendiri.
  if (token && role !== "developer") {
    // Path "bersama" yang boleh diakses lintas role (mis. Arsip & Aset read-only
    // untuk sekdes & kades). Halaman cetak surat juga dibebaskan.
    const pathBersama = ["/admin/arsip", "/admin/aset", "/surat/cetak"];
    const bolehLintas = pathBersama.some(
      (p) =>
        to.path === p || to.path.startsWith(p + "/") || to.path.startsWith(p),
    );

    if (!bolehLintas) {
      // Tentukan area mana yang sedang diakses berdasarkan awalan path
      const areaRole = {
        "/admin": "admin",
        "/sekdes": "sekdes",
        "/kades": "kades",
        "/warga": "warga",
        "/dev": "developer",
      };

      // Cari area yang cocok dengan path tujuan
      let areaDibutuhkan = null;
      for (const prefix in areaRole) {
        if (to.path === prefix || to.path.startsWith(prefix + "/")) {
          areaDibutuhkan = areaRole[prefix];
          break;
        }
      }

      // Kalau path ini milik area role tertentu & bukan role-nya → tendang ke dashboard sendiri
      if (areaDibutuhkan && areaDibutuhkan !== role) {
        next(tujuanDashboard[role] || "/login");
        return;
      }
    }
  }

  next();
});

// ===== Bar progres tipis saat berpindah halaman =====
router.beforeEach((to, from, next) => {
  if (to.path !== from.path) mulaiProgressRute();
  next();
});
router.afterEach(() => selesaiProgressRute());
router.onError(() => selesaiProgressRute());

export default router;
