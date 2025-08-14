// Contoh: cek halaman lewat data attribute di body
const page = document.body.dataset.page;
const userId = document.body.dataset.userId;

if (page === 'dashboard') {
    // Lazy load modul dashboard.js hanya saat di halaman dashboard
    import('./dashboard/dashboard-livewire-event').then(({ default: initDashboard }) => {
        initDashboard();
    });
}

if ( page === 'ajuan') {
    import('./ajuan/ajuan-livewire-event').then(({default:initLivewireEventHandlers}) =>{
        console.log('modul ajuan loaded');
    });
}

// Modul notifications selalu di-load (bisa juga dibuat lazy)
import('./notifications').then(({ default: initNotificationListener }) => {
    // Panggil init notifications dengan userId nanti dari blade
    // window.initNotifications akan didefinisikan di notifications.js
    // atau kamu bisa panggil langsung initNotifications() di sini
    // window.initNotifications = initNotifications;
    initNotificationListener(userId);
});
