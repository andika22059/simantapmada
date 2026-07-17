import { createApp } from "vue";
import axios from "axios";
import "./style.css";
import "./assets/css/theme.css";
import "./assets/css/swal-theme.css";
import App from "./App.vue";
import router from "./router";
import { installUploadProgress } from "./assets/js/uploadProgress";

// Progress bar upload global (muncul otomatis di semua form yang unggah file)
installUploadProgress(axios);

// =====================================================
// 🔒 AXIOS GLOBAL: pasang token + auto-logout saat 401
// =====================================================
const API_BASE = `${import.meta.env.VITE_API_URL}/api`;

// Bersihkan semua jejak sesi (dipakai saat logout / sesi mati)
const bersihkanSesi = () => {
  localStorage.removeItem("token");
  localStorage.removeItem("user");
  sessionStorage.removeItem("sesi_aktif");
  delete axios.defaults.headers.common["Authorization"];
};

// Pasang token otomatis di setiap request (kalau ada)
const tokenAwal = localStorage.getItem("token");
if (tokenAwal) {
  axios.defaults.headers.common["Authorization"] = `Bearer ${tokenAwal}`;
}

// Kalau server balas 401 (token kadaluarsa/invalid) → bersihkan & lempar ke login
axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      bersihkanSesi();
      if (router.currentRoute.value.path !== "/login") {
        router.push({ path: "/login", query: { pesan: "sesi_habis" } });
      }
    }
    return Promise.reject(error);
  },
);

// Re-validasi token ke SERVER saat app dibuka (bukan cuma cek localStorage).
// Hanya untuk sesi yang masih "hidup" di tab ini (punya penanda sesi_aktif).
// Token expired di backend → balas 401 → interceptor di atas yang bereskan.
if (tokenAwal && sessionStorage.getItem("sesi_aktif")) {
  axios
    .get(`${API_BASE}/me`)
    .catch(() => {
      /* 401 sudah ditangani interceptor; error lain diabaikan */
    });
}

const app = createApp(App);

app.use(router);
app.mount("#app");
