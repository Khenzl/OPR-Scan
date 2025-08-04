<?php
error_reporting(0);

// Warna
define("R", "\033[1;31m"); // Merah
define("G", "\033[1;32m"); // Hijau
define("Y", "\033[1;33m"); // Kuning
define("B", "\033[1;34m"); // Biru
define("W", "\033[0m");    // Reset

// Bersihkan layar otomatis
if (stripos(PHP_OS, 'WIN') === 0) {
    system('cls');
} else {
    system('clear');
}

// Handle Ctrl+C
pcntl_signal(SIGINT, function () {
    echo R . "\n\n[!] Keluar. Sampai jumpa!\n" . W;
    exit;
});

function input($msg) {
    echo Y . "$msg " . W;
    return trim(fgets(STDIN));
}

function simpan_hasil($target, $data) {
    if (!is_dir("results")) mkdir("results");
    $file = "results/" . $target . ".txt";
    file_put_contents($file, $data . "\n", FILE_APPEND);
}

$banner_shown = false;

function banner() {
    global $banner_shown;
    if ($banner_shown) return;
    $banner_shown = true;
    echo B;
    echo "                                                               \n";
    echo "                                                               \n";
    echo "  ██████╗ ██████╗ ██████╗    ███████╗ ██████╗ █████╗ ███╗   ██╗\n";
    echo " ██╔═══██╗██╔══██╗██╔══██╗   ██╔════╝██╔════╝██╔══██╗████╗  ██║\n";
    echo " ██║   ██║██████╔╝██████╔╝   ███████╗██║     ███████║██╔██╗ ██║\n";
    echo " ██║   ██║██╔═══╝ ██╔══██╗   ╚════██║██║     ██╔══██║██║╚██╗██║\n";
    echo " ╚██████╔╝██║     ██║  ██║██╗███████║╚██████╗██║  ██║██║ ╚████║\n";
    echo "  ╚═════╝ ╚═╝     ╚═╝  ╚═╝╚═╝╚══════╝ ╚═════╝╚═╝  ╚═╝╚═╝  ╚═══╝\n";
    echo Y . "                 Created by Oken PR | Name Code: Khenzl.\n" . W;
    echo Y . "                 Team Cyber         | Sang Topi Hitam.\n" . W;
    echo Y . "                 OPR-Scan v1.0      | Tanggal 27 Juli 2025.\n\n" . W;
}

function cek_koneksi() {
    return @fsockopen("8.8.8.8", 53);
}

// === MULAI TOOLS ===
banner();

if (!cek_koneksi()) {
    echo R . "[!] Tidak ada koneksi internet!\n" . W;
    exit;
}

// === FUNGSI-FUNGSI ===
function whois_lookup($target) {
    echo B . "\n[ WHOIS Lookup ]\n" . W;

    // Buat folder results jika belum ada
    if (!is_dir('results')) {
        mkdir('results');
    }

    // Ambil dari API
    $url = "https://api.hackertarget.com/whois/?q=$target";
    $res = @file_get_contents($url);

    if ($res && !str_contains($res, 'API count exceeded')) {
        echo G . "[+] WHOIS via API:\n\n" . W;
        echo $res;

        // Simpan hasil ke file results/whois-nama_target.txt
        $filename = "results/whois-" . str_replace(['http://', 'https://', '/', ':'], '_', $target) . ".txt";
        file_put_contents($filename, $res);

        echo G . "\n[✔] Hasil disimpan di: $filename\n" . W;
        return;
    }

    // Jika API gagal, beri peringatan
    echo R . "[!] Gagal mengambil WHOIS dari API. WHOIS lokal tidak tersedia di perangkat ini.\n" . W;
}

function dns_lookup($target) {
    echo B . "\n[ DNS Lookup ]\n" . W;
    $res = @file_get_contents("https://api.hackertarget.com/dnslookup/?q=$target");
    echo $res ?: Y . "[!] Gagal mengambil data DNS\n" . W;
    simpan_hasil($target, $res);
}

function zone_transfer($target) {
    echo B . "\n[ Zone Transfer ]\n" . W;
    $res = @file_get_contents("https://api.hackertarget.com/zonetransfer/?q=$target");
    echo $res ?: Y . "[!] Tidak ada hasil zone transfer atau gagal\n" . W;
    simpan_hasil($target, $res);
}

function traceroute($target) {
    echo B . "\n[ Traceroute ]\n" . W;
    $res = @file_get_contents("https://api.hackertarget.com/mtr/?q=$target");
    echo $res ?: Y . "[!] Gagal melakukan traceroute\n" . W;
    simpan_hasil($target, $res);
}

function port_scan($target) {
    echo B . "\n[ Port Scan ]\n" . W;
    $res = @file_get_contents("https://api.hackertarget.com/nmap/?q=$target");
    echo $res ?: Y . "[!] Port Scan gagal\n" . W;
    simpan_hasil($target, $res);
}

function link_grabber($target) {
    echo B . "\n[ Link Grabber ]\n" . W;
    $res = @file_get_contents("https://api.hackertarget.com/pagelinks/?q=http://$target");
    echo $res ?: Y . "[!] Tidak bisa mengambil link\n" . W;
    simpan_hasil($target, $res);
}

function ip_geolocation($target) {
    echo B . "\n[ IP Geolocation ]\n" . W;
    $ip = gethostbyname($target);
    $json = @file_get_contents("http://ip-api.com/json/$ip");
    $data = json_decode($json, true);
    if ($data && $data['status'] == 'success') {
        ob_start();
        foreach ($data as $k => $v) echo ucfirst($k) . ": $v\n";
        $hasil = ob_get_clean();
        echo $hasil;
        simpan_hasil($target, $hasil);
    } else echo Y . "[!] Gagal mengambil lokasi IP\n" . W;
}

function header_grabber($target) {
    echo B . "\n[ HTTP Header Grabber ]\n" . W;
    $headers = @get_headers("http://$target");
    if ($headers) {
        $hasil = implode("\n", $headers);
        echo $hasil . "\n";
        simpan_hasil($target, $hasil);
    } else echo Y . "[!] Tidak bisa mengambil header\n" . W;
}

function cert_info($target) {
    echo B . "\n[ SSL Certificate Info ]\n" . W;
    $res = @file_get_contents("https://api.hackertarget.com/ssllookup/?q=$target");
    echo $res ?: Y . "[!] Gagal mengambil SSL info\n" . W;
    simpan_hasil($target, $res);
}

function reverse_ip($target) {
    echo B . "\n[ Reverse IP Lookup ]\n" . W;
    $res = @file_get_contents("https://api.hackertarget.com/reverseiplookup/?q=$target");
    echo $res ?: Y . "[!] Gagal mengambil data reverse IP\n" . W;
    simpan_hasil($target, $res);
}

function browser_spy($target) {
    echo B . "\n[ BrowserSpy (User Info) ]\n" . W;
    $res = @file_get_contents("https://api.ip.sb/geoip");
    $json = json_decode($res, true);
    if ($json) {
        ob_start();
        foreach ($json as $k => $v) echo ucfirst($k) . ": $v\n";
        $hasil = ob_get_clean();
        echo $hasil;
        simpan_hasil($target, $hasil);
    } else echo Y . "[!] Gagal mengambil data client info\n" . W;
}

function subdomain_finder($target) {
    echo B . "\n[ Subdomain Finder ]\n" . W;

    $url = "https://api.hackertarget.com/hostsearch/?q=$target";
    $res = @file_get_contents($url);

    if ($res && !str_contains(strtolower($res), 'error') && trim($res) != "") {
        echo G . "[+] Subdomain ditemukan:\n\n" . W;
        echo $res;
        simpan_hasil($target, $res, 'subdomain_finder');
    } else {
        echo R . "[!] Gagal menemukan subdomain.\n" . W;
    }
}

function cms_detector($target) {
    echo B . "\n[ CMS Detector ]\n" . W;

    $url = "https://whatcms.org/APIEndpoint"; // contoh placeholder
    echo Y . "[!] Fitur ini memerlukan API key dari WhatCMS.org. Ganti URL/API-key bila sudah tersedia.\n" . W;

    // Alternatif sederhana (bisa ditingkatkan)
    $res = @file_get_contents("http://$target");

    if ($res) {
        if (str_contains($res, 'wp-content')) {
            echo G . "[+] CMS Terdeteksi: WordPress\n" . W;
            simpan_hasil($target, "WordPress", 'cms_detector');
        } elseif (str_contains($res, 'Joomla')) {
            echo G . "[+] CMS Terdeteksi: Joomla\n" . W;
            simpan_hasil($target, "Joomla", 'cms_detector');
        } else {
            echo Y . "[-] Tidak dapat menentukan CMS dengan pasti.\n" . W;
        }
    } else {
        echo R . "[!] Gagal mengambil konten dari target.\n" . W;
    }
}

function dir_bruteforce($target) {
    echo B . "\n[ Directory Bruteforce ]\n" . W;

    $wordlist = ['admin', 'login', 'dashboard', 'config', 'upload', 'images'];
    foreach ($wordlist as $dir) {
        $url = "http://$target/$dir";
        $headers = @get_headers($url);
        if ($headers && strpos($headers[0], '200')) {
            echo G . "[+] Ditemukan: /$dir\n" . W;
            simpan_hasil($target, "/$dir", 'dir_bruteforce');
        } else {
            echo Y . "[-] Tidak ditemukan: /$dir\n" . W;
        }
    }
}

function webtech_detector($target) {
    echo B . "\n[ Web Technology Detector ]\n" . W;

    $url = "https://api.wappalyzer.com/v2/lookup/?url=$target"; // placeholder
    echo Y . "[!] Fitur ini bisa menggunakan Wappalyzer API (butuh API key).\n" . W;

    $headers = @get_headers("http://$target", 1);
    if ($headers) {
        echo G . "[+] Header Terdeteksi:\n" . W;
        foreach ($headers as $key => $value) {
            echo C . "$key: $value\n" . W;
        }
        simpan_hasil($target, print_r($headers, true), 'webtech_detector');
    } else {
        echo R . "[!] Tidak bisa mengambil header.\n" . W;
    }
}

function http_methods_checker($target) {
    echo B . "\n[ HTTP Methods Checker ]\n" . W;

    $methods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'TRACE'];
    foreach ($methods as $method) {
        $context = stream_context_create(['http' => ['method' => $method]]);
        $fp = @fopen("http://$target", 'r', false, $context);

        if ($fp) {
            echo G . "[+] Method Diizinkan: $method\n" . W;
            fclose($fp);
            simpan_hasil($target, "Method Allowed: $method", 'http_methods_checker');
        } else {
            echo Y . "[-] Method Tidak Diizinkan: $method\n" . W;
        }
    }
}

function admin_panel_finder($target) {
    echo B . "\n[ Admin Panel Finder ]\n" . W;

    $panels = [
        'admin', 'admin/login', 'administrator', 'admin1', 'admin2', 'admincp',
        'admin_area', 'adminLogin', 'adminpanel', 'cp', 'admin123', 'login.php',
        'admin.php', 'useradmin', 'wp-admin'
    ];

    $found = false;

    foreach ($panels as $path) {
        $url = "http://$target/$path";
        $headers = @get_headers($url);

        if ($headers && strpos($headers[0], '200')) {
            echo G . "[+] Panel Admin ditemukan: /$path\n" . W;
            simpan_hasil($target, "/$path", 'admin_panel_finder');
            $found = true;
        } else {
            echo Y . "[-] Tidak ditemukan: /$path\n" . W;
        }
    }

    if (!$found) {
        echo R . "[!] Tidak ditemukan panel admin umum.\n" . W;
    }
}

// === MAIN ===
banner();

if (!cek_koneksi()) {
    echo R . "[!] Tidak ada koneksi internet!\n" . W;
    exit;
}

$target = input("Masukkan domain target (cth: " . R . "example.com" . W . "): ");

while (true) {
    pcntl_signal_dispatch(); // untuk Ctrl+C handler

    echo G . "\n============== MENU OPRScan ==============\n\n" . W;
    echo "[01]. Whois Lookup\n";
    echo "[02]. DNS Lookup\n";
    echo "[03]. Zone Transfer\n";
    echo "[04]. Traceroute\n";
    echo "[05]. Port Scan\n";
    echo "[06]. Link Grabber\n";
    echo "[07]. IP Geolocation\n";
    echo "[08]. HTTP Header Grabber\n";
    echo "[09]. SSL Certificate Info\n";
    echo "[10]. Reverse IP Domain Check\n";
    echo "[11]. BrowserSpy (Client Info)\n";
    echo "[12]. Subdomain Finder\n";
    echo "[13]. CMS Detector\n";
    echo "[14]. Directory Bruteforce\n";
    echo "[15]. Web Tech Detector\n";
    echo "[16]. HTTP Methods Checker\n";
    echo "[17]. Admin Panel Find\n";
    echo R . "[00]. Keluar\n" . W;

    $pilih = input("Pilih menu, contoh [01-16]: ");

    switch ($pilih) {
        case '01': whois_lookup($target); break;
        case '02': dns_lookup($target); break;
        case '03': zone_transfer($target); break;
        case '04': traceroute($target); break;
        case '05': port_scan($target); break;
        case '06': link_grabber($target); break;
        case '07': ip_geolocation($target); break;
        case '08': header_grabber($target); break;
        case '09': cert_info($target); break;
        case '10': reverse_ip($target); break;
        case '11': browser_spy($target); break;
        case '12': subdomain_finder($target); break;
        case '13': cms_detector($target); break;
        case '14': dir_bruteforce($target); break;
        case '15': webtech_detector($target); break;
        case '16': http_methods_checker($target); break;
        case '17': admin_panel_finder($target); break;
        case '00':
            echo R . "\n[!] Keluar, Happy nice day!!..\n" . W;
            exit;
        default:
            echo Y . "[!] Pilihan tidak valid. Coba lagi.\n" . W;
    }
}
?>
