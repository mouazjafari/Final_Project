@extends('admin.layouts.admin-layout')
@section('title', 'استخدامات الكوبون - لوحة التحكم')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coupons-styles.css') }}">
    <style>
        /* Additional Styles for Usages Page */
        .usages-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-top: 2rem;
        }

        .usages-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .usages-table th {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 1rem;
            text-align: right;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .usages-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            color: #1f2937;
            font-size: 0.9rem;
        }

        .usages-table tr:hover {
            background: #f9fafb;
        }

        .usages-table tr:last-child td {
            border-bottom: none;
        }

        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #3730a3;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .order-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .order-link:hover {
            color: #2563eb;
            text-decoration: underline;
        }

        .discount-badge {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            padding: 0.5rem 1rem;
            border-radius: 15px;
            font-weight: 700;
            font-size: 1rem;
            display: inline-block;
        }

        .coupon-summary {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .summary-item {
            text-align: center;
            padding: 1.5rem;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border-radius: 12px;
            border: 2px solid #e5e7eb;
        }

        .summary-label {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .summary-value {
            color: #1f2937;
            font-size: 2rem;
            font-weight: 700;
        }

        .summary-value.highlight {
            color: #10b981;
        }

        @media (max-width: 768px) {
            .usages-table {
                overflow-x: auto;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="page-header" style="background: linear-gradient(90deg,#2c3e50,#34495e);">
        <h2>📊 استخدامات الكوبون: {{ $coupon->code }}</h2>
        <a href="{{ route('admin.coupons.index') }}" class="btn-back">
            ← العودة للكوبونات
        </a>
    </div>

    <!-- Coupon Summary -->
    <div class="coupon-summary">
        <h3 style="margin: 0 0 1.5rem 0; color: #1f2937; font-size: 1.3rem;">
            📋 ملخص الكوبون
        </h3>

        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">🎟️ الكود</div>
                <div class="summary-value">{{ $coupon->code }}</div>
            </div>

            <div class="summary-item">
                <div class="summary-label">💰 قيمة الخصم</div>
                <div class="summary-value">
                    @if ($coupon->type === 'percentage')
                        {{ $coupon->amount }}%
                    @else
                        {{ number_format($coupon->amount, 2) }} ₪
                    @endif
                </div>
            </div>

            <div class="summary-item">
                <div class="summary-label">📊 عدد الاستخدامات</div>
                <div class="summary-value highlight">
                    {{ $coupon->used_count }} / {{ $coupon->max_uses ?? '∞' }}
                </div>
            </div>

            <div class="summary-item">
                <div class="summary-label">💵 إجمالي الخصومات</div>
                <div class="summary-value highlight">
                    {{ number_format($coupon->usages->sum('discount_amount'), 2) }} ₪
                </div>
            </div>

            <div class="summary-item">
                <div class="summary-label">✅ الحالة</div>
                <div class="summary-value" style="font-size: 1.5rem;">
                    @if ($coupon->expires_at && $coupon->expires_at->isPast())
                        ⏰ منتهي
                    @elseif ($coupon->is_active)
                        ✅ مفعل
                    @else
                        ❌ معطل
                    @endif
                </div>
            </div>

            @if ($coupon->expires_at)
                <div class="summary-item">
                    <div class="summary-label">📅 تاريخ الانتهاء</div>
                    <div class="summary-value" style="font-size: 1.2rem;">
                        {{ $coupon->expires_at->format('Y-m-d') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Usages Table -->
    @if ($coupon->usages->count() > 0)
        <div class="usages-table">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>👤 المستخدم</th>
                        <th>🛒 رقم الطلب</th>
                        <th>💰 قيمة الخصم</th>
                        <th>📅 تاريخ الاستخدام</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($coupon->usages as $index => $usage)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="user-badge">
                                    👤 {{ $usage->user->name ?? 'غير محدد' }}
                                </span>
                                <div style="font-size: 0.8rem; color: #6b7280; margin-top: 0.3rem;">
                                    {{ $usage->user->email ?? '-' }}
                                </div>
                            </td>
                            <td>
                                @if ($usage->order)
                                    <a href="{{ route('admin.orders.show', $usage->order_id) }}"
                                       class="order-link"
                                       target="_blank">
                                        🛒 طلب #{{ $usage->order_id }}
                                    </a>
                                @else
                                    <span style="color: #9ca3af;">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="discount-badge">
                                    {{ number_format($usage->discount_amount, 2) }} ₪
                                </span>
                            </td>
                            <td>
                                {{ $usage->created_at->format('Y-m-d H:i') }}
                                <div style="font-size: 0.8rem; color: #6b7280; margin-top: 0.3rem;">
                                    {{ $usage->created_at->diffForHumans() }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total Summary Row -->
        <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 1.5rem; border-radius: 15px; margin-top: 1rem; border: 2px solid #3b82f6;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <span style="color: #1e40af; font-weight: 600; font-size: 1.1rem;">
                        📊 إجمالي عدد الاستخدامات:
                    </span>
                    <span style="color: #1f2937; font-weight: 700; font-size: 1.3rem; margin-right: 0.5rem;">
                        {{ $coupon->usages->count() }}
                    </span>
                </div>

                <div>
                    <span style="color: #1e40af; font-weight: 600; font-size: 1.1rem;">
                        💵 إجمالي الخصومات الممنوحة:
                    </span>
                    <span style="color: #10b981; font-weight: 700; font-size: 1.3rem; margin-right: 0.5rem;">
                        {{ number_format($coupon->usages->sum('discount_amount'), 2) }} ₪
                    </span>
                </div>
            </div>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <h3>لا توجد استخدامات لهذا الكوبون حتى الآن</h3>
            <p>لم يقم أي مستخدم باستخدام هذا الكوبون بعد</p>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        console.log('✅ Coupon Usages page loaded successfully!');

        // Auto-hide alerts
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
    </script>
@endpush
