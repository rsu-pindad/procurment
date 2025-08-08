<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Ajuan;

new class extends Component
{
    #[On('confirmed-hapus')]
    public function delete($id)
    {
        try {
            $ajuan = Ajuan::find($id);
            $ajuan->delete();
            $pesan = '';
            if ($ajuan) {
                $pesan = 'Ajuan berhasil dihapus';
                $this->dispatch('info-hapus', message: $pesan);
            }
        } catch (\Throwable $th) {
            $this->dispatch('info-hapus', message: $th->getMessage());
        }
    }
}; ?>

<div>
    <script type="module">
        const notyf = new Notyf({
            duration: 10000,
            position: {
                x: 'center',
                y: 'center',
            },
            ripple: true,
            dismissible: true,
            types: [{
                type: 'info',
                background: 'orange',
                icon: false
            }]
        });

        document.addEventListener('livewire:init', () => {
            Livewire.on('info-hapus', (event) => {
                notyf.open({
                    type: 'info',
                    message: event.message
                });
                Livewire.dispatch('pg:eventRefresh-user-ajuan-table-z2bm8x-table');
            });
        });
    </script>
</div>
