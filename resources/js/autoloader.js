const userId = document.body.dataset.userId;
const page = document.body.dataset.page;

if (!page) {
    console.warn('data-page tidak ditemukan di <body>.');
    throw new Error('Tidak bisa menentukan halaman.');
}
if (!userId) {
    console.warn('user tidak ditemukan.');
    throw new Error('modul notifikasi tidak akan jalan.');
}

const pageKey = page.replace(/\./g, '/');

import('./notifications').then(({ default: initNotificationListener }) => {
    initNotificationListener(userId);
});

import(`./pages/${pageKey}.js`)
    .then((module) => {
        if (typeof module.default === 'function') {
            module.default();
        }
    })
    .catch(() => {
        console.warn(`Module JS untuk halaman '${pageKey}' tidak ditemukan.`);
    });
