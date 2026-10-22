import './bootstrap';
import * as bootstrap from 'bootstrap';

const parseNumber = (value) => Number(String(value).replace(/[^0-9]/g, '')) || 0;
const formatNumber = (value, maximumFractionDigits = 0) => new Intl.NumberFormat('id-ID', { maximumFractionDigits }).format(value);

function initPublicNavbar() {
    const header = document.querySelector('[data-site-header]');
    const collapseElement = document.getElementById('publicNavigation');
    const links = collapseElement ? [...collapseElement.querySelectorAll('a')] : [];

    const setScrolledState = () => header?.classList.toggle('is-scrolled', window.scrollY > 18);
    setScrolledState();
    window.addEventListener('scroll', setScrolledState, { passive: true });

    links.forEach((link) => {
        link.addEventListener('click', () => {
            if (collapseElement?.classList.contains('show')) {
                bootstrap.Collapse.getOrCreateInstance(collapseElement).hide();
            }
        });
    });
}

function initSolarCalculator() {
    const form = document.getElementById('solarCalculator');
    if (!form) return;

    const monthlyBill = document.getElementById('monthlyBill');
    const installedPower = document.getElementById('installedPower');
    const maximumCapacity = document.getElementById('maximumCapacity');
    const availableSpace = document.getElementById('availableSpace');
    const billSavings = document.getElementById('billSavings');
    const billWithSolar = document.getElementById('billWithSolar');

    const recalculate = () => {
        const bill = Math.max(0, parseNumber(monthlyBill.value));
        const power = Math.max(0, Number(installedPower.value) || 0);
        const capacity = power * 0.1;
        const space = capacity * 7.6;
        const savings = bill * 0.2;
        const remainingBill = Math.max(0, bill - savings);

        maximumCapacity.value = formatNumber(capacity, 1);
        availableSpace.value = `${formatNumber(space, 1)} m²`;
        billSavings.value = formatNumber(savings);
        billWithSolar.value = formatNumber(remainingBill);
    };

    monthlyBill.addEventListener('input', () => {
        const caretAtEnd = monthlyBill.selectionStart === monthlyBill.value.length;
        monthlyBill.value = formatNumber(parseNumber(monthlyBill.value));
        if (caretAtEnd) monthlyBill.setSelectionRange(monthlyBill.value.length, monthlyBill.value.length);
        recalculate();
    });

    installedPower.addEventListener('input', recalculate);
    form.addEventListener('submit', (event) => event.preventDefault());
    recalculate();
}

function initTestimonialSlider() {
    const track = document.getElementById('testimonialTrack');
    const dotsContainer = document.getElementById('testimonialDots');
    if (!track || !dotsContainer) return;

    const cards = [...track.querySelectorAll('.testimonial-card')];
    if (!cards.length) return;

    const getVisibleCards = () => {
        if (window.innerWidth < 576) return 1;
        if (window.innerWidth < 992) return 2;
        return 4;
    };

    let pageCount = 1;
    let dots = [];

    const buildDots = () => {
        const visibleCards = getVisibleCards();
        pageCount = Math.max(1, Math.ceil(cards.length / visibleCards));
        dotsContainer.innerHTML = '';
        dots = Array.from({ length: pageCount }, (_, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `testimonial-dot${index === 0 ? ' active' : ''}`;
            button.setAttribute('aria-label', `Show testimonial group ${index + 1}`);
            button.addEventListener('click', () => {
                const targetCard = cards[index * visibleCards] || cards[cards.length - 1];
                track.scrollTo({ left: targetCard.offsetLeft - track.offsetLeft, behavior: 'smooth' });
            });
            dotsContainer.appendChild(button);
            return button;
        });
    };

    const updateDot = () => {
        if (!dots.length) return;
        const maxScroll = Math.max(1, track.scrollWidth - track.clientWidth);
        const activeIndex = Math.min(pageCount - 1, Math.round((track.scrollLeft / maxScroll) * (pageCount - 1)));
        dots.forEach((dot, index) => dot.classList.toggle('active', index === activeIndex));
    };

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(() => {
            buildDots();
            updateDot();
        }, 120);
    });

    track.addEventListener('scroll', updateDot, { passive: true });
    buildDots();
}

document.addEventListener('DOMContentLoaded', () => {
    const proposalModal = document.getElementById('serviceProposalModal');
    if (proposalModal) {
        const viewer = proposalModal.querySelector('[data-proposal-viewer]');
        const empty = proposalModal.querySelector('[data-proposal-empty]');
        const openLink = proposalModal.querySelector('[data-proposal-open]');
        proposalModal.addEventListener('show.bs.modal', (event) => {
            const trigger = event.relatedTarget;
            const title = trigger?.dataset.serviceTitle || 'Service';
            const url = trigger?.dataset.proposalUrl || '';
            proposalModal.querySelector('.modal-title').textContent = `${title} — Proposal`;
            viewer.replaceChildren();
            empty.hidden = Boolean(url);
            openLink.hidden = !url;
            openLink.href = url || '#';
            if (url) {
                const frame = document.createElement('iframe');
                frame.title = `Proposal for ${title}`;
                frame.src = url;
                frame.className = 'service-proposal-frame';
                viewer.appendChild(frame);
            }
        });
        proposalModal.addEventListener('hidden.bs.modal', () => {
            viewer.replaceChildren();
            openLink.removeAttribute('href');
            openLink.hidden = true;
        });
    }
    const itemType = document.querySelector('[data-item-type]');
    const proposalField = document.querySelector('[data-service-proposal-field]');
    if (itemType && proposalField) {
        const updateProposalField = () => {
            const isService = itemType.value === 'service';
            proposalField.hidden = !isService;
            proposalField.querySelectorAll('input').forEach((input) => { input.disabled = !isService; });
        };
        itemType.addEventListener('change', updateProposalField);
        updateProposalField();
    }
    const categoryField = document.querySelector('[data-product-category-field]');
    if (itemType && categoryField) {
        const updateCategoryField = () => {
            const isProduct = itemType.value === 'product';
            categoryField.hidden = !isProduct;
            categoryField.querySelector('select').required = isProduct;
        };
        itemType.addEventListener('change', updateCategoryField);
        updateCategoryField();
    }
    initPublicNavbar();
    initSolarCalculator();
    initTestimonialSlider();
});
