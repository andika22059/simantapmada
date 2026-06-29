<template>
  <div class="login-container">
    <Transition name="fade">
      <div v-if="isLoading" class="loading-overlay" @click="isLoading = false">
        <div class="loading-content">
          <img
            src="../../assets/img/logoSiMANTAPMADA.png"
            alt="Logo"
            class="loading-logo"
            @error="$event.target.style.display = 'none'"
          />
          <div class="loading-text">
            <span
              v-for="(char, index) in titleChars"
              :key="index"
              class="l-char"
              :class="{ spasi: char === ' ' }"
              :style="{ animationDelay: `${0.05 * index}s` }"
            >
              {{ char }}
            </span>
          </div>
          <p class="loading-hint">
            <i class="fa-solid fa-hand-pointer"></i> Ketuk layar untuk melewati
          </p>
        </div>
      </div>
    </Transition>

    <div class="village-bg"></div>

    <div class="login-wrapper" :class="{ 'is-visible': !isLoading }">
      <div class="login-card">
        <div class="login-header">
          <img
            src="../../assets/img/logoSiMANTAPMADA.png"
            alt="Logo"
            class="header-logo"
            @error="$event.target.style.display = 'none'"
          />
          <h2>Daftar Akun Baru</h2>
          <p>Sistem Manajemen Arsip Dan Pelayanan Masyarakat Desa</p>
        </div>

        <form @submit.prevent>
          <div class="input-group">
            <label>Nama Lengkap</label>
            <div class="input-field">
              <i class="fa-solid fa-id-card icon-left"></i>
              <input
                type="text"
                placeholder="Masukkan nama lengkap"
                required
                autocomplete="off"
                autofocus
              />
            </div>
          </div>

          <div class="input-group">
            <label>Username</label>
            <div class="input-field">
              <i class="fa-solid fa-user icon-left"></i>
              <input
                type="text"
                placeholder="Buat username"
                required
                autocomplete="off"
              />
            </div>
          </div>

          <div class="input-group">
            <label>Password</label>
            <div class="input-field">
              <i class="fa-solid fa-lock icon-left"></i>
              <input
                :type="showPassword ? 'text' : 'password'"
                placeholder="Buat password"
                required
              />
              <i
                class="fa-regular toggle-password"
                :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"
                @click="showPassword = !showPassword"
                style="cursor: pointer"
              ></i>
            </div>
          </div>

          <div class="input-group">
            <label>Konfirmasi Password</label>
            <div class="input-field">
              <i class="fa-solid fa-shield-halved icon-left"></i>
              <input
                :type="showConfirmPassword ? 'text' : 'password'"
                placeholder="Ulangi password"
                required
              />
              <i
                class="fa-regular toggle-password"
                :class="showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'"
                @click="showConfirmPassword = !showConfirmPassword"
                style="cursor: pointer"
              ></i>
            </div>
          </div>

          <button type="submit" class="btn-login">
            Daftar Sekarang <i class="fa-solid fa-user-plus"></i>
          </button>
        </form>

        <div class="redirect-link">
          <p>
            Sudah punya akun? <a href="#" class="link-text">Masuk di sini</a>
          </p>
        </div>

        <div class="login-footer">
          <p>&copy; {{ new Date().getFullYear() }} Pemerintah Desa Macanan</p>
          <p style="font-size: 10px; margin-top: 5px">
            Universitas Negeri Surabaya
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const isLoading = ref(true);
const showPassword = ref(false);
const showConfirmPassword = ref(false); // State baru khusus untuk Konfirmasi Password

const titleStr = "SiMANTAP MADA";
const titleChars = titleStr.split("");

onMounted(() => {
  setTimeout(() => {
    isLoading.value = false;
  }, 2500);
});
</script>

<style scoped>
.login-wrapper {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
  transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.login-wrapper.is-visible {
  opacity: 1;
  transform: translateY(0) scale(1);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.spasi {
  width: 0.5em;
  display: inline-block;
}

.redirect-link {
  text-align: center;
  margin-top: 15px;
  font-size: 14px;
  color: #555;
}

.redirect-link .link-text {
  color: #0d6efd;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s;
}

.redirect-link .link-text:hover {
  color: #0b5ed7;
  text-decoration: underline;
}

@media (max-width: 360px) {
  .login-card {
    padding: 28px 18px;
    border-radius: 20px;
  }
  .login-header h2 {
    font-size: 20px;
  }
}
</style>
