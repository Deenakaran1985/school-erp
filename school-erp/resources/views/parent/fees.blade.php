@extends('layouts.app')
@section('title', 'Fee Payment')

@section('content')
<div class="max-w-3xl mx-auto space-y-5" x-data="feePortal()" x-init="init()">
  <div class="flex items-center gap-3">
    <a href="{{ route('parent.home') }}" class="text-slate-400 hover:text-slate-600">← Back</a>
    <h3 class="text-lg font-semibold text-slate-700">Fee Payment</h3>
  </div>

  <div x-show="loading" class="text-center py-10 text-slate-400">Loading fee details…</div>

  <template x-if="!loading">
    <div class="space-y-4">
      <!-- Pending Fees -->
      <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <h4 class="font-semibold text-slate-700 mb-3">Pending Fees</h4>
        <template x-for="fee in pending" :key="fee.fee_structure_id">
          <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
            <div>
              <p class="font-medium text-slate-800" x-text="fee.fee_head"></p>
              <p class="text-xs text-slate-400" x-text="`${fee.student_name} · ${fee.class} · Due: ${fee.due_date ?? 'N/A'}`"></p>
              <span x-show="fee.overdue" class="text-xs text-red-500 font-medium">OVERDUE</span>
            </div>
            <div class="text-right">
              <p class="font-bold text-slate-800" x-text="`₹${Number(fee.amount).toLocaleString('en-IN')}`"></p>
              <button @click="payFee(fee)"
                class="mt-1 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg">Pay Now</button>
            </div>
          </div>
        </template>
        <template x-if="pending.length === 0">
          <p class="text-sm text-emerald-600 py-4 text-center">✅ All fees are paid!</p>
        </template>
      </div>

      <!-- Payment History -->
      <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <h4 class="font-semibold text-slate-700 mb-3">Payment History</h4>
        <template x-for="p in history" :key="p.id">
          <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0 text-sm">
            <div>
              <p class="font-medium text-slate-800" x-text="p.fee_head"></p>
              <p class="text-xs text-slate-400" x-text="`${p.student} · ${p.date} · ${p.mode}`"></p>
            </div>
            <div class="text-right">
              <p class="font-medium text-emerald-600" x-text="`₹${Number(p.amount).toLocaleString('en-IN')}`"></p>
              <p class="text-xs text-slate-400" x-text="p.receipt_no"></p>
            </div>
          </div>
        </template>
        <template x-if="history.length === 0">
          <p class="text-sm text-slate-400 py-4 text-center">No payment history.</p>
        </template>
      </div>
    </div>
  </template>
</div>

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function feePortal() {
  return {
    loading: true,
    pending: [],
    history: [],
    async init() {
      const [p, h] = await Promise.all([
        fetch('/api/fees/pending', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json()),
        fetch('/api/fees/history', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json()),
      ]);
      this.pending = p.success ? p.data : [];
      this.history = h.success ? h.data : [];
      this.loading = false;
    },
    async payFee(fee) {
      const res  = await fetch('/api/fees/create-order', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ student_id: fee.student_id, fee_structure_id: fee.fee_structure_id }),
      });
      const order = await res.json();
      if (!order.success) { alert(order.message); return; }

      const rzp = new Razorpay({
        key: order.key,
        amount: order.amount,
        currency: order.currency,
        name: 'School ERP',
        description: order.description,
        order_id: order.order_id,
        prefill: order.prefill,
        handler: async (response) => {
          await fetch('/api/fees/verify', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
              'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
              payment_id:          order.payment_id,
              razorpay_order_id:   response.razorpay_order_id,
              razorpay_payment_id: response.razorpay_payment_id,
              razorpay_signature:  response.razorpay_signature,
            }),
          });
          alert('Payment successful! Refreshing…');
          location.reload();
        },
      });
      rzp.open();
    },
  }
}
</script>
@endpush
@endsection
