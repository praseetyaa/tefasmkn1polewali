=== Bukubank Woocommerce - Cek Mutasi Bank dan Pembayaran Secara Otomatis Rekening Indonesia ==
Contributors: tenkuken
Tags: ecommerce, e-commerce, store, sales, sell, shop, cart, checkout, downloadable, downloads, payment, bca, mandiri, bni, bri, otomatis, mutasi, cekmutasi
Requires at least: 4.7
Tested up to: 5.8
Stable tag: 1.0.5
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Plugin ini adalah addon dari bukubank.com sebagai payment gateway woocomerce wordpress. Plugin ini akan melakukan validasi pembayaran bank secara otomatis.

== Description ==

Bukubank merupakan layanan pengelolaan rekening terintegrasi yang membantu Anda mengelola banyak rekening dalam satu dashboard. 
Selain itu, mendukung sistem validasi pembayaran otomatis berdasarkan nominal unik melalui konektivitas API.

Beberapa bank nasional yang kami support, diantaranya :
* BCA,
* MANDIRI,
* BNI,
* BRI,
* dan terus bertambah.

Untuk menggunakan plugin ini, anda di wajibkan untuk melakukan pendaftaran terlebih dahulu di [Bukubank.com](https://bukubank.com).

== Installation ==

= Langkah ke-1 =
Cara menginstall plugin sangatlah mudah.

1. Pastikan Anda telah menginstall plugin WooCommerce karena ini merupakan addon untuk WooCommerce. Versi WooCommerce minimum untuk plugin ini adalah 3.1.0
2. Unggah plugin ini ke folder `/wp-content/plugins/bukubank-woocommerce`, atau install langsung melalui WordPress plugin secara instan.
3. Aktivkan di menu 'Plugins' WordPress Anda.
4. Masuk ke menu WooCommerce -> Settings -> Payments lalu klik Manage pada Bukubank payment.
5. Salin "Payment Notification URL" berupa link contoh: `https://www.tokoku.com/?wc-api=bukubank`
6. Lalu lakukan langkah ke-2 di bawah ini.

= Langkah ke-2 =
Pastikan Anda daftar di web https://bukubank.com dan mempunyai minimal 1 akun rekening yang telah didaftarkan.

1. Kunjungi web https://www.bukubank.com/login lalu login.
2. Edit rekening yang akan digunakan untuk integrasi.
3. Masukkan `Notifikasi URL` pada langkah pertama tadi.
5. Lalu simpan.

Dan silahkan mulai berjualan.


== Frequently Asked Questions ==

= Apakah ada biaya langganan? =
Ya, kami menggunakan sistem deposit. Biaya yang di gunakan adalah 1.500 /rekening/hari.

= Apakah data saya aman? =
Keamanan adalah prioritas utama kami. Kami bertujuan untuk memiliki kepercayaan dan keyakinan Anda dengan melindungi kerahasiaan semua transaksi Anda. Seluruh komunikasi data kami menggunakan SSL, sehingga lebih terjamin keamanannya.

= Berapa kali mutasi akan update? =
Pengecekkan mutasi dilakukan 15 menit sekali.

= Apakah saya bisa akses Internet Banking saya bila menggunakan layanan ini? =
Anda bisa buka iBanking Anda kapanpun, tanpa terganggu oleh bukubank.com. Cukup nonaktifkan mutasi di dashboard bukubank.com lalu Anda bisa login ke ibanking

= Melalui apa saja saya akan menerima notifikasi? =
Sistem akan mengirim notifikasi setiap ada transaksi masuk kepada Anda melalui Email, API, dan Telegram.


== Changelog ==
= 1.0.5 =
* Improve link notes to bukubank.com
* Better compability wordpress and woocommerce

= 1.0.4 =
* Minor Bug fix Mass payment

= 1.0.3 =
* Minor Bug fix

= 1.0.2 =
* Fix Catch IPN

= 1.0.0 =
* Inisialisasi rilis

== Upgrade Notice ==
