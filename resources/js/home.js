import { matchesCatalogFilters } from './catalog-filter.js';

const carousel = document.querySelector('[data-carousel]');

if (carousel) {
    const slides = [...carousel.querySelectorAll('[data-slide]')];
    const indicators = [...carousel.querySelectorAll('[data-indicator]')];
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let activeSlide = 0;
    let rotationTimer;

    const showSlide = (index) => {
        activeSlide = (index + slides.length) % slides.length;

        slides.forEach((slide, slideIndex) => {
            const isActive = slideIndex === activeSlide;
            slide.classList.toggle('is-active', isActive);
            slide.setAttribute('aria-hidden', String(!isActive));
            slide.inert = !isActive;
        });

        indicators.forEach((indicator, indicatorIndex) => {
            indicator.setAttribute('aria-current', String(indicatorIndex === activeSlide));
        });
    };

    const stopRotation = () => window.clearInterval(rotationTimer);
    const startRotation = () => {
        stopRotation();

        if (!reducedMotion.matches && !carousel.matches(':hover') && !carousel.contains(document.activeElement)) {
            rotationTimer = window.setInterval(() => showSlide(activeSlide + 1), 6000);
        }
    };

    carousel.querySelector('[data-previous]').addEventListener('click', () => {
        showSlide(activeSlide - 1);
        startRotation();
    });
    carousel.querySelector('[data-next]').addEventListener('click', () => {
        showSlide(activeSlide + 1);
        startRotation();
    });
    indicators.forEach((indicator) => {
        indicator.addEventListener('click', () => {
            showSlide(Number(indicator.dataset.indicator));
            startRotation();
        });
    });
    carousel.addEventListener('mouseenter', stopRotation);
    carousel.addEventListener('mouseleave', startRotation);
    carousel.addEventListener('focusin', stopRotation);
    carousel.addEventListener('focusout', (event) => {
        if (!carousel.contains(event.relatedTarget)) {
            startRotation();
        }
    });
    reducedMotion.addEventListener('change', startRotation);
    startRotation();
}

const productGrids = [...document.querySelectorAll('[data-product-grid]')];
const categoryFilters = [...document.querySelectorAll('[data-category-filter]')];
const headerSearch = document.querySelector('#site-search');
const resultCount = document.querySelector('.result-count');
const allProductsEmptyState = document.querySelector('[data-empty-state]');
const flashProductsEmptyState = document.querySelector('[data-flash-empty]');

if (productGrids.length > 0 && categoryFilters.length > 0 && headerSearch) {
    let selectedCategoryId = 'all';

    const updateProducts = () => {
        const searchTerm = headerSearch.value.trim().toLocaleLowerCase();
        const visibleCounts = new Map();

        productGrids.forEach((grid) => {
            let visibleCount = 0;

            grid.querySelectorAll('[data-product]').forEach((card) => {
                const isVisible = matchesCatalogFilters({
                    categoryId: card.dataset.categoryId,
                    searchText: card.dataset.searchValue,
                }, selectedCategoryId, searchTerm);

                card.hidden = !isVisible;
                visibleCount += Number(isVisible);
            });

            visibleCounts.set(grid.dataset.productGrid, visibleCount);
        });

        const allVisibleCount = visibleCounts.get('all') ?? 0;
        const flashVisibleCount = visibleCounts.get('flash') ?? 0;

        resultCount.textContent = `${allVisibleCount} demo ${allVisibleCount === 1 ? 'product' : 'products'}`;
        allProductsEmptyState.hidden = allVisibleCount > 0;
        flashProductsEmptyState.hidden = flashVisibleCount > 0;
    };

    categoryFilters.forEach((filter) => {
        filter.addEventListener('click', () => {
            selectedCategoryId = filter.dataset.categoryFilter;
            categoryFilters.forEach((item) => {
                const isSelected = item === filter;
                item.classList.toggle('is-selected', isSelected);
                item.setAttribute('aria-pressed', String(isSelected));
            });
            updateProducts();
        });
    });

    headerSearch.addEventListener('input', updateProducts);
    headerSearch.closest('form').addEventListener('submit', (event) => {
        event.preventDefault();
        document.querySelector('#products').scrollIntoView({ behavior: 'smooth' });
    });
    updateProducts();
}

/* Mobile navigation is temporarily disabled; retain its code here for easy re-enabling.
const mobileToggle = document.querySelector('.menu-toggle');
const mobileNav = document.querySelector('#mobile-nav');

if (mobileToggle && mobileNav) {
    let closeNavigationTimer;

    const closeMobileNavigation = () => {
        window.clearTimeout(closeNavigationTimer);
        mobileToggle.setAttribute('aria-expanded', 'false');
        mobileToggle.setAttribute('aria-label', 'Open navigation menu');
        mobileNav.setAttribute('aria-hidden', 'true');
        mobileNav.inert = true;
        mobileNav.removeAttribute('data-open');

        closeNavigationTimer = window.setTimeout(() => {
            if (mobileToggle.getAttribute('aria-expanded') === 'false') {
                mobileNav.hidden = true;
            }
        }, window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 280);
    };

    mobileToggle.addEventListener('click', () => {
        const isExpanded = mobileToggle.getAttribute('aria-expanded') === 'true';

        if (isExpanded) {
            closeMobileNavigation();

            return;
        }

        window.clearTimeout(closeNavigationTimer);
        mobileNav.hidden = false;
        mobileNav.inert = false;
        mobileNav.setAttribute('aria-hidden', 'false');
        mobileNav.removeAttribute('data-open');
        mobileNav.getBoundingClientRect();
        mobileNav.setAttribute('data-open', 'true');
        mobileToggle.setAttribute('aria-expanded', 'true');
        mobileToggle.setAttribute('aria-label', 'Close navigation menu');
    });

    mobileToggle.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && mobileToggle.getAttribute('aria-expanded') === 'true') {
            closeMobileNavigation();
            mobileToggle.focus();
        }
    });

    mobileNav.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMobileNavigation();
            mobileToggle.focus();
        }
    });

    mobileNav.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            closeMobileNavigation();
        });
    });

    document.addEventListener('click', (event) => {
        const isExpanded = mobileToggle.getAttribute('aria-expanded') === 'true';
        const clickedNavigation = mobileNav.contains(event.target);
        const clickedToggle = mobileToggle.contains(event.target);

        if (isExpanded && !clickedNavigation && !clickedToggle) {
            closeMobileNavigation();
        }
    });
}

*/

const galleryMain = document.querySelector('[data-gallery-main]');

if (galleryMain) {
    const galleryButtons = [...document.querySelectorAll('[data-gallery-view]')];

    galleryButtons.forEach((button) => {
        button.addEventListener('click', () => {
            galleryMain.dataset.view = button.dataset.galleryView;

            galleryButtons.forEach((galleryButton) => {
                const isSelected = galleryButton === button;
                galleryButton.classList.toggle('is-selected', isSelected);
                galleryButton.setAttribute('aria-pressed', String(isSelected));
            });
        });
    });
}

const quantityControl = document.querySelector('[data-quantity-control]');

if (quantityControl) {
    const quantityInput = quantityControl.querySelector('input');
    const minimum = Number(quantityInput.min) || 1;
    const maximum = Number(quantityInput.max) || Number.MAX_SAFE_INTEGER;

    const updateQuantity = (step) => {
        const current = Number.parseInt(quantityInput.value, 10);
        const safeCurrent = Number.isFinite(current) ? current : minimum;
        quantityInput.value = String(Math.min(maximum, Math.max(minimum, safeCurrent + step)));
    };

    quantityControl.querySelector('[data-quantity-decrement]').addEventListener('click', () => updateQuantity(-1));
    quantityControl.querySelector('[data-quantity-increment]').addEventListener('click', () => updateQuantity(1));
    quantityInput.addEventListener('change', () => {
        const current = Number.parseInt(quantityInput.value, 10);
        quantityInput.value = String(Math.min(maximum, Math.max(minimum, Number.isFinite(current) ? current : minimum)));
    });
}

document.querySelectorAll('[data-cart-quantity-form]').forEach((form) => {
    const quantityInput = form.querySelector('[data-cart-quantity]');
    const decrementButton = form.querySelector('[data-cart-decrement]');
    const incrementButton = form.querySelector('[data-cart-increment]');
    const minimum = Number(quantityInput.min) || 1;
    const maximum = Number(quantityInput.max) || Number.MAX_SAFE_INTEGER;

    const updateControls = () => {
        const quantity = Number.parseInt(quantityInput.value, 10);
        const safeQuantity = Number.isFinite(quantity) ? quantity : minimum;
        decrementButton.disabled = safeQuantity <= minimum;
        incrementButton.disabled = safeQuantity >= maximum;
    };

    const changeQuantity = (change) => {
        const current = Number.parseInt(quantityInput.value, 10);
        const safeCurrent = Number.isFinite(current) ? current : minimum;
        const next = Math.min(maximum, Math.max(minimum, safeCurrent + change));

        quantityInput.value = String(next);
        updateControls();

        if (next !== safeCurrent) {
            form.requestSubmit();
        }
    };

    decrementButton.addEventListener('click', () => changeQuantity(-1));
    incrementButton.addEventListener('click', () => changeQuantity(1));
    quantityInput.addEventListener('input', updateControls);
    quantityInput.addEventListener('change', () => {
        const quantity = Number.parseInt(quantityInput.value, 10);
        quantityInput.value = String(Math.min(maximum, Math.max(minimum, Number.isFinite(quantity) ? quantity : minimum)));
        updateControls();
    });
});
