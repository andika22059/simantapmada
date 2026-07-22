<template>
  <Topbar />
  <div class="akun-body">
    <div class="akun-wrap">
      <!-- HEADER -->
      <div class="page-head fade-up">
        <div class="head-text">
          <h2><i class="fa-solid fa-users-gear"></i> Kelola Akun Pengguna</h2>
          <p>Tambah, ubah, reset password, dan hapus akun semua peran.</p>
        </div>
        <button class="btn-back" @click="router.push('/dev')">
          <i class="fa-solid fa-arrow-left"></i> Dashboard
        </button>
      </div>

      <!-- TOOLBAR -->
      <div class="toolbar fade-up delay-1">
        <button class="btn-primary" @click="bukaTambah">
          <i class="fa-solid fa-user-plus"></i> Tambah Akun
        </button>
        <div class="toolbar-right">
          <div class="input-ic">
            <i class="fa-solid fa-filter"></i>
            <select v-model="filterRole" class="sel">
              <option value="">Semua Peran</option>
              <option v-for="r in roles" :key="r" :value="r">
                {{ labelRole(r) }}
              </option>
            </select>
          </div>
          <div class="input-ic">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input v-model="cari" type="text" placeholder="Cari nama / username..." />
          </div>
        </div>
      </div>

      <!-- TABEL -->
      <div class="table-card fade-up delay-2">
        <div class="table-scroll">
          <table class="tbl">
            <thead>
              <tr>
                <th class="c-no">No</th>
                <th>Nama Lengkap</th>
                <th>Username</th>
                <th>Peran</th>
                <th class="ta-c">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <template v-if="loading">
                <tr v-for="n in 6" :key="'sk-' + n" class="skeleton-row">
                  <td v-for="c in 5" :key="c"><span class="skeleton-bar"></span></td>
                </tr>
              </template>
              <tr v-else-if="terfilter.length === 0">
                <td colspan="5" class="empty">
                  <i class="fa-solid fa-user-slash"></i>
                  <p>Tidak ada akun yang cocok.</p>
                </td>
              </tr>
              <tr v-for="(u, i) in terfilter" :key="u.id">
                <td class="c-no">{{ i + 1 }}</td>
                <td class="nm">
                  <div class="ava"><i class="fa-solid fa-user"></i></div>
                  {{ u.nama_lengkap }}
                </td>
                <td class="mono">{{ u.username }}</td>
                <td>
                  <span class="badge" :class="'r-' + u.role">{{
                    labelRole(u.role)
                  }}</span>
                </td>
                <td class="ta-c">
                  <div class="aksi">
                    <button class="ic ic-edit" title="Edit" @click="bukaEdit(u)">
                      <i class="fa-solid fa-pen"></i>
                    </button>
                    <button class="ic ic-key" title="Reset Password" @click="resetPw(u)">
                      <i class="fa-solid fa-key"></i>
                    </button>
                    <button class="ic ic-del" title="Hapus" @click="hapus(u)">
                      <i class="fa-solid fa-trash-can"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- MODAL TAMBAH / EDIT -->
    <div class="ov" :class="{ show: modalOpen }" @click.self="tutup">
      <div class="mdl">
        <div class="mdl-head">
          <h3>
            <i class="fa-solid" :class="editId ? 'fa-user-pen' : 'fa-user-plus'"></i>
            {{ editId ? "Edit Akun" : "Tambah Akun Baru" }}
          </h3>
          <button class="x" @click="tutup"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="mdl-body">
          <div class="fg">
            <label>Nama Lengkap <span>*</span></label>
            <input v-model="form.nama_lengkap" type="text" placeholder="Nama lengkap pengguna" />
          </div>
          <div class="fg">
            <label>Username <span>*</span></label>
            <input v-model="form.username" type="text" placeholder="Username untuk login" autocomplete="off" />
          </div>
          <div class="fg">
            <label>
              Password <span v-if="!editId">*</span>
              <small v-else>(kosongkan bila tidak diubah)</small>
            </label>
            <div class="pw">
              <input
                v-model="form.password"
                :type="lihatPw ? 'text' : 'password'"
                placeholder="Minimal 6 karakter"
                autocomplete="new-password"
              />
              <button type="button" class="pw-eye" @click="lihatPw = !lihatPw">
                <i class="fa-solid" :class="lihatPw ? 'fa-eye-slash' : 'fa-eye'"></i>
              </button>
            </div>
          </div>
          <div class="fg">
            <label>Peran <span>*</span></label>
            <select v-model="form.role" class="sel">
              <option v-for="r in roles" :key="r" :value="r">{{ labelRole(r) }}</option>
            </select>
          </div>
        </div>
        <div class="mdl-foot">
          <button class="btn-batal" @click="tutup">Batal</button>
          <button class="btn-simpan" :disabled="menyimpan" @click="simpan">
            <i v-if="menyimpan" class="fa-solid fa-spinner fa-spin"></i>
            <i v-else class="fa-solid fa-floppy-disk"></i>
            {{ menyimpan ? "Menyimpan..." : "Simpan" }}
          </button>
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

const roles = ["admin", "sekdes", "kades", "warga", "developer"];
const labelRole = (r) =>
  ({
    admin: "Admin",
    sekdes: "Sekretaris Desa",
    kades: "Kepala Desa",
    warga: "Warga",
    developer: "Developer",
  })[r] || r;

const users = ref([]);
const loading = ref(true);
const cari = ref("");
const filterRole = ref("");

const terfilter = computed(() => {
  const q = cari.value.toLowerCase();
  return users.value.filter((u) => {
    const cocokCari =
      (u.nama_lengkap || "").toLowerCase().includes(q) ||
      (u.username || "").toLowerCase().includes(q);
    const cocokRole = !filterRole.value || u.role === filterRole.value;
    return cocokCari && cocokRole;
  });
});

const muat = async () => {
  loading.value = true;
  try {
    const res = await axios.get(`${BASE}/users`);
    users.value = res.data?.data || [];
  } catch (e) {
    users.value = [];
    Swal.fire({
      icon: "error",
      title: "Gagal Memuat",
      text:
        e.response?.data?.message ||
        "Tidak bisa memuat daftar akun. Pastikan Anda login sebagai Developer.",
      confirmButtonColor: "#059669",
    });
  } finally {
    loading.value = false;
  }
};
onMounted(muat);

// ===== Modal tambah/edit =====
const modalOpen = ref(false);
const editId = ref(null);
const menyimpan = ref(false);
const lihatPw = ref(false);
const form = ref({ nama_lengkap: "", username: "", password: "", role: "warga" });

const resetForm = () => {
  form.value = { nama_lengkap: "", username: "", password: "", role: "warga" };
  lihatPw.value = false;
};

const bukaTambah = () => {
  editId.value = null;
  resetForm();
  modalOpen.value = true;
};

const bukaEdit = (u) => {
  editId.value = u.id;
  form.value = {
    nama_lengkap: u.nama_lengkap || "",
    username: u.username || "",
    password: "",
    role: u.role || "warga",
  };
  lihatPw.value = false;
  modalOpen.value = true;
};

const tutup = () => {
  modalOpen.value = false;
};

const simpan = async () => {
  // validasi ringan di depan
  if (!form.value.nama_lengkap || !form.value.username) {
    Swal.fire({
      icon: "warning",
      title: "Belum Lengkap",
      text: "Nama lengkap dan username wajib diisi.",
      confirmButtonColor: "#059669",
    });
    return;
  }
  if (!editId.value && (!form.value.password || form.value.password.length < 6)) {
    Swal.fire({
      icon: "warning",
      title: "Password Kurang",
      text: "Password minimal 6 karakter untuk akun baru.",
      confirmButtonColor: "#059669",
    });
    return;
  }

  menyimpan.value = true;
  try {
    if (editId.value) {
      const payload = {
        nama_lengkap: form.value.nama_lengkap,
        username: form.value.username,
        role: form.value.role,
      };
      if (form.value.password) payload.password = form.value.password;
      await axios.put(`${BASE}/users/${editId.value}`, payload);
    } else {
      await axios.post(`${BASE}/users`, form.value);
    }
    modalOpen.value = false;
    await muat();
    Swal.fire({
      toast: true,
      position: "top-end",
      icon: "success",
      title: editId.value ? "Akun diperbarui" : "Akun baru dibuat",
      showConfirmButton: false,
      timer: 2200,
      timerProgressBar: true,
    });
  } catch (e) {
    Swal.fire({
      icon: "error",
      title: "Gagal Menyimpan",
      text:
        e.response?.data?.message ||
        Object.values(e.response?.data?.errors || {})?.[0]?.[0] ||
        "Terjadi kesalahan. Cek data (username mungkin sudah dipakai).",
      confirmButtonColor: "#059669",
    });
  } finally {
    menyimpan.value = false;
  }
};

const resetPw = async (u) => {
  const { value: pw } = await Swal.fire({
    title: `Reset Password`,
    html: `Untuk akun <b>${u.nama_lengkap}</b> (${u.username})`,
    input: "password",
    inputPlaceholder: "Password baru (min. 6 karakter)",
    inputAttributes: { autocomplete: "new-password" },
    showCancelButton: true,
    confirmButtonText: "Reset",
    cancelButtonText: "Batal",
    confirmButtonColor: "#059669",
    inputValidator: (v) =>
      !v || v.length < 6 ? "Minimal 6 karakter" : undefined,
  });
  if (!pw) return;
  try {
    await axios.put(`${BASE}/users/${u.id}/reset-password`, { password: pw });
    Swal.fire({
      toast: true,
      position: "top-end",
      icon: "success",
      title: "Password direset",
      showConfirmButton: false,
      timer: 2200,
    });
  } catch (e) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: e.response?.data?.message || "Gagal mereset password.",
      confirmButtonColor: "#059669",
    });
  }
};

const hapus = async (u) => {
  const konf = await Swal.fire({
    icon: "warning",
    title: "Hapus Akun?",
    html: `Akun <b>${u.nama_lengkap}</b> (${u.username}) akan dihapus permanen.`,
    showCancelButton: true,
    confirmButtonText: "Ya, Hapus",
    cancelButtonText: "Batal",
    confirmButtonColor: "#ef4444",
    cancelButtonColor: "#64748b",
  });
  if (!konf.isConfirmed) return;
  try {
    await axios.delete(`${BASE}/users/${u.id}`);
    await muat();
    Swal.fire({
      toast: true,
      position: "top-end",
      icon: "success",
      title: "Akun dihapus",
      showConfirmButton: false,
      timer: 2200,
    });
  } catch (e) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: e.response?.data?.message || "Gagal menghapus akun.",
      confirmButtonColor: "#059669",
    });
  }
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.akun-body {
  font-family: "Plus Jakarta Sans", sans-serif;
  background: #f8fafc;
  min-height: 100vh;
  padding: 100px 20px 60px;
  box-sizing: border-box;
  color: #0f172a;
}
.akun-wrap {
  max-width: 1100px;
  margin: 0 auto;
}
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
    transform: translateY(14px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* header */
.page-head {
  background: #0f172a;
  background-image: radial-gradient(
    rgba(255, 255, 255, 0.07) 1.2px,
    transparent 1.2px
  );
  background-size: 22px 22px;
  border-radius: 20px;
  padding: 26px 32px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 15px;
  flex-wrap: wrap;
  margin-bottom: 24px;
}
.head-text h2 {
  margin: 0 0 5px;
  color: #fff;
  font-size: 24px;
  font-weight: 800;
  display: flex;
  align-items: center;
  gap: 10px;
}
.head-text h2 i {
  color: #34d399;
}
.head-text p {
  margin: 0;
  color: #94a3b8;
  font-size: 14px;
}
.btn-back {
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.18);
  padding: 11px 20px;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
  font-family: inherit;
  transition: 0.25s;
}
.btn-back:hover {
  background: #fff;
  color: #0f172a;
}

/* toolbar */
.toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 14px;
  flex-wrap: wrap;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 16px 20px;
  margin-bottom: 20px;
}
.toolbar-right {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}
.btn-primary {
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
  border: none;
  padding: 12px 22px;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-family: inherit;
  box-shadow: 0 6px 15px rgba(16, 185, 129, 0.25);
  transition: 0.25s;
}
.btn-primary:hover {
  transform: translateY(-2px);
}
.input-ic {
  position: relative;
}
.input-ic i {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #10b981;
  font-size: 14px;
}
.sel,
.input-ic input {
  padding: 11px 14px 11px 38px;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  background: #f8fafc;
  outline: none;
  font-family: inherit;
  color: #0f172a;
}
.input-ic input {
  width: 240px;
}
.sel {
  appearance: none;
  cursor: pointer;
  padding-right: 30px;
}
.sel:focus,
.input-ic input:focus {
  border-color: #10b981;
  background: #fff;
}

/* tabel */
.table-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  overflow: hidden;
}
.table-scroll {
  overflow-x: auto;
}
.tbl {
  width: 100%;
  border-collapse: collapse;
  min-width: 640px;
}
.tbl th {
  background: #f8fafc;
  text-align: left;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #0f172a;
  font-weight: 800;
  padding: 16px 20px;
  border-bottom: 2px solid #e2e8f0;
}
.tbl td {
  padding: 14px 20px;
  border-bottom: 1px solid #f1f5f9;
  font-size: 14px;
  color: #334155;
  font-weight: 500;
}
.tbl tbody tr:hover {
  background: #f8fafc;
}
.c-no {
  width: 5%;
  text-align: center;
  color: #94a3b8 !important;
  font-weight: 800;
}
.ta-c {
  text-align: center;
}
.nm {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 700;
  color: #0f172a;
}
.ava {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #ecfdf5;
  color: #059669;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  flex-shrink: 0;
}
.mono {
  font-family: monospace;
  color: #1d4ed8;
  font-weight: 700;
}
.badge {
  padding: 5px 12px;
  border-radius: 8px;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.r-admin {
  background: #ecfdf5;
  color: #059669;
}
.r-sekdes {
  background: #eff6ff;
  color: #2563eb;
}
.r-kades {
  background: #fef3c7;
  color: #b45309;
}
.r-warga {
  background: #f1f5f9;
  color: #475569;
}
.r-developer {
  background: #ede9fe;
  color: #6d28d9;
}
.aksi {
  display: flex;
  gap: 6px;
  justify-content: center;
}
.ic {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  font-size: 13px;
  transition: 0.2s;
}
.ic-edit {
  background: #eff6ff;
  color: #2563eb;
}
.ic-key {
  background: #fef3c7;
  color: #b45309;
}
.ic-del {
  background: #fef2f2;
  color: #dc2626;
}
.ic:hover {
  transform: translateY(-2px);
  filter: brightness(0.96);
}
.empty {
  text-align: center;
  padding: 50px 20px !important;
  color: #94a3b8;
}
.empty i {
  font-size: 38px;
  color: #cbd5e1;
  margin-bottom: 12px;
}
.empty p {
  margin: 0;
  font-weight: 600;
}

/* skeleton */
.skeleton-row td {
  padding: 16px 20px;
}
.skeleton-bar {
  display: block;
  height: 14px;
  border-radius: 7px;
  background: linear-gradient(90deg, #eef2f7 25%, #e2e8f0 37%, #eef2f7 63%);
  background-size: 400% 100%;
  animation: shimmer 1.4s ease infinite;
}
@keyframes shimmer {
  0% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0 50%;
  }
}

/* modal */
.ov {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.65);
  backdrop-filter: blur(5px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  opacity: 0;
  visibility: hidden;
  transition: 0.25s;
  z-index: 10000;
}
.ov.show {
  opacity: 1;
  visibility: visible;
}
.mdl {
  background: #fff;
  width: 460px;
  max-width: 100%;
  max-height: 92vh;
  border-radius: 20px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transform: scale(0.96) translateY(12px);
  transition: 0.25s;
}
.ov.show .mdl {
  transform: scale(1) translateY(0);
}
.mdl-head {
  padding: 20px 24px;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.mdl-head h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 800;
  color: #0f172a;
  display: flex;
  align-items: center;
  gap: 10px;
}
.mdl-head h3 i {
  color: #059669;
}
.x {
  background: #f1f5f9;
  border: none;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  cursor: pointer;
  color: #94a3b8;
  transition: 0.2s;
}
.x:hover {
  background: #fee2e2;
  color: #ef4444;
}
.mdl-body {
  padding: 22px 24px;
  overflow-y: auto;
}
.fg {
  margin-bottom: 16px;
}
.fg label {
  display: block;
  font-size: 13px;
  font-weight: 700;
  color: #334155;
  margin-bottom: 7px;
}
.fg label span {
  color: #ef4444;
}
.fg label small {
  color: #94a3b8;
  font-weight: 500;
}
.fg input,
.fg .sel {
  width: 100%;
  padding: 12px 14px;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 14px;
  font-family: inherit;
  outline: none;
  box-sizing: border-box;
  background: #f8fafc;
  color: #0f172a;
}
.fg .sel {
  padding-left: 14px;
}
.fg input:focus,
.fg .sel:focus {
  border-color: #10b981;
  background: #fff;
}
.pw {
  position: relative;
}
.pw-eye {
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  width: 34px;
  height: 34px;
}
.mdl-foot {
  padding: 16px 24px;
  border-top: 1px solid #f1f5f9;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  background: #f8fafc;
}
.btn-batal {
  background: #fff;
  border: 1px solid #cbd5e1;
  color: #475569;
  padding: 11px 20px;
  border-radius: 10px;
  font-weight: 700;
  cursor: pointer;
  font-family: inherit;
}
.btn-simpan {
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
  border: none;
  padding: 11px 24px;
  border-radius: 10px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-family: inherit;
}
.btn-simpan:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

@media (max-width: 600px) {
  .akun-body {
    padding: 90px 14px 40px;
  }
  .toolbar,
  .toolbar-right {
    flex-direction: column;
    align-items: stretch;
    width: 100%;
  }
  .input-ic input,
  .sel {
    width: 100%;
    box-sizing: border-box;
  }
}
</style>
