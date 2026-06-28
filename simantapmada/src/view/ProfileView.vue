<template>
  <Topbar />
  <div class="profil-body">
    <div class="profil-wrapper">
      <!-- HEADER -->
      <div class="profil-header fade-up">
        <div class="ph-text">
          <h1><i class="fa-solid fa-user-gear"></i> Profil Saya</h1>
          <p>Kelola data diri dan keamanan akun Anda.</p>
        </div>
        <button class="btn-back" @click="kembali" type="button">
          <i class="fa-solid fa-arrow-left"></i> Kembali
        </button>
      </div>

      <div class="profil-grid">
        <!-- KARTU KIRI: KARTU IDENTITAS -->
        <div class="card-identitas fade-up">
          <div class="avatar-big">
            <img
              v-if="previewFoto || fotoSaatIni"
              :src="previewFoto || fotoSaatIni"
              alt="Foto Profil"
            />
            <i v-else class="fa-solid fa-user"></i>
          </div>
          <h2>{{ form.nama_lengkap || "Pengguna" }}</h2>
          <span class="role-chip" :class="roleUser">{{ roleLabel }}</span>
          <div class="username-tag">
            <i class="fa-solid fa-at"></i> {{ form.username }}
          </div>
        </div>

        <!-- KARTU KANAN: FORM -->
        <div class="card-form">
          <!-- BAGIAN 1: DATA DIRI -->
          <div class="form-block fade-up">
            <div class="block-title">
              <i class="fa-solid fa-id-card"></i> Data Diri
            </div>

            <div class="input-group">
              <label>Foto Profil</label>
              <div class="foto-uploader">
                <div class="foto-prev">
                  <img
                    v-if="previewFoto || fotoSaatIni"
                    :src="previewFoto || fotoSaatIni"
                    alt="Preview"
                  />
                  <i v-else class="fa-solid fa-image"></i>
                </div>
                <div class="foto-actions">
                  <button
                    type="button"
                    class="btn-pilih-foto"
                    @click="pilihFoto"
                  >
                    <i class="fa-solid fa-upload"></i> Pilih Foto
                  </button>
                  <button
                    v-if="previewFoto"
                    type="button"
                    class="btn-batal-foto"
                    @click="batalFoto"
                  >
                    <i class="fa-solid fa-xmark"></i> Batal
                  </button>
                  <span class="foto-hint">JPG/PNG, maks 2MB</span>
                  <input
                    type="file"
                    ref="fotoInput"
                    accept=".jpg,.jpeg,.png"
                    hidden
                    @change="handleFoto"
                  />
                </div>
              </div>
            </div>

            <div class="input-group">
              <label>Nama Lengkap</label>
              <div class="input-wrap">
                <i class="fa-solid fa-user"></i>
                <input
                  type="text"
                  v-model="form.nama_lengkap"
                  placeholder="Nama lengkap"
                />
              </div>
            </div>

            <div class="input-group">
              <label>Username</label>
              <div class="input-wrap">
                <i class="fa-solid fa-at"></i>
                <input
                  type="text"
                  v-model="form.username"
                  placeholder="Username untuk login"
                />
              </div>
              <small class="warn-text">
                <i class="fa-solid fa-circle-info"></i> Jika diubah, gunakan
                username baru saat login berikutnya.
              </small>
            </div>

            <button
              class="btn-simpan"
              :disabled="savingProfil"
              @click="simpanProfil"
            >
              <i
                :class="
                  savingProfil
                    ? 'fa-solid fa-spinner fa-spin'
                    : 'fa-solid fa-floppy-disk'
                "
              ></i>
              {{ savingProfil ? "Menyimpan..." : "Simpan Perubahan" }}
            </button>
          </div>

          <!-- BAGIAN 2: GANTI PASSWORD -->
          <div class="form-block fade-up">
            <div class="block-title">
              <i class="fa-solid fa-lock"></i> Ganti Password
            </div>

            <div class="input-group">
              <label>Password Lama</label>
              <div class="input-wrap">
                <i class="fa-solid fa-key"></i>
                <input
                  :type="showLama ? 'text' : 'password'"
                  v-model="pass.password_lama"
                  placeholder="Masukkan password saat ini"
                />
                <i
                  class="toggle-eye"
                  :class="
                    showLama ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'
                  "
                  @click="showLama = !showLama"
                ></i>
              </div>
            </div>

            <div class="input-group">
              <label>Password Baru</label>
              <div class="input-wrap">
                <i class="fa-solid fa-lock"></i>
                <input
                  :type="showBaru ? 'text' : 'password'"
                  v-model="pass.password_baru"
                  placeholder="Minimal 6 karakter"
                />
                <i
                  class="toggle-eye"
                  :class="
                    showBaru ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'
                  "
                  @click="showBaru = !showBaru"
                ></i>
              </div>
            </div>

            <div class="input-group">
              <label>Konfirmasi Password Baru</label>
              <div class="input-wrap">
                <i class="fa-solid fa-lock"></i>
                <input
                  :type="showBaru ? 'text' : 'password'"
                  v-model="pass.password_baru_confirmation"
                  placeholder="Ulangi password baru"
                />
              </div>
            </div>

            <button
              class="btn-simpan btn-pass"
              :disabled="savingPass"
              @click="simpanPassword"
            >
              <i
                :class="
                  savingPass
                    ? 'fa-solid fa-spinner fa-spin'
                    : 'fa-solid fa-shield-halved'
                "
              ></i>
              {{ savingPass ? "Memproses..." : "Ubah Password" }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Topbar from "../components/Topbar.vue";
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const router = useRouter();

const BASE = `${import.meta.env.VITE_API_URL}/api`;

// Data user dari localStorage
const userData = ref(null);
const form = ref({ nama_lengkap: "", username: "" });
const fotoSaatIni = ref(null);

const roleUser = computed(() => userData.value?.role || "admin");
const roleLabel = computed(() =>
  (userData.value?.role || "admin").toUpperCase(),
);

onMounted(() => {
  const raw = localStorage.getItem("user");
  if (raw) {
    try {
      userData.value = JSON.parse(raw);
      form.value.nama_lengkap = userData.value.nama_lengkap || "";
      form.value.username = userData.value.username || "";
      fotoSaatIni.value = userData.value.foto || null;
    } catch (e) {
      userData.value = null;
    }
  }
  // Pasang token ke axios biar request profil terautentikasi
  const token = localStorage.getItem("token");
  if (token) axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
});

// === FOTO ===
const fotoInput = ref(null);
const fileFoto = ref(null);
const previewFoto = ref(null);

const pilihFoto = () => fotoInput.value.click();
const handleFoto = (e) => {
  const file = e.target.files[0];
  if (file) {
    fileFoto.value = file;
    previewFoto.value = URL.createObjectURL(file);
  }
};
const batalFoto = () => {
  fileFoto.value = null;
  previewFoto.value = null;
  if (fotoInput.value) fotoInput.value.value = null;
};

// === SIMPAN PROFIL ===
const savingProfil = ref(false);
const simpanProfil = async () => {
  if (!form.value.nama_lengkap || !form.value.username) {
    Swal.fire({
      icon: "warning",
      title: "Data Belum Lengkap",
      text: "Nama lengkap dan username wajib diisi.",
      confirmButtonColor: "#059669",
    });
    return;
  }

  const fd = new FormData();
  fd.append("nama_lengkap", form.value.nama_lengkap);
  fd.append("username", form.value.username);
  if (fileFoto.value) fd.append("foto", fileFoto.value);

  savingProfil.value = true;
  try {
    const res = await axios.post(`${BASE}/profile/update`, fd, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    // Perbarui data di localStorage biar navbar & sapaan ikut berubah
    const updated = res.data.user;
    localStorage.setItem("user", JSON.stringify(updated));
    userData.value = updated;
    fotoSaatIni.value = updated.foto || null;
    batalFoto();

    await Swal.fire({
      icon: "success",
      title: "Tersimpan!",
      text: "Profil berhasil diperbarui.",
      confirmButtonColor: "#059669",
      timer: 2000,
      timerProgressBar: true,
    });
  } catch (error) {
    const pesan =
      error.response?.data?.message ||
      (error.response?.data?.errors
        ? Object.values(error.response.data.errors)[0][0]
        : "Gagal menyimpan profil.");
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: pesan,
      confirmButtonColor: "#059669",
    });
  } finally {
    savingProfil.value = false;
  }
};

// === GANTI PASSWORD ===
const showLama = ref(false);
const showBaru = ref(false);
const savingPass = ref(false);
const pass = ref({
  password_lama: "",
  password_baru: "",
  password_baru_confirmation: "",
});

const simpanPassword = async () => {
  if (
    !pass.value.password_lama ||
    !pass.value.password_baru ||
    !pass.value.password_baru_confirmation
  ) {
    Swal.fire({
      icon: "warning",
      title: "Lengkapi Form",
      text: "Semua kolom password wajib diisi.",
      confirmButtonColor: "#059669",
    });
    return;
  }
  if (pass.value.password_baru !== pass.value.password_baru_confirmation) {
    Swal.fire({
      icon: "warning",
      title: "Tidak Cocok",
      text: "Konfirmasi password baru tidak sama.",
      confirmButtonColor: "#059669",
    });
    return;
  }
  if (pass.value.password_baru.length < 6) {
    Swal.fire({
      icon: "warning",
      title: "Password Terlalu Pendek",
      text: "Password baru minimal 6 karakter.",
      confirmButtonColor: "#059669",
    });
    return;
  }

  savingPass.value = true;
  try {
    await axios.post(`${BASE}/profile/password`, pass.value);

    pass.value = {
      password_lama: "",
      password_baru: "",
      password_baru_confirmation: "",
    };

    await Swal.fire({
      icon: "success",
      title: "Berhasil!",
      text: "Password berhasil diubah. Gunakan password baru saat login berikutnya.",
      confirmButtonColor: "#059669",
    });
  } catch (error) {
    const pesan = error.response?.data?.message || "Gagal mengubah password.";
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: pesan,
      confirmButtonColor: "#059669",
    });
  } finally {
    savingPass.value = false;
  }
};

// Kembali ke dashboard sesuai role
const kembali = () => {
  const tujuan = {
    admin: "/admin",
    sekdes: "/sekdes",
    kades: "/kades",
    warga: "/warga",
    developer: "/dev",
  };
  router.push(tujuan[userData.value?.role] || "/admin");
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.profil-body {
  font-family: "Plus Jakarta Sans", sans-serif;
  background: var(--bg-page, #f8fafc);
  min-height: 100vh;
  padding: 110px 20px 60px;
  box-sizing: border-box;
}
.profil-wrapper {
  max-width: 1000px;
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

/* HEADER */
.profil-header {
  background: var(--header-bg, linear-gradient(135deg, #0f172a, #1e293b));
  border-radius: 20px;
  padding: 30px 35px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 15px;
  margin-bottom: 25px;
  box-shadow: 0 15px 35px -10px rgba(15, 23, 42, 0.3);
  border-bottom: 4px solid var(--primary, #059669);
}
.ph-text h1 {
  margin: 0 0 5px;
  font-size: 24px;
  font-weight: 800;
  color: #fff;
  display: flex;
  align-items: center;
  gap: 12px;
}
.ph-text h1 i {
  color: #6ee7b7;
}
.ph-text p {
  margin: 0;
  font-size: 14px;
  color: #cbd5e1;
}
.btn-back {
  background: rgba(255, 255, 255, 0.12);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #fff;
  padding: 11px 22px;
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
.btn-back:hover {
  background: var(--primary, #059669);
  border-color: var(--primary, #059669);
  transform: translateY(-2px);
}

/* GRID */
.profil-grid {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 25px;
  align-items: start;
}

/* KARTU IDENTITAS */
.card-identitas {
  background: #fff;
  border: 1px solid var(--border, #e2e8f0);
  border-radius: 20px;
  padding: 30px 25px;
  text-align: center;
  box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
  position: sticky;
  top: 110px;
}
.avatar-big {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  margin: 0 auto 18px;
  background: var(--primary-soft, #ecfdf5);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 50px;
  color: var(--primary, #059669);
  overflow: hidden;
  border: 4px solid var(--primary-border, #a7f3d0);
}
.avatar-big img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.card-identitas h2 {
  margin: 0 0 10px;
  font-size: 19px;
  font-weight: 800;
  color: var(--navy, #0f172a);
}
.role-chip {
  display: inline-block;
  font-size: 11px;
  font-weight: 800;
  padding: 4px 12px;
  border-radius: 50px;
  letter-spacing: 0.5px;
  margin-bottom: 15px;
}
.role-chip.admin {
  background: #eff6ff;
  color: #2563eb;
}
.role-chip.sekdes {
  background: #f0fdf4;
  color: #059669;
}
.role-chip.kades {
  background: #fffbeb;
  color: #b45309;
}
.role-chip.warga {
  background: #f1f5f9;
  color: #475569;
}
.role-chip.developer {
  background: #faf5ff;
  color: #9333ea;
}
.username-tag {
  font-size: 13px;
  font-weight: 600;
  color: var(--text-muted, #64748b);
  background: var(--bg-page, #f8fafc);
  padding: 8px 14px;
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

/* KARTU FORM */
.card-form {
  display: flex;
  flex-direction: column;
  gap: 25px;
}
.form-block {
  background: #fff;
  border: 1px solid var(--border, #e2e8f0);
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
}
.block-title {
  font-size: 15px;
  font-weight: 800;
  color: var(--primary-dark, #047857);
  background: var(--primary-soft, #ecfdf5);
  border: 1px solid var(--primary-border, #a7f3d0);
  padding: 12px 18px;
  border-radius: 12px;
  margin-bottom: 25px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.btn-pass + .block-title,
.form-block:last-child .block-title {
  color: var(--navy, #0f172a);
}

.input-group {
  margin-bottom: 20px;
}
.input-group label {
  display: block;
  font-size: 12px;
  font-weight: 800;
  color: var(--text-body, #334155);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}
.input-wrap {
  position: relative;
}
.input-wrap > i:first-child {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-faint, #94a3b8);
  font-size: 15px;
}
.input-wrap input {
  width: 100%;
  padding: 13px 45px;
  border: 2px solid var(--border, #e2e8f0);
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  color: var(--navy, #0f172a);
  background: var(--bg-page, #f8fafc);
  outline: none;
  transition: 0.3s;
  box-sizing: border-box;
  font-family: inherit;
}
.input-wrap input:focus {
  background: #fff;
  border-color: var(--primary, #059669);
  box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.12);
}
.toggle-eye {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-faint, #94a3b8);
  cursor: pointer;
  font-size: 15px;
}
.toggle-eye:hover {
  color: var(--navy, #0f172a);
}
.warn-text {
  display: block;
  margin-top: 7px;
  font-size: 11px;
  color: var(--text-muted, #64748b);
  font-weight: 600;
}

/* FOTO UPLOADER */
.foto-uploader {
  display: flex;
  align-items: center;
  gap: 18px;
}
.foto-prev {
  width: 80px;
  height: 80px;
  border-radius: 14px;
  background: var(--bg-page, #f8fafc);
  border: 2px dashed var(--primary-border, #a7f3d0);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  flex-shrink: 0;
  color: var(--text-faint, #94a3b8);
  font-size: 24px;
}
.foto-prev img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.foto-actions {
  display: flex;
  flex-direction: column;
  gap: 8px;
  align-items: flex-start;
}
.btn-pilih-foto,
.btn-batal-foto {
  border: none;
  padding: 9px 16px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-family: inherit;
  transition: 0.2s;
}
.btn-pilih-foto {
  background: var(--primary, #059669);
  color: #fff;
}
.btn-pilih-foto:hover {
  background: var(--primary-dark, #047857);
}
.btn-batal-foto {
  background: #fef2f2;
  color: #ef4444;
}
.foto-hint {
  font-size: 11px;
  color: var(--text-faint, #94a3b8);
  font-weight: 600;
}

/* BUTTON SIMPAN */
.btn-simpan {
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 12px;
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
  font-size: 14px;
  font-weight: 800;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  box-shadow: 0 8px 20px rgba(5, 150, 105, 0.3);
  transition: 0.3s;
  font-family: inherit;
}
.btn-simpan:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 25px rgba(5, 150, 105, 0.4);
}
.btn-simpan:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}
.btn-pass {
  background: linear-gradient(135deg, #1e293b, #0f172a);
  box-shadow: 0 8px 20px rgba(15, 23, 42, 0.3);
}
.btn-pass:hover {
  box-shadow: 0 12px 25px rgba(15, 23, 42, 0.4);
}

/* RESPONSIVE */
@media (max-width: 1100px) {
  .profil-header {
    flex-wrap: wrap;
    gap: 12px;
  }
}

@media (max-width: 900px) {
  .profil-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  .btn-back {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 820px) {
  .profil-grid {
    grid-template-columns: 1fr;
  }
  .card-identitas {
    position: static;
  }
}
@media (max-width: 480px) {
  .profil-body {
    padding: 95px 14px 30px;
  }
  .profil-header {
    flex-direction: column;
    align-items: flex-start;
  }
  .btn-back {
    width: 100%;
    justify-content: center;
  }
  .form-block {
    padding: 22px 18px;
  }
  .foto-uploader {
    flex-direction: column;
    align-items: flex-start;
  }
}

@media (max-width: 360px) {
  .profil-header h2 {
    font-size: 17px;
  }
  .form-block {
    padding: 16px 14px;
  }
}
</style>
