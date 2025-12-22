// ==================== Show Order Details - NEW DESIGN ====================

function showOrderDetails(orderId) {
    const modal = document.getElementById('orderDetailsModal');
    const modalBody = document.getElementById('orderDetailsContent');

    modal.style.display = 'block';

    modalBody.innerHTML = `
        <div class="loading-spinner">
            <div style="display: inline-block; width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #667eea; border-radius: 50%; animation: spin 1s linear infinite;"></div>
            <p style="margin-top: 1rem; color: #667eea; font-weight: 600;">جاري تحميل التفاصيل...</p>
        </div>
    `;

    fetch(`/admin/orders/${orderId}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            const order = data.order;

            const statusConfig = {
                'pending': { text: 'قيد الانتظار', icon: '⏳', color: '#f59e0b', bg: '#fef3c7' },
                'processing': { text: 'قيد المعالجة', icon: '🔄', color: '#3b82f6', bg: '#dbeafe' },
                'completed': { text: 'اكتمل', icon: '✅', color: '#10b981', bg: '#d1fae5' },
                'cancelled': { text: 'ملغي', icon: '❌', color: '#ef4444', bg: '#fee2e2' }
            };

            const status = statusConfig[order.status] || statusConfig['pending'];

            modalBody.innerHTML = `
                <!-- Header Section -->
                <div style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); padding: 2rem; border-radius: 15px; margin-bottom: 2rem; color: white; box-shadow: 0 10px 30px rgba(44, 62, 80, 0.3);">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h2 style="margin: 0 0 0.5rem 0; font-size: 1.8rem; font-weight: 700;">
                                🛒 طلب رقم #${order.id}
                            </h2>
                            <p style="margin: 0; opacity: 0.9; font-size: 0.95rem;">
                                📅 ${order.created_at}
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <div style="background: ${status.bg}; color: ${status.color}; padding: 0.8rem 1.5rem; border-radius: 25px; font-weight: 700; font-size: 1.1rem; display: inline-block; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                ${status.icon} ${status.text}
                            </div>
                            <div style="margin-top: 1rem; font-size: 2rem; font-weight: 800;">
                                💰 ${order.total_price} ₪
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer & Address Info -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <!-- Customer Card -->
                    <div style="background: white; border-radius: 15px; padding: 1.5rem; border: 2px solid #e5e7eb; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 2px solid #f3f4f6;">
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #2c3e50, #34495e); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 10px rgba(44, 62, 80, 0.3);">
                                👤
                            </div>
                            <div>
                                <h3 style="margin: 0; color: #1f2937; font-size: 1.2rem; font-weight: 700;">معلومات العميل</h3>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 1.2rem;">👨‍💼</span>
                                <span style="color: #6b7280; font-size: 0.9rem;">الاسم:</span>
                                <strong style="color: #1f2937; margin-right: auto;">${order.user.name}</strong>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 1.2rem;">📧</span>
                                <span style="color: #6b7280; font-size: 0.9rem;">البريد:</span>
                                <strong style="color: #1f2937; margin-right: auto; font-size: 0.85rem;">${order.user.email}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Address Card -->
                    <div style="background: white; border-radius: 15px; padding: 1.5rem; border: 2px solid #e5e7eb; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 2px solid #f3f4f6;">
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #2c3e50, #34495e); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 10px rgba(44, 62, 80, 0.3);">
                                📍
                            </div>
                            <div>
                                <h3 style="margin: 0; color: #1f2937; font-size: 1.2rem; font-weight: 700;">عنوان التوصيل</h3>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 1.2rem;">🏙️</span>
                                <span style="color: #6b7280; font-size: 0.9rem;">المدينة:</span>
                                <strong style="color: #1f2937; margin-right: auto;">${order.address.city}</strong>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 1.2rem;">🛣️</span>
                                <span style="color: #6b7280; font-size: 0.9rem;">الشارع:</span>
                                <strong style="color: #1f2937; margin-right: auto;">${order.address.street}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                ${order.notes ? `
                <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 15px; padding: 1.5rem; margin-bottom: 2rem; border: 2px solid #fbbf24; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.2);">
                    <div style="display: flex; align-items: start; gap: 1rem;">
                        <span style="font-size: 2rem;">📝</span>
                        <div>
                            <h4 style="margin: 0 0 0.5rem 0; color: #92400e; font-weight: 700;">ملاحظات الطلب:</h4>
                            <p style="margin: 0; color: #78350f; line-height: 1.6; font-style: italic;">${order.notes}</p>
                        </div>
                    </div>
                </div>
                ` : ''}

                <!-- Design Orders Section -->
                <div style="background: #f9fafb; border-radius: 15px; padding: 2rem; border: 2px solid #e5e7eb;">
                    <h3 style="margin: 0 0 1.5rem 0; color: #1f2937; font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-size: 2rem;">🎨</span> تفاصيل التصاميم
                    </h3>

                    ${order.designOrders && order.designOrders.length > 0 ?
                        order.designOrders.map((doItem, index) => {
                            let designName = 'غير محدد';
                            try {
                                const name = doItem.design_name || 'غير محدد';
                                if (typeof name === 'string' && name.startsWith('{')) {
                                    const parsed = JSON.parse(name);
                                    designName = parsed.ar || parsed.en || 'غير محدد';
                                } else {
                                    designName = name;
                                }
                            } catch(e) {
                                designName = doItem.design_name || 'غير محدد';
                            }

                            let imageHTML = '';
                            if (doItem.design_images && doItem.design_images.length > 0 && doItem.design_images[0].path) {
                                imageHTML = `
                                    <img src="/storage/${doItem.design_images[0].path}"
                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                        alt="صورة التصميم">
                                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #f0f0f0, #e5e5e5); border-radius: 12px; display: none; align-items: center; justify-content: center; font-size: 4rem; opacity: 0.4;">
                                        🎨
                                    </div>
                                `;
                            } else {
                                imageHTML = `
                                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #f0f0f0, #e5e5e5); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 4rem; opacity: 0.4;">
                                        🎨
                                    </div>
                                `;
                            }

                            let optionsHTML = '';
                            if (doItem.selected_options && doItem.selected_options.length > 0) {
                                optionsHTML = `
                                    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px dashed #e5e7eb;">
                                        <h4 style="margin: 0 0 1rem 0; color: #6b7280; font-size: 1rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
                                            ⚙️ الخيارات المختارة
                                        </h4>
                                        <div style="display: flex; flex-wrap: wrap; gap: 0.8rem;">
                                            ${doItem.selected_options.map(option => {
                                                let optionName = 'خيار';
                                                try {
                                                    if (option.name) {
                                                        if (typeof option.name === 'object') {
                                                            optionName = option.name.ar || option.name.en || 'خيار';
                                                        } else if (typeof option.name === 'string' && option.name.startsWith('{')) {
                                                            const parsed = JSON.parse(option.name);
                                                            optionName = parsed.ar || parsed.en || 'خيار';
                                                        } else {
                                                            optionName = option.name;
                                                        }
                                                    }
                                                } catch(e) {
                                                    optionName = option.name || 'خيار';
                                                }

                                                const typeConfig = {
                                                    'color': { icon: '🎨', bg: '#fce7f3', color: '#be185d', border: '#f9a8d4' },
                                                    'sleeve': { icon: '👔', bg: '#dbeafe', color: '#1e40af', border: '#93c5fd' },
                                                    'dome': { icon: '🏛️', bg: '#fef3c7', color: '#92400e', border: '#fcd34d' },
                                                    'fabric': { icon: '🧵', bg: '#d1fae5', color: '#065f46', border: '#6ee7b7' }
                                                };
                                                const config = typeConfig[option.type] || typeConfig['color'];

                                                return `
                                                    <div style="background: ${config.bg}; color: ${config.color}; padding: 0.6rem 1.2rem; border-radius: 20px; border: 2px solid ${config.border}; font-weight: 600; font-size: 0.9rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 0.5rem;">
                                                        <span>${config.icon}</span>
                                                        <span>${optionName}</span>
                                                    </div>
                                                `;
                                            }).join('')}
                                        </div>
                                    </div>
                                `;
                            }

                            return `
                                <div style="background: white; border-radius: 15px; padding: 1.5rem; margin-bottom: ${index < order.designOrders.length - 1 ? '1.5rem' : '0'}; border: 2px solid #e5e7eb; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                                    <div style="display: grid; grid-template-columns: 180px 1fr; gap: 1.5rem; align-items: start;">
                                        <!-- Image -->
                                        <div style="width: 180px; height: 180px; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                            ${imageHTML}
                                        </div>

                                        <!-- Details -->
                                        <div>
                                            <h4 style="margin: 0 0 1rem 0; color: #1f2937; font-size: 1.3rem; font-weight: 700;">
                                                ${designName}
                                            </h4>

                                            <!-- Stats Grid -->
                                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem;">
                                                <div style="background: linear-gradient(135deg, #ede9fe, #ddd6fe); padding: 1rem; border-radius: 10px; border: 2px solid #c4b5fd;">
                                                    <div style="color: #6b21a8; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.3rem; opacity: 0.8;">المقاس</div>
                                                    <div style="color: #6b21a8; font-weight: 700; font-size: 1.1rem;">
                                                        📏 ${doItem.size ? doItem.size.name : '-'}
                                                    </div>
                                                </div>

                                                <div style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); padding: 1rem; border-radius: 10px; border: 2px solid #6ee7b7;">
                                                    <div style="color: #047857; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.3rem; opacity: 0.8;">الكمية</div>
                                                    <div style="color: #047857; font-weight: 700; font-size: 1.1rem;">
                                                        📦 ${doItem.quantity}
                                                    </div>
                                                </div>

                                                <div style="background: linear-gradient(135deg, #fef3c7, #fde68a); padding: 1rem; border-radius: 10px; border: 2px solid #fcd34d;">
                                                    <div style="color: #92400e; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.3rem; opacity: 0.8;">سعر الوحدة</div>
                                                    <div style="color: #92400e; font-weight: 700; font-size: 1.1rem;">
                                                        💵 ${doItem.unit_price || 0} ₪
                                                    </div>
                                                </div>

                                                <div style="background: linear-gradient(135deg, #fce7f3, #fbcfe8); padding: 1rem; border-radius: 10px; border: 2px solid #f9a8d4;">
                                                    <div style="color: #be185d; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.3rem; opacity: 0.8;">الإجمالي</div>
                                                    <div style="color: #be185d; font-weight: 700; font-size: 1.1rem;">
                                                        💰 ${doItem.total_price || 0} ₪
                                                    </div>
                                                </div>
                                            </div>

                                            ${optionsHTML}
                                        </div>
                                    </div>
                                </div>
                            `;
                        }).join('')
                    : `
                        <div style="text-align: center; padding: 3rem; color: #9ca3af;">
                            <div style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.5;">📭</div>
                            <p style="margin: 0; font-size: 1.1rem;">لا توجد تصاميم مرتبطة بهذا الطلب</p>
                        </div>
                    `}
                </div>
            `;

            modalBody.style.opacity = '0';
            setTimeout(() => {
                modalBody.style.transition = 'opacity 0.5s ease';
                modalBody.style.opacity = '1';
            }, 100);
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = `
                <div style="text-align: center; padding: 3rem;">
                    <div style="font-size: 5rem; margin-bottom: 1rem;">⚠️</div>
                    <h3 style="color: #ef4444; margin-bottom: 0.5rem;">حدث خطأ أثناء تحميل التفاصيل</h3>
                    <p style="color: #6b7280;">يرجى المحاولة مرة أخرى</p>
                </div>
            `;
        });
}

// ==================== Close Order Modal ====================

function closeOrderModal() {
    const modal = document.getElementById('orderDetailsModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// إغلاق المودال عند النقر خارجه
window.onclick = function(event) {
    const modal = document.getElementById('orderDetailsModal');
    if (event.target === modal) {
        closeOrderModal();
    }
};

// إغلاق المودال بزر Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeOrderModal();
    }
});

// CSS for Spinner
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
