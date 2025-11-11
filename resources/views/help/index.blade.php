<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bantuan & Tutorial Aplikasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                {{-- Kita gunakan 'prose' dari Tailwind untuk merapikan teks panjang --}}
                <article class="prose prose-indigo max-w-none p-6 sm:p-8 lg:p-10">
                    <h1>Selamat Datang di Asisten Belajar AI!</h1>
                    <p class="lead">
                        Aplikasi ini dirancang untuk membantu Anda (mahasiswa) memahami materi *textbook* akademik
                        secara mandiri.
                        Berikut adalah panduan lengkap alur penggunaan aplikasi.
                    </p>

                    <hr>

                    <h2>1. 📊 Memahami Dashboard Utama</h2>
                    <p>
                        Setelah login, Anda akan disambut oleh <strong>Dashboard</strong> (<code>/dashboard</code>).
                        Halaman ini adalah pusat ringkasan aktivitas belajar Anda.
                    </p>
                    <ul>
                        <li><strong>Ringkasan Aktivitas Belajar:</strong> Menampilkan jumlah buku yang sudah Anda baca,
                            total tes yang selesai, dan jumlah tes yang berhasil Anda LULUSI (nilai >= 80).</li>
                        <li><strong>Statistik Percobaan Tes:</strong> Melacak total berapa kali Anda mencoba semua tes
                            (Pre-Test & Post-Test) dan nilai rata-ratanya.</li>
                        <li><strong>Progres Membaca per Buku:</strong> Grafik batang (Chart.js) yang menunjukkan progres
                            persentase membaca Anda untuk setiap buku.</li>
                        <li><strong>Tes Belum Lulus / Belum Dikerjakan:</strong> Ini adalah daftar "Tugas" Anda. Semua
                            Post-Test yang belum dikerjakan atau nilainya masih di bawah 80 akan muncul di sini agar
                            Anda bisa langsung mengerjakannya.</li>
                    </ul>

                    <h2>2. 📚 Katalog & Detail Buku</h2>
                    <p>
                        Dari navbar, klik <strong>Katalog Buku</strong> untuk melihat semua materi yang tersedia.
                    </p>
                    <ul>
                        <li>Klik pada judul buku untuk masuk ke <strong>Halaman Detail Buku</strong>.</li>
                        <li>Di sini, Anda akan melihat ringkasan buku, tombol "Baca Buku", dan yang paling penting:
                            <strong>Status Belajar</strong>.</li>
                        <li><strong>Status Belajar:</strong> Ini adalah statistik *khusus* untuk buku ini. Anda bisa
                            melihat status Pre-Test dan status kelulusan (Lulus/Belum Lulus) untuk setiap bab
                            (Post-Test) di buku ini.</li>
                        <li><strong>Reset Progress:</strong> Ada tombol "Reset Progress" jika Anda ingin mengulang
                            membaca buku dan mengerjakan semua tes dari awal.</li>
                    </ul>
                    <h2>3. 🧠 Mengerjakan Pre-Test (Opsional)</h2>
                    <p>
                        Pre-Test dirancang untuk mengukur pemahaman awal Anda *sebelum* membaca buku.
                    </p>
                    <ol>
                        <li>Di halaman Detail Buku, klik tombol <strong>"📖 Baca Buku"</strong>.</li>
                        <li>Jika buku itu memiliki Pre-Test dan Anda belum mengerjakannya, sebuah modal (popup) akan
                            muncul.</li>
                        <li>Anda diberi 2 pilihan:
                            <ul>
                                <li><strong>Kerjakan Pre-Test:</strong> Membawa Anda ke halaman kuis.</li>
                                <li><strong>Langsung Baca:</strong> Melewatkan Pre-Test dan langsung masuk ke PDF
                                    reader.</li>
                            </ul>
                        </li>
                        <li>Pre-Test bersifat opsional dan tidak memiliki *passing grade*.</li>
                    </ol>

                    <h2>4. 📖 Membaca Buku & Interaksi AI</h2>
                    <p>
                        Halaman ini (<code>/books/{id}/read</code>) terbagi menjadi dua bagian utama: PDF Reader di kiri
                        dan Panel AI di kanan.
                    </p>
                    <h3>Fitur PDF Reader (Kiri)</h3>
                    <ul>
                        <li><strong>Navigasi Halaman:</strong> Gunakan tombol panah atau masukkan nomor halaman untuk
                            melompat.</li>
                        <li><strong>Auto-Bookmark:</strong> Progres membaca (halaman terakhir) Anda disimpan secara
                            otomatis.</li>
                        <li><strong>Aksi:</strong> Klik tombol aksi untuk memunculkan dropdown. Didalam dropdown ini
                            anda bisa melakukan beberapa aksi antara lain:
                            <ul>
                                <li><strong>Zoom:</strong>Anda bisa zoom in dan Zoom out.</li>
                                <li><strong>Lompat ke bab:</strong>Anda bisa langsung perpindah antara bab.</li>
                                <li><strong>Post-test bab:</strong>Anda bisa liat status dan kerja post test dengan klik
                                    tombol kerjakan.</li>
                                <li><strong>Kembali ke detail buku:</strong> Kembail ke detail buku.</li>
                            </ul>
                        </li>
                    </ul>
                    <h3>Fitur Panel AI (Kanan)</h3>
                    <p>Panel ini memiliki dua mode (tab) untuk bertanya:</p>
                    <ul>
                        <li><strong>Tab "Tanya Halaman Ini":</strong>Pertanyaan Anda akan dijawab oleh AI
                            <strong>hanya</strong> berdasarkan konteks PDF di halaman yang sedang Anda buka. Ini berguna
                            untuk menanyakan gambar, tabel, atau paragraf spesifik.</li>
                        <li><strong>Tab "Tanya Bab Ini":</strong> Mode lebih luas. Pertanyaan Anda akan dijawab oleh AI
                            berdasarkan konteks <strong>seluruh halaman</strong> dalam bab yang sedang Anda baca (misal,
                            Bab 1 dari hal. 5-20). Ini berguna untuk menanyakan rangkuman bab atau kaitan antar topik
                            dalam bab itu.</li>
                    </ul>

                    <h2>5. 🧪 Mengerjakan Post-Test (Wajib)</h2>
                    <p>
                        Post-Test dirancang untuk mengukur pemahaman Anda *setelah* membaca satu bab.
                    </p>
                    <ul>
                        <li>Post-Test <strong>bisa</strong> diakses dari dalam PDF Reader, Dashboard atau Halaman Detail
                            Buku&lt;.</li>
                        <li>Temukan bab yang ingin Anda tes (misal dari daftar "Tes Belum Lulus") dan klik "Kerjakan
                            Ulang".</li>
                        <li><strong>PENTING:</strong> Post-Test memiliki <strong>Passing Grade 80%</strong>.</li>
                        <li>Jika nilai Anda < 80, status tes tetap "Belum Lulus" dan Anda harus mengulanginya lagi.</li>
                        <li>Jika nilai Anda >= 80, status berubah jadi "Lulus" dan artinya anda sudah cukup mengerti
                            dengan bab tersebut .</li>
                    </ul>
                    <p>
                        Seluruh progres kelulusan tes ini akan ter-update di Dashboard dan Halaman Detail Buku Anda.
                    </p>

                </article>
            </div>
        </div>
    </div>
</x-app-layout>
