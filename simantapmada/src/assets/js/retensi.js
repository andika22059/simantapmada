// =====================================================================
// Helper status retensi arsip (deteksi otomatis berdasarkan tgl_retensi)
// Dipakai di Dashboard Arsip & lonceng notifikasi global (Topbar).
//
//   statusRetensi(arsip) -> "kedaluwarsa" | "segera" | "aman"
//   hitungRetensi(list)  -> { kedaluwarsa, segera }
// =====================================================================

// Ambang "akan jatuh tempo" (hari)
export const AMBANG_SEGERA = 30;

export function statusRetensi(a) {
  if (!a) return "aman";
  // Arsip permanen tidak pernah kedaluwarsa
  if (a.status === "Permanen" || a.nasib_akhir === "Permanen") return "aman";
  // Sudah ditandai manual
  if (a.status === "Expired" || a.status === "Musnah") return "kedaluwarsa";
  if (!a.tgl_retensi) return "aman";
  const t = new Date(a.tgl_retensi);
  if (isNaN(t)) return "aman";
  const now = new Date();
  now.setHours(0, 0, 0, 0);
  t.setHours(0, 0, 0, 0);
  const selisihHari = Math.round((t - now) / 86400000);
  if (selisihHari < 0) return "kedaluwarsa";
  if (selisihHari <= AMBANG_SEGERA) return "segera";
  return "aman";
}

export function hitungRetensi(list) {
  const r = { kedaluwarsa: 0, segera: 0 };
  (list || []).forEach((a) => {
    const s = statusRetensi(a);
    if (s === "kedaluwarsa") r.kedaluwarsa++;
    else if (s === "segera") r.segera++;
  });
  return r;
}
