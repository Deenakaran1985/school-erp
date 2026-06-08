<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Payslip</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'DejaVu Sans',sans-serif; font-size:11px; color:#1e293b; padding:30px 36px; }
    .header { display:flex; justify-content:space-between; align-items:flex-start; border-bottom:3px solid #7c3aed; padding-bottom:14px; margin-bottom:18px; }
    .school-name { font-size:17px; font-weight:bold; color:#7c3aed; }
    .school-sub  { font-size:9px; color:#64748b; margin-top:2px; }
    .badge { background:#7c3aed; color:#fff; padding:4px 14px; border-radius:4px; font-size:10px; font-weight:bold; letter-spacing:1px; }
    .month-label { font-size:11px; color:#64748b; margin-top:4px; text-align:right; }
    section { margin-bottom:16px; }
    .section-title { font-size:10px; font-weight:bold; letter-spacing:.06em; text-transform:uppercase; color:#7c3aed; border-bottom:1px solid #ede9fe; padding-bottom:4px; margin-bottom:8px; }
    .row { display:flex; padding:5px 0; border-bottom:1px dashed #f1f5f9; }
    .row:last-child { border-bottom:none; }
    .lbl { color:#64748b; width:50%; }
    .val { font-weight:600; width:50%; text-align:right; }
    table { width:100%; border-collapse:collapse; }
    table th { background:#f8f7ff; color:#7c3aed; font-size:10px; padding:6px 10px; text-align:left; border:1px solid #e8e4fd; }
    table td { padding:5px 10px; border:1px solid #f1f5f9; font-size:11px; }
    .earn  { color:#16a34a; }
    .deduct{ color:#dc2626; }
    .net-box { background:#f0fdf4; border:2px solid #86efac; padding:12px 16px; text-align:center; margin:14px 0; border-radius:6px; }
    .net-label { font-size:10px; color:#16a34a; }
    .net-value { font-size:24px; font-weight:bold; color:#15803d; }
    .footer-note { font-size:9px; color:#94a3b8; text-align:center; margin-top:20px; }
    .sig-area { display:flex; justify-content:space-between; margin-top:30px; }
    .sig-box { text-align:center; }
    .sig-line { border-top:1px solid #1e293b; width:120px; margin:0 auto 4px; }
    .sig-label { font-size:9px; color:#64748b; }
  </style>
</head>
<body>

<!-- Header -->
<div class="header">
  <div>
    <div class="school-name">{{ config('school.name', config('app.name')) }}</div>
    <div class="school-sub">{{ config('school.district','') }} · EMIS: {{ config('school.emis_code','') }}</div>
    <div class="school-sub">Academic Year: {{ $payroll->academicYear->name }}</div>
  </div>
  <div style="text-align:right">
    <div class="badge">PAYSLIP</div>
    <div class="month-label">
      {{ \Carbon\Carbon::createFromFormat('Y-m', $payroll->month_year)->format('F Y') }}
    </div>
  </div>
</div>

<!-- Employee Details -->
<section>
  <div class="section-title">Employee Details</div>
  <table>
    <tr>
      <th>Employee Name</th><td>{{ $payroll->staff->name }}</td>
      <th>Employee ID</th><td>{{ $payroll->staff->employee_id }}</td>
    </tr>
    <tr>
      <th>Designation</th><td>{{ $payroll->staff->designation }}</td>
      <th>Department</th><td>{{ $payroll->staff->department?->name ?? '—' }}</td>
    </tr>
    <tr>
      <th>PAN No.</th><td>{{ $payroll->staff->pan_number ?? '—' }}</td>
      <th>Bank Account</th><td>{{ $payroll->staff->bank_account ?? '—' }}</td>
    </tr>
    <tr>
      <th>Working Days</th><td>{{ $payroll->working_days }}</td>
      <th>Present Days</th><td>{{ $payroll->present_days }}</td>
    </tr>
  </table>
</section>

<!-- Earnings vs Deductions -->
<section>
  <div class="section-title">Salary Details</div>
  <table>
    <tr>
      <th style="width:30%;color:#16a34a">Earnings</th>
      <th style="width:20%">Amount (₹)</th>
      <th style="width:30%;color:#dc2626">Deductions</th>
      <th style="width:20%">Amount (₹)</th>
    </tr>
    <tr>
      <td>Basic Salary</td>
      <td class="earn">{{ number_format($payroll->basic_salary,2) }}</td>
      <td>Provident Fund ({{ $payroll->staff->pf_percent }}%)</td>
      <td class="deduct">{{ number_format($payroll->pf_deduction,2) }}</td>
    </tr>
    <tr>
      <td>Dearness Allow. ({{ $payroll->staff->da_percent }}%)</td>
      <td class="earn">{{ number_format($payroll->da_amount,2) }}</td>
      <td>ESI</td>
      <td class="deduct">{{ number_format($payroll->esi_deduction,2) }}</td>
    </tr>
    <tr>
      <td>House Rent Allow. ({{ $payroll->staff->hra_percent }}%)</td>
      <td class="earn">{{ number_format($payroll->hra_amount,2) }}</td>
      <td>TDS</td>
      <td class="deduct">{{ number_format($payroll->tds_deduction,2) }}</td>
    </tr>
    <tr>
      <td>Other Allowance</td>
      <td class="earn">{{ number_format($payroll->other_allowance,2) }}</td>
      <td>Loan Deduction</td>
      <td class="deduct">{{ number_format($payroll->loan_deduction,2) }}</td>
    </tr>
    <tr>
      <td></td><td></td>
      <td>Other Deductions</td>
      <td class="deduct">{{ number_format($payroll->other_deduction,2) }}</td>
    </tr>
    <tr style="font-weight:bold;background:#f8fafc">
      <td>Gross Salary</td>
      <td class="earn">{{ number_format($payroll->gross_salary,2) }}</td>
      <td>Total Deductions</td>
      <td class="deduct">{{ number_format($payroll->total_deduction,2) }}</td>
    </tr>
  </table>
</section>

<!-- Net Salary -->
<div class="net-box">
  <div class="net-label">NET SALARY PAYABLE</div>
  <div class="net-value">₹ {{ number_format($payroll->net_salary, 2) }}</div>
  @if($payroll->remarks)
    <div style="font-size:9px;color:#64748b;margin-top:4px">Note: {{ $payroll->remarks }}</div>
  @endif
</div>

<!-- Signatures -->
<div class="sig-area">
  <div class="sig-box">
    <div class="sig-line"></div>
    <div class="sig-label">Employee Signature</div>
  </div>
  <div class="sig-box">
    <div class="sig-line"></div>
    <div class="sig-label">Accounts Department</div>
  </div>
  <div class="sig-box">
    <div class="sig-line"></div>
    <div class="sig-label">Principal / Authorised Signatory</div>
  </div>
</div>

<div class="footer-note">
  This is a computer-generated payslip. Generated on {{ now()->format('d M Y h:i A') }} |
  {{ config('school.name') }} · Confidential
</div>

</body>
</html>