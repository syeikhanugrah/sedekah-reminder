#!/usr/bin/env bash

for i in provisioning/files/*.pem
do
    if [ -f "$i" ]; then
        echo "Sertifikat sudah terpasang, melanjutkan"
    else
        echo "Memasang dependensi yang dibutuhkan mkcert"
        sudo apt-get update
        sudo apt-get install libnss3-tools

        echo "Memasang Certificate Authority dan membuat sertifikat"
        ./bin/mkcert -install > /dev/null 2>&1
        ./bin/mkcert -cert-file provisioning/files/sedekah-reminder.dev.pem -key-file provisioning/files/sedekah-reminder.dev-key.pem sedekah-reminder.dev > /dev/null 2>&1
    fi
done
