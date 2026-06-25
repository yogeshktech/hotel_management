function formatInr(n) {
    return '₹' + Number(n).toLocaleString('en-IN', { maximumFractionDigits: 0 });
}

function getSelectedAddonIds() {
    if (document.getElementById('full_package_addons')?.checked) {
        return [];
    }
    return Array.from(document.querySelectorAll('.addon-checkbox:checked')).map((el) => parseInt(el.value, 10));
}

function isFullPackageAddons() {
    return document.getElementById('full_package_addons')?.checked || false;
}

function renderAddonOptions(addons) {
    const wrap = document.getElementById('addonOptionsWrap');
    if (!wrap) return;

    const wasFullPackage = document.getElementById('full_package_addons')?.checked ?? false;
    const checkedIds = Array.from(document.querySelectorAll('.addon-checkbox:checked')).map((el) => el.value);

    if (!addons || !addons.length) {
        wrap.innerHTML = '<p class="small text-muted mb-0">No optional facilities for this room.</p>';
        return;
    }

    const packageAddons = addons.filter((a) => a.is_included_in_package);
    const fullPackage = wasFullPackage;

    let html = '';

    if (packageAddons.length) {
        html += `
            <div class="form-check mb-2 border rounded p-2 bg-white">
                <input class="form-check-input" type="checkbox" id="full_package_addons" ${fullPackage ? 'checked' : ''}>
                <label class="form-check-label small fw-semibold" for="full_package_addons">
                    All facilities package
                    <span class="text-muted fw-normal d-block">Includes: ${packageAddons.map((a) => a.name).join(', ')}</span>
                </label>
            </div>`;
    }

    html += '<div id="individualAddons" class="' + (fullPackage ? 'opacity-50' : '') + '">';
    html += '<p class="small fw-semibold mb-1">Or pick individually:</p>';

    addons.forEach((addon) => {
        const priceLabel = addon.is_free
            ? 'Free'
            : `₹${Number(addon.price).toLocaleString('en-IN')} · ${addon.charge_label}`;
        const checked = fullPackage ? false : (checkedIds.includes(String(addon.id)) || document.querySelector(`.addon-checkbox[value="${addon.id}"]`)?.checked);

        html += `
            <div class="form-check mb-1">
                <input class="form-check-input addon-checkbox" type="checkbox" name="addon_ids[]" value="${addon.id}"
                    id="addon_${addon.id}" ${fullPackage ? 'disabled' : ''} ${checked ? 'checked' : ''}>
                <label class="form-check-label small" for="addon_${addon.id}">
                    ${addon.name} <span class="text-muted">(${priceLabel})</span>
                </label>
            </div>`;
    });

    html += '</div>';
    wrap.innerHTML = html;

    document.getElementById('full_package_addons')?.addEventListener('change', function () {
        const disabled = this.checked;
        document.querySelectorAll('.addon-checkbox').forEach((cb) => {
            cb.disabled = disabled;
            if (disabled) cb.checked = false;
        });
        document.getElementById('individualAddons')?.classList.toggle('opacity-50', disabled);
        window.refreshBookingPrice?.();
    });

    document.querySelectorAll('.addon-checkbox').forEach((cb) => {
        cb.addEventListener('change', () => window.refreshBookingPrice?.());
    });
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
                addon_ids: getSelectedAddonIds(),
                full_package_addons: isFullPackageAddons(),
            }),
        });

        const json = await res.json();

        if (!json.success) {
            preview.innerHTML = `<p class="text-danger small mb-0">${json.message || 'Unable to calculate price'}</p>`;
            return;
        }

        const d = json.data;

        if (d.available_addons) {
            renderAddonOptions(d.available_addons);
        }

        const availHtml = d.available
            ? `<span class="text-success">✓ Available (${d.units_available} unit(s))</span>`
            : `<span class="text-danger">✗ Not available for these dates</span>`;

        const panelHint = document.getElementById('panelPriceHint');
        if (panelHint && d.total_price) {
            panelHint.innerHTML = `${formatInr(d.total_price)} <small>total (all charges)</small>`;
        }

        const lineHtml = (d.line_items || []).map((item) => {
            const cls = item.amount < 0 ? 'text-success' : '';
            const amt = item.amount < 0 ? `-${formatInr(Math.abs(item.amount))}` : formatInr(item.amount);
            return `<div class="d-flex justify-content-between ${cls}"><span>${item.label}</span><span>${amt}</span></div>`;
        }).join('');

        const seasonalNote = d.has_seasonal_pricing
            ? `<div class="text-warning small mt-1">Seasonal rate on ${d.seasonal_nights} of ${d.nights} night(s)</div>`
            : '';

        preview.innerHTML = `
            <p class="small fw-semibold mb-2">Price breakdown <span class="text-muted fw-normal">(no hidden charges)</span></p>
            <div class="d-flex justify-content-between text-muted small"><span>Nights</span><span>${d.nights}</span></div>
            ${lineHtml}
            ${seasonalNote}
            <hr class="my-2">
            <div class="d-flex justify-content-between fw-bold"><span>Total payable</span><span>${formatInr(d.total_price)}</span></div>
            <div class="mt-2 small">${availHtml}</div>
        `;

        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) submitBtn.disabled = !d.available;
    } catch (e) {
        preview.innerHTML = '<p class="text-danger small mb-0">Failed to load price</p>';
    }
};

document.addEventListener('DOMContentLoaded', () => {
    ['check_in', 'check_out', 'guest_package', 'child_count', 'promo_code'].forEach((id) => {
        document.getElementById(id)?.addEventListener('change', window.refreshBookingPrice);
    });
    document.getElementById('room_id')?.addEventListener('change', window.refreshBookingPrice);
    if (document.getElementById('bookingPanel')) {
        window.refreshBookingPrice();
    }
});
