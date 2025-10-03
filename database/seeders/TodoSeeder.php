<?php

namespace Database\Seeders;

use App\Models\Todo;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar nama-nama Indonesia
        $assignees = [
            'Budi Santoso',
            'Siti Nurhaliza',
            'Ahmad Dahlan',
            'Dewi Lestari',
            'Rizki Pratama',
            'Fitri Handayani',
            'Andi Wijaya',
            'Nina Kusuma',
            'Arief Hidayat',
            'Putri Maharani',
            'Dimas Prasetyo',
            'Rina Wulandari',
            'Hendra Gunawan',
            'Lestari Indah',
            'Bambang Suryanto'
        ];

        // Daftar judul tugas dalam konteks Indonesia
        $todoTitles = [
            // Pekerjaan Kantor
            'Menyusun laporan keuangan Q3 2025',
            'Review proposal kerjasama vendor',
            'Meeting dengan klien dari Surabaya',
            'Presentasi hasil riset pasar',
            'Update database pelanggan',
            'Koordinasi dengan tim marketing',
            'Audit dokumen administrasi',
            'Rekap data penjualan bulan ini',
            'Buat strategi promosi Ramadan',
            'Evaluasi kinerja tim',
            
            // Project & Development
            'Develop fitur pembayaran QRIS',
            'Testing aplikasi mobile versi terbaru',
            'Deploy website ke production server',
            'Optimasi performa database',
            'Perbaikan bug pada modul laporan',
            'Integrasi API payment gateway',
            'Design mockup aplikasi internal',
            'Code review pull request developer',
            'Setup server backup otomatis',
            'Migrasi data ke cloud',
            
            // Administrasi & HR
            'Proses rekrutmen staff accounting',
            'Pengajuan cuti karyawan bulan ini',
            'Update kebijakan perusahaan',
            'Buat jadwal training karyawan baru',
            'Surat pemberitahuan kenaikan gaji',
            'Perpanjang kontrak vendor cleaning service',
            'Arsip dokumen kepegawaian',
            'Buat form evaluasi karyawan',
            
            // Marketing & Sales
            'Kampanye iklan di media sosial',
            'Follow up leads dari pameran JCC',
            'Buat konten blog perusahaan',
            'Survey kepuasan pelanggan',
            'Negosiasi harga dengan supplier Bandung',
            'Presentasi produk ke calon investor',
            'Analisis kompetitor di e-commerce',
            'Desain banner promosi Harbolnas',
            
            // Operasional
            'Cek inventaris kantor cabang Jakarta',
            'Koordinasi pengiriman barang ke Medan',
            'Maintenance peralatan kantor',
            'Perpanjang sewa gedung kantor',
            'Order supplies ATK untuk 3 bulan',
            'Verifikasi tagihan listrik dan air',
            'Inspeksi keamanan gedung',
            'Update SOP operasional',
            
            // Meeting & Event
            'Rapat evaluasi kinerja bulanan',
            'Persiapan acara gathering tahunan',
            'Workshop peningkatan produktivitas',
            'Seminar pajak untuk UMKM',
            'Town hall meeting dengan direksi',
            'Team building di Puncak',
            'Pelatihan customer service excellence',
            'Webinar digital marketing strategy',
            
            // Legal & Compliance
            'Pengurusan SIUP perusahaan',
            'Review kontrak kerjasama distributor',
            'Konsultasi pajak dengan konsultan',
            'Laporan SPT tahunan perusahaan',
            'Pendaftaran merek dagang produk baru',
            'Audit internal compliance',
            'Update izin operasional',
            
            // Customer Support
            'Handle komplain pelanggan Tokopedia',
            'Respon inquiry via WhatsApp Business',
            'Update FAQ di website',
            'Training product knowledge tim CS',
            'Monitoring rating di Google Review',
            'Buat template email customer support',
            
            // Lain-lain
            'Bayar tagihan internet kantor',
            'Perpanjang domain website',
            'Backup data server bulanan',
            'Update antivirus komputer kantor',
            'Servis AC ruang meeting',
            'Reservasi hotel untuk business trip Bali',
            'Beli kado untuk klien VIP',
            'Fotokopi dokumen tender',
        ];

        $statuses = ['pending', 'open', 'in_progress', 'completed'];
        $priorities = ['low', 'medium', 'high'];

        // Generate 100 todos dengan data yang bervariasi
        for ($i = 0; $i < 100; $i++) {
            // Random status dan priority
            $status = $statuses[array_rand($statuses)];
            $priority = $priorities[array_rand($priorities)];
            
            // Random assignee (80% ada assignee, 20% null)
            $assignee = rand(1, 10) > 2 ? $assignees[array_rand($assignees)] : null;
            
            // Random due date (dari hari ini sampai 90 hari ke depan)
            $dueDate = Carbon::now()->addDays(rand(0, 90))->format('Y-m-d');
            
            // Time tracked berdasarkan status
            // Completed: 60-480 menit (1-8 jam)
            // In Progress: 30-240 menit (0.5-4 jam)
            // Open/Pending: 0-60 menit (0-1 jam)
            if ($status === 'completed') {
                $timeTracked = rand(60, 480);
            } elseif ($status === 'in_progress') {
                $timeTracked = rand(30, 240);
            } else {
                $timeTracked = rand(0, 60);
            }
            
            // Pilih judul secara random
            $title = $todoTitles[array_rand($todoTitles)];
            
            Todo::create([
                'title' => $title,
                'assignee' => $assignee,
                'due_date' => $dueDate,
                'time_tracked' => $timeTracked,
                'status' => $status,
                'priority' => $priority,
            ]);
        }

        // Tambahkan beberapa data spesifik untuk testing yang lebih mudah
        
        // Data untuk testing filter by status = completed
        Todo::create([
            'title' => 'Finalisasi laporan tahunan 2024',
            'assignee' => 'Budi Santoso',
            'due_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
            'time_tracked' => 480,
            'status' => 'completed',
            'priority' => 'high',
        ]);

        // Data untuk testing filter by priority = high dan status = pending
        Todo::create([
            'title' => 'URGENT: Perbaikan server produksi down',
            'assignee' => 'Ahmad Dahlan',
            'due_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'time_tracked' => 0,
            'status' => 'pending',
            'priority' => 'high',
        ]);

        // Data untuk testing filter by assignee
        for ($i = 0; $i < 5; $i++) {
            Todo::create([
                'title' => 'Task khusus untuk Siti Nurhaliza - ' . ($i + 1),
                'assignee' => 'Siti Nurhaliza',
                'due_date' => Carbon::now()->addDays(rand(5, 30))->format('Y-m-d'),
                'time_tracked' => rand(30, 180),
                'status' => $statuses[array_rand($statuses)],
                'priority' => $priorities[array_rand($priorities)],
            ]);
        }

        // Data untuk testing filter by title (contains "laporan")
        $laporanTasks = [
            'Laporan penjualan regional Jawa Timur',
            'Buat laporan keuangan triwulan',
            'Review laporan audit internal',
            'Laporan progress project website',
            'Kompilasi laporan kepuasan pelanggan',
        ];

        foreach ($laporanTasks as $task) {
            Todo::create([
                'title' => $task,
                'assignee' => $assignees[array_rand($assignees)],
                'due_date' => Carbon::now()->addDays(rand(1, 60))->format('Y-m-d'),
                'time_tracked' => rand(60, 300),
                'status' => $statuses[array_rand($statuses)],
                'priority' => $priorities[array_rand($priorities)],
            ]);
        }

        // Data untuk testing filter by date range (Oktober 2025)
        for ($i = 1; $i <= 10; $i++) {
            Todo::create([
                'title' => 'Task bulan Oktober 2025 - Hari ke-' . $i,
                'assignee' => $assignees[array_rand($assignees)],
                'due_date' => '2025-10-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'time_tracked' => rand(0, 240),
                'status' => $statuses[array_rand($statuses)],
                'priority' => $priorities[array_rand($priorities)],
            ]);
        }

        // Data untuk testing time_tracked range
        $timeRangeTasks = [
            ['title' => 'Task ringan 30 menit', 'time' => 30],
            ['title' => 'Task sedang 90 menit', 'time' => 90],
            ['title' => 'Task berat 240 menit', 'time' => 240],
            ['title' => 'Task sangat berat 480 menit', 'time' => 480],
        ];

        foreach ($timeRangeTasks as $task) {
            Todo::create([
                'title' => $task['title'],
                'assignee' => $assignees[array_rand($assignees)],
                'due_date' => Carbon::now()->addDays(rand(1, 30))->format('Y-m-d'),
                'time_tracked' => $task['time'],
                'status' => 'completed',
                'priority' => $priorities[array_rand($priorities)],
            ]);
        }

        echo "✅ Berhasil membuat " . Todo::count() . " data todos dengan identitas Indonesia!\n";
    }
}