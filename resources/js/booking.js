function formatInr(n) {
    return '₹' + Number(n).toLocaleString('en-IN', { maximumFractionDigits: 0 });
}

window.refreshBookingPrice = async function () {
    const panel = document.getElementById('bookingPanel');
    if (!panel) return;

    const roomId = document.getElementById('room_id')?.value;
    const checkIn = document.getElementById('check_in')?.value;
    const checkOut = document.getElementById('check_out')?.value;
    const guestPackage = document.getElementById('guest_package')?.value;
    const childCount = document.getElementById('child_count')?.value || 0;
    const promoCode = document.getElementById('promo_code')?.value || '';
    const preview = document.getElementById('pricePreview');

    if (!roomId || !checkIn || !checkOut || !preview) return;

    preview.style.display = '';
    preview.innerHTML = '<p class="text-muted mb-0 small">Calculating...</p>';

    try {
        const res = await fetch(panel.dataset.calculateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({
                room_id: roomId,
                check_in: checkIn,
                check_out: checkOut,
                guest_package: guestPackage,
                child_count: childCount,
                promo_code: promoCode,
            }),
        });

        const json = await res.json();

        if (!json.success) {
            preview.innerHTML = `<p class="text-danger small mb-0">${json.message || 'Unable to calculate price'}</p>`;
            return;
        }

        const d = json.data;
        const fees = (d.cleaning_fee || 0) + (d.service_fee || 0);
        const availHtml = d.available
            ? `<span class="text-success">✓ Available (${d.units_available} unit(s))</span>`
            : `<span class="text-danger">✗ Not available for these dates</span>`;

        preview.innerHTML = `
            <div class="d-flex justify-content-between"><span>Nights</span><span>${d.nights}</span></div>
            <div class="d-flex justify-content-between"><span>Base</span><span>${formatInr(d.base_price)}</span></div>
            <div class="d-flex justify-content-between"><span>Fees</span><span>${formatInr(fees)}</span></div>
            ${d.promo_discount ? `<div class="d-flex justify-content-between text-success"><span>Promo</span><span>-${formatInr(d.promo_discount)}</span></div>` : ''}
            <hr class="my-2">
            <div class="d-flex justify-content-between fw-bold"><span>Total</span><span>${formatInr(d.total_price)}</span></div>
            <div class="mt-2 small">${availHtml}</div>
        `;

        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) submitBtn.disabled = !d.available;
    } catch (e) {
        preview.innerHTML = '<p class="text-danger small mb-0">Failed to load price</p>';
    }
};

document.addEventListener('DOMContentLoaded', () => {
    ['check_in', 'check_out', 'guest_package', 'child_count', 'promo_code'].forEach(id => {
        document.getElementById(id)?.addEventListener('change', window.refreshBookingPrice);
    });
    document.getElementById('room_id')?.addEventListener('change', window.refreshBookingPrice);
    if (document.getElementById('bookingPanel')) {
        window.refreshBookingPrice();
    }
});
