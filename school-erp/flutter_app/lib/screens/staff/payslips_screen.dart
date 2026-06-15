import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../core/services/staff_service.dart';
import '../../core/utils/app_theme.dart';

class PayslipsScreen extends StatefulWidget {
  const PayslipsScreen({super.key});

  @override
  State<PayslipsScreen> createState() => _PayslipsScreenState();
}

class _PayslipsScreenState extends State<PayslipsScreen> {
  final _service = StaffService();
  List<dynamic> _payslips = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final r = await _service.getPayslips();
      if (r['success'] == true) setState(() => _payslips = r['data'] ?? []);
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _openPayslip(String url) async {
    final uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Cannot open payslip PDF.')));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('My Payslips')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _payslips.isEmpty
              ? Center(child: Text('No payslips found.', style: AppTheme.bodySmall))
              : ListView.separated(
                  padding: const EdgeInsets.all(16),
                  itemCount: _payslips.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 8),
                  itemBuilder: (_, i) {
                    final p = _payslips[i] as Map<String, dynamic>;
                    final status = p['status'] as String? ?? '';
                    final isPaid = status == 'paid';

                    return Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          children: [
                            Row(
                              children: [
                                CircleAvatar(
                                  backgroundColor: AppTheme.primary.withOpacity(.1),
                                  child: const Icon(Icons.receipt_long_rounded,
                                      color: AppTheme.primary, size: 20),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(p['month'] ?? '', style: AppTheme.labelBold),
                                      Text(
                                        '${p['present_days']} / ${p['working_days']} days',
                                        style: AppTheme.bodySmall,
                                      ),
                                    ],
                                  ),
                                ),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                  decoration: BoxDecoration(
                                    color: (isPaid ? AppTheme.success : AppTheme.warning).withOpacity(.1),
                                    borderRadius: BorderRadius.circular(20),
                                  ),
                                  child: Text(
                                    status.toUpperCase(),
                                    style: TextStyle(
                                      fontSize: 10,
                                      fontWeight: FontWeight.bold,
                                      color: isPaid ? AppTheme.success : AppTheme.warning,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 12),
                            const Divider(height: 1),
                            const SizedBox(height: 12),
                            Row(
                              children: [
                                _salaryCol('Gross', p['gross_salary']),
                                _salaryCol('Deductions', p['total_deduction'], color: AppTheme.error),
                                _salaryCol('Net Pay', p['net_salary'], color: AppTheme.success),
                              ],
                            ),
                            if (isPaid && p['payslip_url'] != null) ...[
                              const SizedBox(height: 12),
                              SizedBox(
                                width: double.infinity,
                                child: OutlinedButton.icon(
                                  onPressed: () => _openPayslip(p['payslip_url']),
                                  icon: const Icon(Icons.picture_as_pdf_rounded, size: 16),
                                  label: const Text('View PDF Payslip'),
                                  style: OutlinedButton.styleFrom(
                                    foregroundColor: AppTheme.primary,
                                    side: const BorderSide(color: AppTheme.primary),
                                    shape: RoundedRectangleBorder(
                                        borderRadius: BorderRadius.circular(10)),
                                  ),
                                ),
                              ),
                            ],
                          ],
                        ),
                      ),
                    );
                  },
                ),
    );
  }

  Widget _salaryCol(String label, dynamic amount, {Color? color}) {
    final val = double.tryParse(amount?.toString() ?? '0') ?? 0;
    return Expanded(
      child: Column(
        children: [
          Text('₹${val.toStringAsFixed(0)}',
              style: TextStyle(
                  fontWeight: FontWeight.bold,
                  fontSize: 15,
                  color: color ?? AppTheme.textPrimary)),
          Text(label, style: AppTheme.bodySmall),
        ],
      ),
    );
  }
}
