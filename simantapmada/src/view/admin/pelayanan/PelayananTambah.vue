<template>
  <div class="form-wrapper animated-fade">
    <div class="form-hero">
      <div class="hero-text">
        <h2>
          <i class="fa-solid fa-file-signature text-emerald"></i>
          {{ judulHalaman }}
        </h2>
        <p>
          Lengkapi formulir di bawah ini dengan data yang valid dan sesuai KTP.
        </p>
      </div>
      <button @click="router.back()" class="btn-back-white" type="button">
        <i class="fa-solid fa-xmark"></i> Batal
      </button>
    </div>

    <div class="form-card">
      <form id="pengajuanForm" ref="formRef" @submit.prevent="submitPengajuan">
        <div
          class="form-grid"
          style="margin-bottom: 25px; position: relative; z-index: 99"
        >
          <div class="input-group full-width">
            <label
              ><i class="fa-solid fa-list-check"></i> Jenis Layanan Surat
              *</label
            >

            <select
              name="jenis_surat"
              v-model="selectedLayanan"
              :disabled="isLocked"
              class="input-control"
              style="cursor: pointer; position: relative; z-index: 99"
              required
            >
              <option value="" disabled selected>
                -- Klik Untuk Memilih Jenis Surat --
              </option>

              <optgroup label="1. KEPENDUDUKAN & IDENTITAS">
                <option value="domisili">Surat Keterangan Domisili</option>
                <option value="domisili_sekolah">
                  Surat Keterangan Domisili (Sekolah)
                </option>
                <option value="kelahiran">Surat Keterangan Kelahiran</option>
                <option value="kematian">Surat Keterangan Kematian</option>
                <option value="pindah">
                  Surat Keterangan Pindah Penduduk
                </option>
                <option value="beda_nama">Surat Keterangan Beda Nama</option>
                <option value="skip">
                  Surat Keterangan Identitas Penduduk (SKIP)
                </option>
              </optgroup>

              <optgroup label="2. PERNIKAHAN & KELUARGA">
                <option value="nikah">Surat Pengantar Nikah (N1-N4)</option>
                <option value="numpang_nikah">
                  Surat Keterangan Numpang Nikah
                </option>
                <option value="belum_menikah">
                  Surat Keterangan Belum Menikah
                </option>
                <option value="nikah_siri">
                  Surat Keterangan Nikah Siri / Non-Catat
                </option>
                <option value="janda_duda">
                  Surat Keterangan Janda / Duda
                </option>
                <option value="ket_menikah">
                  Surat Keterangan Telah Menikah
                </option>
                <option value="cerai_goib">
                  Surat Keterangan Ghaib (Cerai Goib)
                </option>
                <option value="wali">Surat Keterangan Wali</option>
                <option value="ahli_waris">
                  Surat Keterangan Waris (Ahli Waris)
                </option>
              </optgroup>

              <optgroup label="3. EKONOMI & USAHA">
                <option value="usaha">Surat Keterangan Usaha (SKU)</option>
                <option value="sktm">
                  Surat Keterangan Tidak Mampu (SKTM)
                </option>
                <option value="penghasilan">
                  Surat Keterangan Penghasilan Orang Tua
                </option>
                <option value="ket_pekerjaan">
                  Surat Keterangan Pekerjaan
                </option>
                <option value="pengalaman_kerja">
                  Surat Keterangan Pengalaman Kerja
                </option>
              </optgroup>

              <optgroup label="4. PERTANAHAN, ASET & KUASA">
                <option value="jual_beli">Surat Keterangan Jual Beli</option>
                <option value="milik_kendaraan">
                  Surat Keterangan Kepemilikan Kendaraan
                </option>
                <option value="milik_tanah">
                  Surat Keterangan Kepemilikan Tanah
                </option>
                <option value="tanah_sengketa">
                  Surat Keterangan Tidak Sengketa
                </option>
                <option value="pinjam_tanah">
                  Surat Keterangan Pinjam Pakai Tanah
                </option>
                <option value="kuasa">Surat Kuasa</option>
                <option value="kuasa_bpkb">
                  Surat Kuasa Pengurusan BPKB
                </option>
              </optgroup>

              <optgroup label="5. KEPOLISIAN, IZIN & LAINNYA">
                <option value="skck">
                  Surat Keterangan Kelakuan Baik (Pengantar SKCK)
                </option>
                <option value="kehilangan">
                  Surat Keterangan Kehilangan
                </option>
                <option value="keramaian">
                  Surat Keterangan Izin Keramaian
                </option>
                <option value="bepergian">Surat Keterangan Bepergian</option>
                <option value="tidak_dirumah">
                  Surat Keterangan Tidak di Rumah
                </option>
                <option value="izin_sekolah">
                  Surat Keterangan Izin Sekolah
                </option>
                <option value="ket_umum">Surat Keterangan Umum</option>
                <option value="permohonan_izin">
                  Surat Permohonan Izin
                </option>
              </optgroup>
            </select>
          </div>
        </div>

        <div id="dynamicFieldsContainer" v-html="dynamicHtml"></div>

        <div class="input-group full-width" style="margin-top: 15px">
          <label
            ><i class="fa-solid fa-pen-to-square"></i> Keterangan Tambahan
            (Opsional)</label
          >
          <textarea
            name="keterangan"
            class="input-control"
            rows="2"
            placeholder="Tuliskan catatan tambahan jika ada..."
          ></textarea>
        </div>

        <div class="input-group full-width" style="margin-top: 25px">
          <label
            ><i class="fa-solid fa-cloud-arrow-up"></i> Upload Lampiran
            Berkas</label
          >
        </div>
        <div class="upload-area" @click="triggerFileInput">
          <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
          <div class="upload-text">
            Klik atau seret file ke area ini untuk mengunggah dokumen
          </div>
          <div class="upload-hint">
            Format: PDF (Maks. 2MB per file)
          </div>
          <input
            type="file"
            ref="fileInputRef"
            id="file_input"
            multiple
            style="display: none"
            @change="handleFileUpload"
          />
        </div>

        <div class="file-list" v-if="selectedFiles.length > 0">
          <div
            v-for="(file, index) in selectedFiles"
            :key="index"
            class="file-item"
          >
            <i
              class="fa-solid fa-file-invoice text-emerald"
              style="font-size: 16px"
            ></i>
            <span style="flex: 1; text-align: left; padding-left: 8px"
              >{{ file.name }} ({{ (file.size / 1024).toFixed(1) }} KB)</span
            >
            <i
              class="fa-solid fa-trash-can remove-icon"
              @click.stop="removeFile(index)"
            ></i>
          </div>
        </div>

        <button class="btn-submit" type="submit" :disabled="isLoading">
          <span v-if="isLoading"
            ><i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...</span
          >
          <span v-else
            ><i class="fa-solid fa-paper-plane"></i> Simpan & Ajukan</span
          >
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";
import {
  customFields,
  formatRupiah,
  toggleAlamat,
} from "../../../assets/js/admin/form_template.js";

const router = useRouter();
const route = useRoute();

const formRef = ref(null);
const fileInputRef = ref(null);
const isLoading = ref(false);
const selectedLayanan = ref("");
const selectedFiles = ref([]);
const isLocked = ref(false);
const editId = ref(null);

const judulHalaman = computed(() =>
  editId.value ? "Edit Pengajuan" : "Buat Pengajuan Baru",
);

const dynamicHtml = computed(() => {
  if (!selectedLayanan.value) return "";
  return customFields[selectedLayanan.value]
    ? customFields[selectedLayanan.value]
    : customFields["default"];
});

onMounted(() => {
  window.formatRupiah = (element) => formatRupiah(element);
  window.toggleAlamat = (targetName, checkbox) =>
    toggleAlamat(targetName, checkbox);

  if (route.query.edit) {
    muatDataEdit(route.query.edit);
  } else if (route.query.layanan) {
    selectedLayanan.value = route.query.layanan;
    isLocked.value = true;
  } else {
    isLocked.value = false;
  }
});

// ===== MODE EDIT =====
// Isi ulang value input pada form (termasuk field dinamis dari v-html)
const isiForm = (dp) => {
  const form = formRef.value;
  if (!form || !dp) return;
  const setVal = (name, value) => {
    if (value === undefined || value === null) return;
    form.querySelectorAll(`[name="${name}"]`).forEach((el) => {
      if (el.type === "checkbox") {
        el.checked = value !== "0" && value !== "false" && !!value;
      } else if (el.type === "radio") {
        el.checked = String(el.value) === String(value);
      } else {
        el.value = value;
      }
      el.dispatchEvent(new Event("input", { bubbles: true }));
      el.dispatchEvent(new Event("change", { bubbles: true }));
    });
  };
  const pemohon = dp.data_pemohon || {};
  Object.keys(pemohon).forEach((k) => setVal(`data_pemohon[${k}]`, pemohon[k]));
  const detail = dp.detail || {};
  Object.keys(detail).forEach((k) => setVal(`detail[${k}]`, detail[k]));
};

const muatDataEdit = async (id) => {
  try {
    const res = await axios.get(`${import.meta.env.VITE_API_URL}/api/pelayanan/${id}`);
    const d = res.data?.data || res.data;
    if (!d || !d.id) throw new Error("not found");

    // LOCK: kalau sudah diproses Sekdes, tolak edit
    if (d.status !== "Diajukan") {
      await Swal.fire(
        "Terkunci",
        "Pengajuan ini sudah diverifikasi Sekdes, tidak bisa diedit.",
        "info",
      );
      router.push("/admin/pelayanan/list");
      return;
    }

    editId.value = d.id;
    selectedLayanan.value = d.jenis_surat;
    isLocked.value = true;

    // tunggu v-html field dinamis ter-render baru isi value-nya
    await nextTick();
    await nextTick();
    setTimeout(() => {
      let dp = d.data_pengajuan;
      if (typeof dp === "string") {
        try {
          dp = JSON.parse(dp);
        } catch (e) {
          dp = {};
        }
      }
      isiForm(dp || {});
      const ket = formRef.value?.querySelector('[name="keterangan"]');
      if (ket) ket.value = d.keterangan || "";
    }, 80);
  } catch (e) {
    console.error("Gagal memuat data edit:", e);
    Swal.fire("Gagal", "Gagal memuat data pengajuan untuk diedit.", "error");
    router.push("/admin/pelayanan/list");
  }
};

const triggerFileInput = () => {
  fileInputRef.value.click();
};

const handleFileUpload = (event) => {
  const files = Array.from(event.target.files);
  selectedFiles.value = [...selectedFiles.value, ...files];
  event.target.value = "";
};

const removeFile = (index) => {
  selectedFiles.value.splice(index, 1);
};

// 🔥 INI DIA FUNGSI SAKTINYA (YANG TADI LU BELUM COPAS)
const submitPengajuan = async () => {
  isLoading.value = true;
  const formData = new FormData(formRef.value);

  if (isLocked.value) {
    formData.append("jenis_surat", selectedLayanan.value);
  }

  selectedFiles.value.forEach((file) => {
    formData.append("lampiran[]", file);
  });

  // Mode edit → kirim PUT (via method spoofing karena multipart)
  const editing = !!editId.value;
  let url = `${import.meta.env.VITE_API_URL}/api/pelayanan`;
  if (editing) {
    url = `${import.meta.env.VITE_API_URL}/api/pelayanan/${editId.value}`;
    formData.append("_method", "PUT");
  }

  // MUNCULIN LOADING SWEETALERT MEWAH
  Swal.fire({
    title: editing ? "Menyimpan perubahan..." : "Lagi Menyimpan...",
    text: "Tunggu bentar, Pengajuan sedang Diajukan",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  try {
    const response = await axios.post(url, formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    console.log("Respon Sukses:", response.data);

    // POP-UP BERHASIL
    await Swal.fire({
      icon: "success",
      title: "Berhasil!",
      text: editing
        ? "Perubahan pengajuan berhasil disimpan!"
        : "Pengajuan pelayanan berhasil Diajukan",
      showConfirmButton: false,
      timer: 1500,
    });

    router.push(editing ? "/admin/pelayanan/list" : "/admin/pelayanan");
  } catch (error) {
    console.error("Detail Error:", error);

    if (!error.response) {
      Swal.fire({
        icon: "error",
        title: "Server Mati!",
        text: "Pastiin Jaringan aman",
        confirmButtonColor: "#d33",
      });
    } else if (error.response.status === 403) {
      // Pengajuan terkunci (sudah diverifikasi Sekdes saat proses edit)
      await Swal.fire({
        icon: "warning",
        title: "Terkunci",
        text:
          error.response.data?.message ||
          "Pengajuan sudah diproses Sekdes, tidak bisa diedit.",
        confirmButtonColor: "#059669",
      });
      router.push("/admin/pelayanan/list");
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal Menyimpan",
        text: "Pastikan semua field bertanda * sudah diisi!",
        confirmButtonColor: "#d33",
      });
    }
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
@import "../../../assets/css/admin/pelayanantambah.css";
</style>
