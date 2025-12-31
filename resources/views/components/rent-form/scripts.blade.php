@props(['bookedDates'])

<script>
    const disabledDates = @json($bookedDates ?? []);

    const showToast = (message) => {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className =
            'bg-red-600 text-white p-6 rounded-3xl shadow-2xl mb-4 animate-fadein ' +
            'border-b-4 border-red-800 font-black text-sm tracking-tight flex items-center space-x-3 pointer-events-auto';

        toast.innerHTML = `
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>${message}</span>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('animate-fadeout');
            setTimeout(() => toast.remove(), 600);
        }, 4000);
    };

    const getAlpineData = () => {
        const el = document.querySelector('[x-data]');
        return el && el.__x ? el.__x.$data : null;
    };

    document.addEventListener('DOMContentLoaded', () => {

        if (!window.$ || !$.datepicker) return;

        $('#tanggal_sewa').datepicker({
            dateFormat: 'dd-mm-yy',
            minDate: 0,
            beforeShowDay(date) {
                const d = $.datepicker.formatDate('dd-mm-yy', date);
                return [!disabledDates.includes(d), disabledDates.includes(d) ? 'ui-state-booked' : ''];
            },
            onSelect(selectedDate) {
                const data = getAlpineData();
                if (!data) return;

                data.tanggalSewa = selectedDate;

                const [dd, mm, yyyy] = selectedDate.split('-');
                const minReturn = new Date(yyyy, mm - 1, dd);
                minReturn.setDate(minReturn.getDate() + 1);

                $('#tanggal_kembali')
                    .datepicker('option', 'minDate', minReturn)
                    .val('');

                data.tanggalKembali = '';
                $(this).datepicker('hide');

                data.checkDriver?.();
                updateDeadlineUI();
            }
        });

        $('#tanggal_kembali').datepicker({
            dateFormat: 'dd-mm-yy',
            beforeShowDay(date) {
                const d = $.datepicker.formatDate('dd-mm-yy', date);
                return [!disabledDates.includes(d), disabledDates.includes(d) ? 'ui-state-booked' : ''];
            },
            onSelect(selectedDate) {
                const data = getAlpineData();
                if (!data) return;

                const sewaStr = $('#tanggal_sewa').val();
                if (!sewaStr) return;

                const [sd, sm, sy] = sewaStr.split('-');
                const [kd, km, ky] = selectedDate.split('-');

                const dSewa = new Date(sy, sm - 1, sd);
                const dKembali = new Date(ky, km - 1, kd);

                if (dKembali <= dSewa) {
                    showToast('Tanggal kembali minimal H+1 dari tanggal sewa.');
                    $(this).val('');
                    data.tanggalKembali = '';
                    return;
                }

                let overlap = false;
                for (let d = new Date(dSewa); d <= dKembali; d.setDate(d.getDate() + 1)) {
                    const f = $.datepicker.formatDate('dd-mm-yy', d);
                    if (disabledDates.includes(f)) { overlap = true; break; }
                }

                if (overlap) {
                    showToast('Rentang tanggal menabrak jadwal booking lain.');
                    $(this).val('');
                    data.tanggalKembali = '';
                    return;
                }

                data.tanggalKembali = selectedDate;
                $(this).datepicker('hide');
                data.checkDriver?.();
                updateDeadlineUI();
            }
        });

        const updateDeadlineUI = () => {
            const d = getAlpineData();
            const el = document.getElementById('deadline-info');
            if (!el || !d) return;

            if (d.tanggalKembali && d.jamSewa) {
                el.innerHTML = `
                    🚀 Pengembalian:
                    <span class="font-black underline mx-2">
                        ${d.tanggalKembali} pukul ${d.jamSewa} WIB
                    </span>
                `;
            } else {
                el.textContent = 'Pilih tanggal untuk melihat estimasi.';
            }
        };

        document.getElementById('jam_sewa')?.addEventListener('change', updateDeadlineUI);

        const payBtn = document.getElementById('pay-button');
        const form = document.getElementById('peminjaman-form');

        if (payBtn && form) {
            payBtn.addEventListener('click', async () => {
                if (!form.checkValidity()) {
                    showToast('Mohon lengkapi seluruh field!');
                    form.reportValidity();
                    return;
                }

                payBtn.disabled = true;
                payBtn.textContent = 'MENGHUBUNGKAN...';

                try {
                    const res = await axios.post(form.action, new FormData(form));
                    if (!res.data?.snap_token) {
                        throw new Error(res.data?.error || 'Token pembayaran tidak tersedia.');
                    }

                    snap.pay(res.data.snap_token, {
                        onSuccess: () => {
                            window.location.href = "{{ route('payment.success') }}";
                        },
                        onClose: async () => {
                            payBtn.disabled = false;
                            payBtn.textContent = 'KONFIRMASI & BAYAR';
                            if (res.data.peminjaman_id) {
                                await axios.delete(`/peminjaman/${res.data.peminjaman_id}/cancel`, {
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                });
                            }
                        }
                    });

                } catch (e) {
                    payBtn.disabled = false;
                    payBtn.textContent = 'KONFIRMASI & BAYAR';
                    showToast(e.message || 'Gagal memproses transaksi.');
                }
            });
        }
    });
</script>

<style>
    [x-cloak] { display: none !important; }

    .ui-datepicker {
        border-radius: 2rem !important;
        padding: 1.5rem !important;
        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.3) !important;
        border: none !important;
    }

    .ui-state-booked a {
        background: #ef4444 !important;
        color: #fff !important;
        opacity: .4;
        pointer-events: none !important;
    }

    @keyframes fadein {
        from { opacity: 0; transform: translateX(60px); }
        to   { opacity: 1; transform: translateX(0); }
    }

    @keyframes fadeout {
        from { opacity: 1; transform: translateX(0); }
        to   { opacity: 0; transform: translateX(60px); }
    }

    .animate-fadein  { animation: fadein .5s forwards; }
    .animate-fadeout { animation: fadeout .5s forwards; }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none; margin: 0;
    }
</style>
