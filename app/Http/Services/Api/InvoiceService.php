<?php

namespace App\Http\Services\Api;

use App\Exceptions\GeneralException;
use App\Http\Enum\OrderStatusEnum;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf; // ✅ استيراد mPDF

class InvoiceService
{
    public function createInvoice(Order $order)
    {
        if ($order->status !== OrderStatusEnum::Completed->value) {
            throw new GeneralException('cannot create invoice for an order that is not completed', 400);
        }

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-' . Date::now()->format('YmdHis') . '-' . $order->id,
            'total' => $order->total_price
        ]);

        $invoice->load([
            'order.designOrders.design',
            'order.designOrders.size',
            'order.designOrders.options',
            'order.user',
            'order.address.city',
            'order.coupon',
            'order.payments'
        ]);

        try {
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 20,
                'margin_bottom' => 20,
                'margin_header' => 10,
                'margin_footer' => 10,
                'default_font' => 'dejavusans',
                'autoScriptToLang' => true,
                'autoLangToFont' => true,
            ]);

            $html = view('invoices.pdf', ['invoice' => $invoice])->render();

            $mpdf->WriteHTML($html);

            $pdfContent = $mpdf->Output('', 'S');
            $pdfPath = 'invoices/' . $invoice->invoice_number . '.pdf';
            Storage::disk('public')->put($pdfPath, $pdfContent);

            $invoice->pdf_url = $pdfPath;
            $invoice->save();

            return $invoice;
        } catch (\Exception $e) {
            FacadesLog::error('PDF Generation Error: ' . $e->getMessage());
            throw new GeneralException('Failed to generate invoice PDF: ' . $e->getMessage(), 500);
        }
    }

    public function downloadInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);

        if (!Storage::disk('public')->exists($invoice->pdf_url)) {
            throw new GeneralException('Invoice file not found', 404);
        }

        return response()->download(
            storage_path('app/public/' . $invoice->pdf_url),
            $invoice->invoice_number . '.pdf'
        );
    }
}
