<x-mail::message>
    # Pengajuan Baru

    Halo **{{ $user->name }}**,

    Ada pengajuan baru dari unit **{{ $ajuan->unit->nama_unit }}**, diajukan oleh **{{ $ajuan->users->name }}**.

    <x-mail::button :url="url('/pengajuan/' . $ajuan->id)">
        Lihat Pengajuan
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
