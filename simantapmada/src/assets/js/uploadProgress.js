// =====================================================================
// PROGRESS BAR UPLOAD GLOBAL
// Bar tipis di atas layar yang otomatis muncul saat ADA upload file
// (request apa pun yang mengirim FormData): surat masuk/keluar, aset,
// arsip, pengaturan, profil, pelayanan, dst.
//
// Cukup dipasang sekali di main.js:
//   import { installUploadProgress } from "@/assets/js/uploadProgress";
//   installUploadProgress(axios);
// =====================================================================

let bar = null;

function pastikanBar() {
  if (bar) return;
  const wrap = document.createElement("div");
  wrap.style.cssText =
    "position:fixed;top:0;left:0;width:100%;height:3px;z-index:999999;background:transparent;pointer-events:none;";
  bar = document.createElement("div");
  bar.style.cssText =
    "height:100%;width:0%;opacity:0;background:linear-gradient(90deg,#059669,#10b981);box-shadow:0 0 8px rgba(16,185,129,.7);transition:width .2s ease,opacity .4s ease;";
  wrap.appendChild(bar);
  document.body.appendChild(wrap);
}

function setPersen(p) {
  pastikanBar();
  bar.style.opacity = "1";
  bar.style.width = Math.max(2, Math.min(p, 100)) + "%";
}

function selesai() {
  if (!bar) return;
  bar.style.width = "100%";
  setTimeout(() => {
    bar.style.opacity = "0";
    setTimeout(() => {
      if (bar) bar.style.width = "0%";
    }, 400);
  }, 300);
}

// ===== Progress bar saat berpindah halaman (indeterminate) =====
// Dipakai router: mulai saat navigasi, selesai saat halaman siap.
let timerRute = null;

export function mulaiProgressRute() {
  let p = 12;
  setPersen(p);
  clearInterval(timerRute);
  timerRute = setInterval(() => {
    p = Math.min(p + Math.random() * 12, 85); // merambat, tak pernah 100 sendiri
    setPersen(p);
  }, 120);
}

export function selesaiProgressRute() {
  clearInterval(timerRute);
  timerRute = null;
  selesai();
}

export function installUploadProgress(axiosInstance) {
  const ax = axiosInstance;
  if (!ax || !ax.interceptors) return;

  ax.interceptors.request.use((config) => {
    const punyaFile =
      typeof FormData !== "undefined" && config.data instanceof FormData;
    if (punyaFile) {
      config._uploadAktif = true;
      setPersen(2);
      config.onUploadProgress = (e) => {
        if (e && e.total) setPersen(Math.round((e.loaded * 100) / e.total));
        else setPersen(60);
      };
    }
    return config;
  });

  ax.interceptors.response.use(
    (res) => {
      if (res.config && res.config._uploadAktif) selesai();
      return res;
    },
    (err) => {
      if (err.config && err.config._uploadAktif) selesai();
      return Promise.reject(err);
    },
  );
}
