// ==========================================
// Search & Filter Functions
// ==========================================

document.getElementById('searchDesignInput')?.addEventListener('input', filterCards);
document.getElementById('searchUserInput')?.addEventListener('input', filterCards);
document.getElementById('sizeFilter')?.addEventListener('change', filterCards);
document.getElementById('priceFilter')?.addEventListener('change', filterCards);
document.getElementById('colorFilter')?.addEventListener('change', filterCards);
document.getElementById('sleeveFilter')?.addEventListener('change', filterCards);
document.getElementById('domeFilter')?.addEventListener('change', filterCards);
document.getElementById('fabricFilter')?.addEventListener('change', filterCards);

function filterCards() {
    const designSearchTerm = document.getElementById('searchDesignInput').value.toLowerCase();
    const userSearchTerm = document.getElementById('searchUserInput').value.toLowerCase();
    const sizeFilter = document.getElementById('sizeFilter').value;
    const priceFilter = document.getElementById('priceFilter').value;
    const colorFilter = document.getElementById('colorFilter')?.value || '';
    const sleeveFilter = document.getElementById('sleeveFilter')?.value || '';
    const domeFilter = document.getElementById('domeFilter')?.value || '';
    const fabricFilter = document.getElementById('fabricFilter')?.value || '';

    const cards = document.querySelectorAll('.design-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const designName = card.getAttribute('data-design-name') || '';
        const userName = card.getAttribute('data-user-name') || '';
        const cardSize = card.getAttribute('data-size');
        const cardPrice = parseFloat(card.getAttribute('data-price'));
        const cardColor = card.getAttribute('data-color');
        const cardSleeve = card.getAttribute('data-sleeve');
        const cardDome = card.getAttribute('data-dome');
        const cardFabric = card.getAttribute('data-fabric');

        const matchesDesignSearch = designName.includes(designSearchTerm);
        const matchesUserSearch = userName.includes(userSearchTerm);
        const matchesSize = !sizeFilter || cardSize === sizeFilter;

        let matchesPrice = true;
        if (priceFilter) {
            if (priceFilter === '0-50') {
                matchesPrice = cardPrice < 50;
            } else if (priceFilter === '50-100') {
                matchesPrice = cardPrice >= 50 && cardPrice < 100;
            } else if (priceFilter === '100-200') {
                matchesPrice = cardPrice >= 100 && cardPrice < 200;
            } else if (priceFilter === '200-500') {
                matchesPrice = cardPrice >= 200 && cardPrice < 500;
            } else if (priceFilter === '500+') {
                matchesPrice = cardPrice >= 500;
            }
        }

        const matchesColor = !colorFilter || cardColor.includes(colorFilter);
        const matchesSleeve = !sleeveFilter || cardSleeve.includes(sleeveFilter);
        const matchesDome = !domeFilter || cardDome.includes(domeFilter);
        const matchesFabric = !fabricFilter || cardFabric.includes(fabricFilter);

        if (matchesDesignSearch && matchesUserSearch && matchesSize && matchesPrice &&
            matchesColor && matchesSleeve && matchesDome && matchesFabric) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    updateResultsCount(visibleCount);
}

function updateResultsCount(count) {
    const statBadge = document.querySelector('.stat-badge');
    if (statBadge) {
        statBadge.textContent = `النتائج: ${count}`;
    }
}

function toggleAdvancedFilters() {
    const content = document.getElementById('advancedFiltersContent');
    const btn = document.querySelector('.toggle-filters-btn');
    const icon = document.getElementById('toggleIcon');

    if (content.style.display === 'none') {
        content.style.display = 'block';
        btn?.classList.add('active');
        icon.textContent = '▲';
    } else {
        content.style.display = 'none';
        btn?.classList.remove('active');
        icon.textContent = '▼';
    }
}

function resetFilters() {
    document.getElementById('searchDesignInput').value = '';
    document.getElementById('searchUserInput').value = '';
    document.getElementById('sizeFilter').value = '';
    document.getElementById('priceFilter').value = '';

    if (document.getElementById('colorFilter')) {
        document.getElementById('colorFilter').value = '';
    }
    if (document.getElementById('sleeveFilter')) {
        document.getElementById('sleeveFilter').value = '';
    }
    if (document.getElementById('domeFilter')) {
        document.getElementById('domeFilter').value = '';
    }
    if (document.getElementById('fabricFilter')) {
        document.getElementById('fabricFilter').value = '';
    }

    filterCards();
}

// ==========================================
// Design Details Modal - عرض التفاصيل
// ==========================================

async function showDesignDetails(designId) {
    closeDesignModal();

    try {
        const response = await fetch(`/admin/designs/${designId}/details`);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const design = await response.json();

        // طباعة للتأكد من البيانات
        console.log('Design Data:', design);
        console.log('Images:', design.images);
        console.log('Images Length:', design.images?.length);

        const modalHTML = generateDesignModalHTML(design);
        document.body.insertAdjacentHTML('beforeend', modalHTML);

    } catch (error) {
        console.error('Error loading design:', error);
        alert('❌ حدث خطأ أثناء تحميل تفاصيل التصميم.\n' + error.message);
    }
}

function generateDesignModalHTML(design) {
    console.log('Generating modal for design:', design);

    // معالجة الصور مع Slider
    let imageHTML = '';

    // تحقق من وجود الصور بطرق مختلفة
    const hasImages = design.images && Array.isArray(design.images) && design.images.length > 0;
    const hasSingleImage = design.image && design.image !== null;

    console.log('Has Images Array:', hasImages);
    console.log('Has Single Image:', hasSingleImage);

    if (hasImages) {
        console.log('Using images array, count:', design.images.length);
        // إذا في أكثر من صورة، استخدم الـ Slider
        imageHTML = `
            <div class="slider-container modal-slider" data-design-id="${design.id}">
                ${design.images.map((img, index) => {
                    const imgPath = img.image_path || img;
                    console.log(`Image ${index}:`, imgPath);
                    return `
                        <img src="/storage/${imgPath}"
                             class="slider-image ${index === 0 ? 'active' : ''}"
                             alt="${design.name}"
                             onerror="console.error('Failed to load image:', this.src); this.style.display='none';">
                    `;
                }).join('')}

                ${design.images.length > 1 ? `
                    <button class="slider-prev" onclick="prevImage(this)">‹</button>
                    <button class="slider-next" onclick="nextImage(this)">›</button>

                    <div class="slider-dots">
                        ${design.images.map((_, index) => `
                            <span class="dot ${index === 0 ? 'active' : ''}"
                                  onclick="goToSlide(this, ${index})"></span>
                        `).join('')}
                    </div>

                    <div class="slider-counter">
                        <span class="current-slide">1</span> / ${design.images.length}
                    </div>
                ` : ''}
            </div>
        `;
    } else if (hasSingleImage) {
        console.log('Using single image:', design.image);
        // صورة واحدة فقط
        imageHTML = `<img src="/storage/${design.image}" alt="${design.name}" onerror="console.error('Failed to load image:', this.src); this.src='/path/to/placeholder.png';">`;
    } else {
        console.log('No images found');
        // لا توجد صور
        imageHTML = `<div style="font-size: 80px; text-align: center; padding: 40px; color: #ccc;">🎨<br><span style="font-size: 16px;">لا توجد صور</span></div>`;
    }

    // معالجة المقاسات
    const sizesHTML = design.sizes?.length > 0
        ? design.sizes.map(s => `<span class="badge">${s.name}</span>`).join('')
        : `<span class="badge">لا يوجد</span>`;

    // معالجة الألوان
    const colorsHTML = design.colors?.length > 0
        ? design.colors.map(c => `<span class="badge color-badge">${c.name}</span>`).join('')
        : `<span class="badge">لا يوجد</span>`;

    // معالجة الأكمام
    const sleevesHTML = design.sleeves?.length > 0
        ? design.sleeves.map(s => `<span class="badge sleeve-badge">${s.name}</span>`).join('')
        : `<span class="badge">لا يوجد</span>`;

    // معالجة القباب
    const domesHTML = design.domes?.length > 0
        ? design.domes.map(d => `<span class="badge dome-badge">${d.name}</span>`).join('')
        : `<span class="badge">لا يوجد</span>`;

    // معالجة الأقمشة
    const fabricsHTML = design.fabrics?.length > 0
        ? design.fabrics.map(f => `<span class="badge fabric-badge">${f.name}</span>`).join('')
        : `<span class="badge">لا يوجد</span>`;

    return `
        <div id="designPreviewModal" class="design-modal-overlay" onclick="closeDesignModalOnOutside(event)">
            <div class="design-modal-box">
                <div class="design-left">
                    ${imageHTML}
                </div>
                <div class="design-right">
                    <h2>📛 ${design.name}</h2>

                    <div class="info-item">
                        <div class="info-title">💰 السعر:</div>
                        <div style="font-size: 20px; font-weight: bold; color: #27ae60;">${design.price} ₪</div>
                    </div>

                    <div class="info-item">
                        <div class="info-title">👤 المستخدم:</div>
                        <div>${design.user_name}</div>
                    </div>

                    ${design.description ? `
                    <div class="info-item">
                        <div class="info-title">📝 الوصف:</div>
                        <div>${design.description}</div>
                    </div>
                    ` : ''}

                    <div class="info-item">
                        <div class="info-title">📏 المقاسات:</div>
                        <div class="badge-box">${sizesHTML}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-title">🎨 الألوان:</div>
                        <div class="badge-box">${colorsHTML}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-title">👔 الأكمام:</div>
                        <div class="badge-box">${sleevesHTML}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-title">🏛️ القباب:</div>
                        <div class="badge-box">${domesHTML}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-title">🧵 الأقمشة:</div>
                        <div class="badge-box">${fabricsHTML}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-title">📅 تاريخ الإنشاء:</div>
                        <div>${design.created_at}</div>
                    </div>

                    <button class="close-modal-btn" onclick="closeDesignModal()">
                        إغلاق ✖️
                    </button>
                </div>
            </div>
        </div>
    `;
}

function closeDesignModal() {
    const modal = document.getElementById('designPreviewModal');
    if (modal) {
        modal.remove();
    }
}

function closeDesignModalOnOutside(event) {
    if (event.target.id === 'designPreviewModal') {
        closeDesignModal();
    }
}

// ==========================================
// Image Slider Functions (للـ Cards والـ Modal)
// ==========================================

function nextImage(btn) {
    const slider = btn.closest('.slider-container');
    const images = slider.querySelectorAll('.slider-image');
    const dots = slider.querySelectorAll('.dot');
    const counter = slider.querySelector('.current-slide');
    let current = 0;

    images.forEach((img, i) => {
        if (img.classList.contains('active')) {
            current = i;
            img.classList.remove('active');
            dots[i]?.classList.remove('active');
        }
    });

    const next = (current + 1) % images.length;
    images[next].classList.add('active');
    dots[next]?.classList.add('active');
    if (counter) counter.textContent = next + 1;
}

function prevImage(btn) {
    const slider = btn.closest('.slider-container');
    const images = slider.querySelectorAll('.slider-image');
    const dots = slider.querySelectorAll('.dot');
    const counter = slider.querySelector('.current-slide');
    let current = 0;

    images.forEach((img, i) => {
        if (img.classList.contains('active')) {
            current = i;
            img.classList.remove('active');
            dots[i]?.classList.remove('active');
        }
    });

    const prev = (current - 1 + images.length) % images.length;
    images[prev].classList.add('active');
    dots[prev]?.classList.add('active');
    if (counter) counter.textContent = prev + 1;
}

function goToSlide(dot, index) {
    const slider = dot.closest('.slider-container');
    const images = slider.querySelectorAll('.slider-image');
    const dots = slider.querySelectorAll('.dot');
    const counter = slider.querySelector('.current-slide');

    images.forEach(img => img.classList.remove('active'));
    dots.forEach(d => d.classList.remove('active'));

    images[index].classList.add('active');
    dots[index].classList.add('active');
    if (counter) counter.textContent = index + 1;
}

// ==========================================
// Delete Modal Functions
// ==========================================

function confirmDelete(id, name) {
    const modal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const itemName = document.getElementById('deleteItemName');

    if (itemName) itemName.textContent = name;
    if (deleteForm) deleteForm.action = '/admin/designs/' + id;
    if (modal) modal.style.display = 'block';
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) modal.style.display = 'none';
}

// ==========================================
// Modal Close Events
// ==========================================

// Close modal when clicking outside
window.onclick = function(event) {
    const deleteModal = document.getElementById('deleteModal');

    if (event.target == deleteModal) {
        closeDeleteModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDesignModal();
        closeDeleteModal();
    }
});

// ==========================================
// Auto-hide Alert Messages
// ==========================================

window.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.querySelector('.alert-success');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.opacity = '0';
            setTimeout(() => successMessage.remove(), 300);
        }, 3000);
    }

    const errorMessage = document.querySelector('.alert-error');
    if (errorMessage) {
        setTimeout(() => {
            errorMessage.style.opacity = '0';
            setTimeout(() => errorMessage.remove(), 300);
        }, 5000);
    }
});

// ==========================================
// Card Animations on Load
// ==========================================

window.addEventListener('load', function() {
    const cards = document.querySelectorAll('.design-card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 50);
        }, index * 50);
    });
}); 
