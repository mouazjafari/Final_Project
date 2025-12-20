// ==================== Filter Functions ====================

// البحث برقم الطلب
document.getElementById('searchOrderInput')?.addEventListener('input', function(e) {
    const searchValue = e.target.value.toLowerCase();
    filterOrders();
});

// البحث باسم العميل
document.getElementById('searchUserInput')?.addEventListener('input', function(e) {
    const searchValue = e.target.value.toLowerCase();
    filterOrders();
});

// فلتر الحالة
document.getElementById('statusFilter')?.addEventListener('change', function() {
    filterOrders();
});

// فلتر السعر
document.getElementById('priceFilter')?.addEventListener('change', function() {
    filterOrders();
});

// فلتر التاريخ من
document.getElementById('dateFromFilter')?.addEventListener('change', function() {
    filterOrders();
});

// فلتر التاريخ إلى
document.getElementById('dateToFilter')?.addEventListener('change', function() {
    filterOrders();
});

// دالة الفلترة الرئيسية
function filterOrders() {
    const searchOrder = document.getElementById('searchOrderInput')?.value.toLowerCase() || '';
    const searchUser = document.getElementById('searchUserInput')?.value.toLowerCase() || '';
    const statusFilter = document.getElementById('statusFilter')?.value || '';
    const priceFilter = document.getElementById('priceFilter')?.value || '';
    const dateFrom = document.getElementById('dateFromFilter')?.value || '';
    const dateTo = document.getElementById('dateToFilter')?.value || '';

    const orderCards = document.querySelectorAll('.order-card');

    orderCards.forEach(card => {
        const orderId = card.dataset.orderId;
        const userName = card.dataset.userName;
        const status = card.dataset.status;
        const price = parseFloat(card.dataset.price);
        const date = card.dataset.date;

        let show = true;

        // فلتر رقم الطلب
        if (searchOrder && !orderId.includes(searchOrder)) {
            show = false;
        }

        // فلتر اسم المستخدم
        if (searchUser && !userName.includes(searchUser)) {
            show = false;
        }

        // فلتر الحالة
        if (statusFilter && status !== statusFilter) {
            show = false;
        }

        // فلتر السعر
        if (priceFilter) {
            if (priceFilter === '0-100' && (price < 0 || price > 100)) {
                show = false;
            } else if (priceFilter === '100-300' && (price < 100 || price > 300)) {
                show = false;
            } else if (priceFilter === '300-500' && (price < 300 || price > 500)) {
                show = false;
            } else if (priceFilter === '500+' && price < 500) {
                show = false;
            }
        }

        // فلتر التاريخ من
        if (dateFrom && date < dateFrom) {
            show = false;
        }

        // فلتر التاريخ إلى
        if (dateTo && date > dateTo) {
            show = false;
        }

        card.style.display = show ? 'block' : 'none';
    });

    // عرض رسالة إذا لم تكن هناك نتائج
    checkEmptyResults();
}

// التحقق من وجود نتائج
function checkEmptyResults() {
    const orderCards = document.querySelectorAll('.order-card');
    const visibleCards = Array.from(orderCards).filter(card => card.style.display !== 'none');

    let emptyMessage = document.querySelector('.no-results-message');

    if (visibleCards.length === 0) {
        if (!emptyMessage) {
            emptyMessage = document.createElement('div');
            emptyMessage.className = 'empty-state no-results-message';
            emptyMessage.innerHTML = `
                <div class="empty-icon">🔍</div>
                <h3>لا توجد نتائج</h3>
                <p>لم يتم العثور على طلبات تطابق معايير البحث</p>
            `;
            document.querySelector('.addresses-grid').after(emptyMessage);
        }
    } else {
        if (emptyMessage) {
            emptyMessage.remove();
        }
    }
}

// ==================== Advanced Filters Toggle ====================

function toggleAdvancedFilters() {
    const content = document.getElementById('advancedFiltersContent');
    const icon = document.getElementById('toggleIcon');

    if (content.style.display === 'none' || content.style.display === '') {
        content.style.display = 'block';
        icon.textContent = '▲';
    } else {
        content.style.display = 'none';
        icon.textContent = '▼';
    }
}

// ==================== Reset Filters ====================

function resetFilters() {
    document.getElementById('searchOrderInput').value = '';
    document.getElementById('searchUserInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('priceFilter').value = '';
    document.getElementById('dateFromFilter').value = '';
    document.getElementById('dateToFilter').value = '';

    filterOrders();

    showSuccessMessage('✅ تم إعادة تعيين جميع الفلاتر');
}

// ==================== Update Order Status ====================

function updateOrderStatus(orderId, newStatus) {
    if (!confirm('هل أنت متأكد من تغيير حالة الطلب؟')) {
        location.reload();
        return;
    }

    const selectElement = document.querySelector(`[data-order-id="${orderId}"]`);
    const originalClass = selectElement.className;

    // إضافة تأثير التحميل
    selectElement.disabled = true;
    selectElement.style.opacity = '0.6';

    fetch(`/admin/orders/${orderId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // تحديث class الـ select
            selectElement.className = `status-select status-${newStatus}`;
            selectElement.style.opacity = '1';
            selectElement.disabled = false;

            // تحديث data attribute
            const card = selectElement.closest('.order-card');
            card.dataset.status = newStatus;

            // إظهار رسالة نجاح
            showSuccessMessage('✅ تم تحديث حالة الطلب بنجاح!');

            // تأثير نبض على البطاقة
            card.style.backgroundColor = '#d1fae5';
            setTimeout(() => {
                card.style.backgroundColor = '';
            }, 1000);
        } else {
            showErrorMessage('❌ حدث خطأ أثناء تحديث الحالة');
            selectElement.className = originalClass;
            selectElement.disabled = false;
            selectElement.style.opacity = '1';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('❌ حدث خطأ في الاتصال بالخادم');
        selectElement.className = originalClass;
        selectElement.disabled = false;
        selectElement.style.opacity = '1';
    });
}

// ==================== Show Order Details ====================

function showOrderDetails(orderId) {
    const modal = document.getElementById('orderDetailsModal');
    const modalBody = document.getElementById('orderDetailsContent');

    // عرض المودال
    modal.style.display = 'block';

    // عرض شاشة التحميل
    modalBody.innerHTML = `
        <div class="loading-spinner">
            <div style="display: inline-block; width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #667eea; border-radius: 50%; animation: spin 1s linear infinite;"></div>
            <p style="margin-top: 1rem;">جاري تحميل التفاصيل...</p>
        </div>
    `;

    fetch(`/admin/orders/${orderId}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            const order = data.order;

            // تحديد نص الحالة
            const statusTexts = {
                'pending': '⏳ قيد الانتظار',
                'processing': '🔄 قيد المعالجة',
                'completed': '✅ اكتمل',
                'cancelled': '❌ ملغي'
            };

            const statusText = statusTexts[order.status] || 'غير محدد';

            // بناء HTML للتفاصيل
            modalBody.innerHTML = `
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <div style="background: #f9fafb; padding: 1.5rem; border-radius: 15px; border: 2px solid #e5e7eb;">
                        <h6 style="color: #667eea; margin-bottom: 1rem; font-weight: 700; font-size: 1.1rem;">
                            <span style="font-size: 1.3rem;">📋</span> معلومات الطلب
                        </h6>
                        <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb;">
                                <span style="color: #6b7280; font-weight: 600;">الحالة:</span>
                                <span class="status-select status-${order.status}" style="padding: 0.4rem 1rem; border-radius: 15px; font-size: 0.85rem;">${statusText}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb;">
                                <span style="color: #6b7280; font-weight: 600;">تاريخ الطلب:</span>
                                <span style="color: #1f2937; font-weight: 500;">📅 ${order.created_at}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                                <span style="color: #6b7280; font-weight: 600;">الإجمالي:</span>
                                <span style="color: #10b981; font-weight: 700; font-size: 1.3rem;">💰 ${order.total_price} ₪</span>
                            </div>
                        </div>
                    </div>

                    <div style="background: #f9fafb; padding: 1.5rem; border-radius: 15px; border: 2px solid #e5e7eb;">
                        <h6 style="color: #667eea; margin-bottom: 1rem; font-weight: 700; font-size: 1.1rem;">
                            <span style="font-size: 1.3rem;">👤</span> معلومات العميل
                        </h6>
                        <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb;">
                                <span style="color: #6b7280; font-weight: 600;">الاسم:</span>
                                <span style="color: #1f2937; font-weight: 500;">${order.user.name}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb;">
                                <span style="color: #6b7280; font-weight: 600;">البريد:</span>
                                <span style="color: #1f2937; font-weight: 500;">📧 ${order.user.email}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb;">
                                <span style="color: #6b7280; font-weight: 600;">العنوان:</span>
                                <span style="color: #1f2937; font-weight: 500;">📍 ${order.address.street}</span>
                            </div>
                            ${order.notes ? `
                            <div style="display: flex; flex-direction: column; gap: 0.3rem; padding: 0.5rem 0;">
                                <span style="color: #6b7280; font-weight: 600;">ملاحظات:</span>
                                <span style="color: #1f2937; font-style: italic; font-size: 0.9rem;">📝 ${order.notes}</span>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                </div>

                <div style="background: white; border-radius: 15px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); border: 2px solid #e5e7eb;">
                    <h6 style="color: #667eea; margin-bottom: 1.5rem; font-weight: 700; font-size: 1.2rem;">
                        <span style="font-size: 1.5rem;">🎨</span> تفاصيل التصميم
                    </h6>
                    <div style="display: grid; gap: 1.5rem;">
                        ${order.designOrders && order.designOrders.length > 0 ?
                            order.designOrders.map(doItem => `
                                <div style="display: grid; grid-template-columns: 200px 1fr; gap: 1.5rem; align-items: start; border: 2px solid #e5e7eb; padding: 1rem; border-radius: 15px;">
                                    <div style="text-align: center;">
                                        ${doItem.design_images && doItem.design_images.length > 0 && doItem.design_images[0].path && doItem.design_images[0].path !== 'null' ?
                                            `<img src="/storage/${doItem.design_images[0].path}"
                                                style="width: 100%; height: 200px; object-fit: cover; border-radius: 15px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);"
                                                onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                                alt="صورة التصميم">
                                             <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #f0f0f0 0%, #e5e5e5 100%); border-radius: 15px; display: none; align-items: center; justify-content: center; font-size: 3rem; opacity: 0.3;">
                                                🎨
                                            </div>` :
                                            `<div style="width: 100%; height: 200px; background: linear-gradient(135deg, #f0f0f0 0%, #e5e5e5 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 3rem; opacity: 0.3;">
                                                🎨
                                            </div>`
                                        }
                                    </div>
                                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                                        <h5 style="color: #1f2937; margin-bottom: 0.5rem; font-size: 1.3rem; font-weight: 700;">
                                            ${(() => {
                                                try {
                                                    const name = doItem.design_name || 'غير محدد';
                                                    if (typeof name === 'string' && name.startsWith('{')) {
                                                        const parsed = JSON.parse(name);
                                                        return parsed.ar || parsed.en || 'غير محدد';
                                                    }
                                                    return name;
                                                } catch(e) {
                                                    return doItem.design_name || 'غير محدد';
                                                }
                                            })()}
                                        </h5>
                                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                                            <div style="background: #f0f9ff; padding: 1rem; border-radius: 10px; border: 2px solid #bfdbfe;">
                                                <div style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.3rem;">المقاس</div>
                                                <div style="color: #1e40af; font-weight: 700; font-size: 1.1rem;">
                                                    📏 ${doItem.size ? doItem.size.name : 'غير محدد'}
                                                </div>
                                            </div>
                                            <div style="background: #f0fdf4; padding: 1rem; border-radius: 10px; border: 2px solid #bbf7d0;">
                                                <div style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.3rem;">الكمية</div>
                                                <div style="color: #15803d; font-weight: 700; font-size: 1.1rem;">
                                                    📦 ${doItem.quantity}
                                                </div>
                                            </div>
                                        </div>

                                        ${doItem.selected_options && doItem.selected_options.length > 0 ? `
                                            <div style="background: #fef3c7; padding: 1rem; border-radius: 10px; border: 2px solid #fcd34d; margin-top: 1rem;">
                                                <div style="color: #92400e; font-weight: 700; margin-bottom: 0.8rem; font-size: 1rem;">
                                                    ⚙️ الخيارات المختارة:
                                                </div>
                                                <div style="display: flex; flex-wrap: wrap; gap: 0.6rem;">
                                                    ${doItem.selected_options.map(option => {
                                                        let optionName = 'خيار';

                                                        try {
                                                            const nameData = typeof option.name === 'string' && option.name.startsWith('{')
                                                                ? JSON.parse(option.name)
                                                                : option.name;
                                                            optionName = nameData?.ar || nameData?.en || nameData || 'خيار';
                                                        } catch(e) {
                                                            optionName = option.name || 'خيار';
                                                        }

                                                        return `
                                                            <div style="background: white; padding: 0.6rem 1.2rem; border-radius: 20px; border: 2px solid #fbbf24; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                                <span style="color: #78350f; font-weight: 600; font-size: 0.9rem;">
                                                                    ✓ ${optionName}
                                                                </span>
                                                            </div>
                                                        `;
                                                    }).join('')}
                                                </div>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            `).join('')
                        : '<p style="text-align: center; color: #6b7280; padding: 2rem;">لا توجد تصاميم مرتبطة بهذا الطلب</p>'}
                    </div>
                </div>

            `;

            // إضافة تأثير Fade In
            modalBody.style.opacity = '0';
            setTimeout(() => {
                modalBody.style.transition = 'opacity 0.5s ease';
                modalBody.style.opacity = '1';
            }, 100);
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = `
                <div style="text-align: center; padding: 2rem; color: #ef4444;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">⚠️</div>
                    <h4 style="margin-bottom: 0.5rem;">حدث خطأ أثناء تحميل التفاصيل</h4>
                    <p style="color: #6b7280;">يرجى المحاولة مرة أخرى</p>
                </div>
            `;
        });
}

// ==================== Close Order Modal ====================

function closeOrderModal() {
    document.getElementById('orderDetailsModal').style.display = 'none';
}

// إغلاق المودال عند النقر خارجه
window.onclick = function(event) {
    const modal = document.getElementById('orderDetailsModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
};

// ==================== Success/Error Messages ====================

function showSuccessMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success';
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '99999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = message;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
}

function showErrorMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-error';
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '99999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = message;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
}

// ==================== Auto-hide Alerts ====================

document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

// ==================== CSS for Spinner Animation ====================

const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
