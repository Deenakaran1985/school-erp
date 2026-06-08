<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Receipt {{ $payment->receipt_no }}</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family: 'DejaVu Sans', sans-serif;
      font-size: 11px;
      color: #1e293b;
      padding: 24px;
    }
    .header {
      text-align: center;
      border-bottom: 2px solid #1e40af;
      padding-bottom: 12px;
      margin-bottom: 16px;
    }
    .school-name {
      font-size: 18px;
      font-weight: bold;
      color: #1e40af;
      letter-spacing: 0.5px;
    }
    .school-sub {
      font-size: 10px;
      color: #64748b;
      margin-top: 2px;
    }
    .receipt-title {
      font-size: 13px;
      font-weight: bold;
      text-align: center;
      background: #1e40af;
      color: white;
      padding: 6px 0;
      margin-bottom: 14px;
      letter-spacing: 1px;
    }
    .row {
      display: flex;
      justify-content: space-between;
      padding: 5px 0;
      border-bottom: 1px dashed #e2e8f0;
    }
    .row:last-child { border-bottom: none; }
    .label { color: #64748b; width: 45%; }
    .value { font-weight: 600; width: 55%; text-align: right; }
    .amount-box {
      background: #f0fdf4;
      border: 1px solid #86efac;
      border-radius: 6px;
      padding: 10px 14px;
      margin-top: 14px;
      text-align: center;
    }
    .amount-label { font-size: 10px; color: #16a34a; }
    .amount-value { font-size: 22px; font-weight: bold; color: #15803d; }
    .footer {
      margin-top: 20px;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
    }
    .qr-placeholder {
      width: 60px; height: 60px;
      border: 1px solid #cbd5e1;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 8px;
      color: #94a3b8;
      text-align: center;
    }
    .signature {
      text-align: right;
      font-size: 10px;
      color: #64748b;
    }
    .sig-line {
      border-top: 1px solid #1e293b;
      margin-top: 30px;
      margin-bottom: 4px;
      width: 120px;
      margin-left: auto;
    }
    .watermark {
      text-align: center;
      margin-top: 14px;
      font-size: 9px;
      color: #94a3b8;
    }
    .status-paid {
      display: inline-block;
      background: #dcfce7;
      color: #16a34a;
      border: 1px solid #86efac;
      border-radius: 4px;
      padding: 2px 8px;
      font-size: 10px;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <!-- School Header -->
  <div class="header">
    <div class="school-name">{{ config('school.name', config('app.name')) }}</div>
    <div class="school-sub">
      {{ config('school.district', '') }}
      &nbsp;|&nbsp; EMIS Code: {{ config('school.emis_code', '') }}
    </div>
    <div class="school-sub">
      Academic Year: {{ $payment->feeStructure->academicYear->name }}
    </div>
  </div>

  <!-- Receipt Title -->
  <div class="receipt-title">FEE RECEIPT</div>

  <!-- Receipt + Status row -->
  <div style="display:flex;justify-content:space-between;margin-bottom:12px;">
    <div>
      <span style="color:#64748b;font-size:10px">Receipt No:</span>
      <strong style="font-size:12px;color:#1e40af"> {{ $payment->receipt_no }}</strong>
    </div>
    <span class="status-paid">✓ PAID</span>
  </div>

  <!-- Student Details -->
  <div class="row">
    <span class="label">Student Name</span>
    <span class="value">{{ $payment->student->name }}</span>
  </div>
  <div class="row">
    <span class="label">Father's Name</span>
    <span class="value">{{ $payment->student->father_name }}</span>
  </div>
  <div class="row">
    <span class="label">Admission No.</span>
    <span class="value">{{ $payment->student->admission_no }}</span>
  </div>
  <div class="row">
    <span class="label">Class / Section</span>
    <span class="value">Class {{ $payment->student->schoolClass->name }} {{ $payment->student->section?->name }}</span>
  </div>

  <div style="border-top:1px solid #e2e8f0;margin:10px 0"></div>

  <!-- Payment Details -->
  <div class="row">
    <span class="label">Fee Head</span>
    <span class="value">{{ $payment->feeStructure->fee_head }}</span>
  </div>
  <div class="row">
    <span class="label">Term</span>
    <span class="value">{{ ucfirst(str_replace('_',' ',$payment->feeStructure->term)) }}</span>
  </div>
  <div class="row">
    <span class="label">Payment Date</span>
    <span class="value">{{ $payment->payment_date?->format('d M Y') }}</span>
  </div>
  <div class="row">
    <span class="label">Payment Mode</span>
    <span class="value">{{ strtoupper($payment->payment_mode) }}</span>
  </div>
  @if($payment->transaction_id)
  <div class="row">
    <span class="label">Transaction ID</span>
    <span class="value" style="font-size:9px">{{ $payment->transaction_id }}</span>
  </div>
  @endif
  @if($payment->discount > 0)
  <div class="row">
    <span class="label">Discount</span>
    <span class="value" style="color:#16a34a">- ₹{{ number_format($payment->discount) }}</span>
  </div>
  @endif
  @if($payment->fine > 0)
  <div class="row">
    <span class="label">Fine</span>
    <span class="value" style="color:#dc2626">+ ₹{{ number_format($payment->fine) }}</span>
  </div>
  @endif
  @if($payment->collectedBy)
  <div class="row">
    <span class="label">Collected By</span>
    <span class="value">{{ $payment->collectedBy->name }}</span>
  </div>
  @endif

  <!-- Total Amount -->
  <div class="amount-box">
    <div class="amount-label">AMOUNT PAID</div>
    <div class="amount-value">₹{{ number_format($payment->amount_paid, 2) }}</div>
  </div>

  <!-- Footer -->
  <div class="footer">
    <div class="qr-placeholder">QR<br>Code</div>
    <div class="signature">
      <div class="sig-line"></div>
      Authorised Signature
    </div>
  </div>

  <div class="watermark">
    This is a computer-generated receipt. No signature required if paid online. |
    Generated: {{ now()->format('d M Y h:i A') }}
  </div>

</body>
</html>