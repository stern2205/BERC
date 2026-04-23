function showLogoutModal() {
    document.getElementById('logout-modal').classList.remove('hidden');
    // Prevent scrolling on the body while modal is open
    document.body.style.overflow = 'hidden';
}

function hideLogoutModal() {
    document.getElementById('logout-modal').classList.add('hidden');
    // Restore scrolling
    document.body.style.overflow = 'auto';
}

function confirmLogout() {
    // Submit the hidden form we created in step 1
    document.getElementById('logout-form').submit();
}

// Close modal if user clicks outside of the content
window.onclick = function(event) {
    const modal = document.getElementById('logout-modal');
    if (event.target == modal) {
        hideLogoutModal();
    }
}

document.addEventListener('DOMContentLoaded', function () {

    /* ───────── CLOCK ───────── */
    function updateClock() {
        const now = new Date();
        const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

        let h = now.getHours();
        let m = now.getMinutes();
        const ampm = h >= 12 ? 'PM' : 'AM';

        h = h % 12 || 12;

        const timeStr =
            (h < 10 ? '0' : '') + h + ':' +
            (m < 10 ? '0' : '') + m +
            ' ' + ampm + ' | ' +
            days[now.getDay()].toUpperCase();

        const dd = String(now.getDate()).padStart(2, '0');
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const dateStr = dd + '/' + mm + '/' + now.getFullYear();

        const clock = document.getElementById('clock');
        const datestamp = document.getElementById('datestamp');
        const clockM = document.getElementById('clock-m');
        const dateM = document.getElementById('date-m');

        if (clock) clock.textContent = timeStr;
        if (datestamp) datestamp.textContent = dateStr;
        if (clockM) clockM.textContent = timeStr;
        if (dateM) dateM.textContent = dateStr;
    }

    updateClock();
    setInterval(updateClock, 1000);

    /* ───────── HAMBURGER MENU ───────── */
    const btn = document.getElementById('hamburger-btn');
    const menu = document.getElementById('mobile-menu');
    const hb1 = document.getElementById('hb1');
    const hb2 = document.getElementById('hb2');
    const hb3 = document.getElementById('hb3');

    let open = false;

    if (btn && menu) {
        btn.addEventListener('click', () => {
            open = !open;

            if (open) {
                menu.classList.remove('hidden');
                requestAnimationFrame(() => menu.classList.add('open'));

                if (hb1) hb1.style.transform = 'translateY(6px) rotate(45deg)';
                if (hb2) hb2.style.opacity = '0';
                if (hb3) hb3.style.transform = 'translateY(-6px) rotate(-45deg)';
            } else {
                menu.classList.remove('open');
                menu.addEventListener('transitionend', () => {
                    menu.classList.add('hidden');
                }, { once: true });

                if (hb1) hb1.style.transform = '';
                if (hb2) hb2.style.opacity = '';
                if (hb3) hb3.style.transform = '';
            }
        });
    }

    /* ───────── CHAIR MANAGEMENT DESKTOP MENU ───────── */
    const desktopChairBtn = document.getElementById('desktop-chair-btn');
    const desktopChairClose = document.getElementById('desktop-chair-close');
    const desktopChairMenu = document.getElementById('desktop-chair-menu');
    const desktopDashLink = document.getElementById('desktop-dash-link');
    const desktopCalLink = document.getElementById('desktop-cal-link');

    if (desktopChairBtn && desktopChairMenu) {
        desktopChairBtn.addEventListener('click', () => {
            desktopChairMenu.classList.remove('hidden');
            desktopChairMenu.classList.add('flex');
            desktopChairBtn.classList.add('hidden');

            // Hide standard links to make room
            if (desktopDashLink) desktopDashLink.classList.add('hidden');
            if (desktopCalLink) desktopCalLink.classList.add('hidden');
        });
    }

    if (desktopChairClose && desktopChairMenu) {
        desktopChairClose.addEventListener('click', () => {
            desktopChairMenu.classList.add('hidden');
            desktopChairMenu.classList.remove('flex');
            desktopChairBtn.classList.remove('hidden');

            // Bring standard links back
            if (desktopDashLink) desktopDashLink.classList.remove('hidden');
            if (desktopCalLink) desktopCalLink.classList.remove('hidden');
        });
    }

    /* ───────── CHAIR MANAGEMENT MOBILE MENU ───────── */
    const mobileChairBtn = document.getElementById('mobile-chair-btn');
    const mobileChairMenu = document.getElementById('mobile-chair-menu');
    const mobileChairIcon = document.getElementById('mobile-chair-icon');

    if (mobileChairBtn && mobileChairMenu) {
        mobileChairBtn.addEventListener('click', () => {
            mobileChairMenu.classList.toggle('hidden');
            if (mobileChairIcon) {
                mobileChairIcon.classList.toggle('rotate-180');
            }
        });
    }

});
