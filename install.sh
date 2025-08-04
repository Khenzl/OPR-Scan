#!/bin/bash

# Warna
R='\033[0;31m'
G='\033[0;32m'
Y='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${Y}🔧 Memulai proses instalasi OPRScan...${NC}"

# Cek dan install PHP
if ! command -v php > /dev/null; then
  echo -e "${Y}[~] Menginstall PHP...${NC}"
  pkg install php -y || apt install php -y
else
  echo -e "${G}[✓] PHP sudah terpasang${NC}"
fi

# Cek dan install curl
if ! command -v curl > /dev/null; then
  echo -e "${Y}[~] Menginstall curl...${NC}"
  pkg install curl -y || apt install curl -y
else
  echo -e "${G}[✓] curl sudah terpasang${NC}"
fi

# Cek dan install wget
if ! command -v wget > /dev/null; then
  echo -e "${Y}[~] Menginstall wget...${NC}"
  pkg install wget -y || apt install wget -y
else
  echo -e "${G}[✓] wget sudah terpasang${NC}"
fi

# Cek dan install openssl
if ! command -v openssl > /dev/null; then
  echo -e "${Y}[~] Menginstall openssl...${NC}"
  pkg install openssl -y || apt install openssl -y
else
  echo -e "${G}[✓] openssl sudah terpasang${NC}"
fi

# Buat folder results jika belum ada
if [ ! -d "results" ]; then
  echo -e "${Y}[~] Membuat folder 'results/'...${NC}"
  mkdir results
else
  echo -e "${G}[✓] Folder 'results/' sudah ada${NC}"
fi

# Buat file target massal jika belum ada
if [ ! -f "targets.txt" ]; then
  echo -e "${Y}[~] Membuat file 'targets.txt' (kosong)...${NC}"
  touch targets.txt
fi

# Jadikan OPRScan bisa dieksekusi
chmod +x OPRScan

echo -e "${G}✅ Instalasi selesai. Jalankan dengan:${NC} ${Y}php OPRScan${NC}"
