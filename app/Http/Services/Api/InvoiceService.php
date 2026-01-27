<?php

namespace App\Http\Services\Api;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Enum\OrderStatusEnum;
use App\Models\Invoice;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    public function createInvoice(Order $order)
    {
        // Logic to create an invoice
        if ($order->status !== OrderStatusEnum::Completed->value) {
            throw new GeneralException('cannot create invoice for an order that is not completed', 400);
        }

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-' . Date::now()->format('YmdHis') . '-' . $order->id,
            'total' => $order->total_price
        ]);
        $invoice->load(['order.design', 'order.user', 'order.coupon']); // تحميل العلاقة مع الكوبون
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice
        ]);
        $pdfPath = 'invoices/' . $invoice->invoice_number . '.pdf';
        Storage::disk('public')->put($pdfPath, $pdf->output());
        $invoice->pdf_url = $pdfPath;
        $invoice->save();
        return $invoice;
    }
    public function downloadInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);

        return response()->download(
            storage_path('app/public/' . $invoice->pdf_url)
        );
    }
}
