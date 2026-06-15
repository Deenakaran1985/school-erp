import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/services/correspondent_service.dart';
import '../../core/utils/app_theme.dart';

class PayrollSummaryScreen extends StatefulWidget {
  const PayrollSummaryScreen({super.key});

  @override
  State<PayrollSummaryScreen> createState() => _PayrollSummaryScreenState();
}

class _PayrollSummaryScreenState extends State<PayrollSummaryScreen> {
  final _service = CorrespondentService();

  String _month = DateFormat('yyyy-MM').format(DateTime.now());
  Map<String, dynamic>? _data;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final r = await _service.getPayrollSummary(month: _month);
      if (r['success'] == true) setState(() => _data = r);
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _pickMonth() async {
    final parsed = DateTime.parse('$_month-01');
    final picked = await showDatePicker(
      context: context,
      initialDate: parsed,
      firstDate: DateTime(DateTime.now().year - 1),
      lastDate: DateTime.now(),
    );
    if (picked != null) {
      setState(() => _month = DateFormat('yyyy-MM').format(picked));
      _load();
    }
  }

  Color _statusColor(String s) => switch (s) {
    'paid'     => AppTheme.success,
    'approved' => AppTheme.secondary,
    'draft'    => AppTheme.warning,
    _          => AppTheme.textSecondary,
  };

  IconData _statusIcon(String s) => switch (s) {
    'paid'     => Icons.check_circle_rounded,
    'approved' => Icons.verified_rounded,
    'draft'    => Icons.pending_rounded,
    _          => Icons.help_rounded,
  };

  @override
  Widget build(BuildContext context) {
    final byDept   = (_data?['by_department'] as List<dynamic>?) ?? [];
    final summary  = (_data?['summary'] as Map<String, dynamic>?) ?? {};
    final totalNet = summary['total_net'];
    final paid     = summary['paid_count'] ?? 0;
    final approved = summary['approved_count'] ?? 0;
    final draft    = summary['draft_count'] ?? 0;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Payroll Summary'),
        actions: [
          IconButton(
              icon: const Icon(Icons.calendar_month_rounded),
              onPressed: _pickMonth),
        ],
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _load,
              child: ListView(
                padding: const EdgeInsets.all(16),
                children: [
                  // Month chip
                  Center(
                    child: ActionChip(
                      avatar: const Icon(Icons.calendar_today_rounded, size: 16),
                      label: Text(DateFormat('MMMM yyyy')
                          .format(DateTime.parse('$_month-01'))),
                      onPressed: _pickMonth,
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Total net card
                  Card(
                    color: AppTheme.secondary.withOpacity(.08),
                    child: Padding(
                      padding: const EdgeInsets.all(20),
                      child: Column(children: [
                        const Icon(Icons.account_balance_wallet_rounded,
                            color: AppTheme.secondary, size: 32),
                        const SizedBox(height: 8),
                        Text(
                          '₹${_fmt(totalNet)}',
                          style: const TextStyle(
                              fontSize: 28,
                              fontWeight: FontWeight.bold,
                              color: AppTheme.secondary),
                        ),
                        Text('Total Net Payroll', style: AppTheme.bodySmall),
                      ]),
                    ),
                  ),
                  const SizedBox(height: 12),

                  // Status summary row
                  Row(children: [
                    _statusChip('Draft',    draft,    AppTheme.warning),
                    const SizedBox(width: 8),
                    _statusChip('Approved', approved, AppTheme.secondary),
                    const SizedBox(width: 8),
                    _statusChip('Paid',     paid,     AppTheme.success),
                  ]),
                  const SizedBox(height: 20),

                  // By department
                  if (byDept.isNotEmpty) ...[
                    Text('By Department', style: AppTheme.labelBold),
                    const SizedBox(height: 8),
                    ...byDept.map((d) {
                      final dept = d as Map<String, dynamic>;
                      final statuses = (dept['statuses'] as List<dynamic>?) ?? [];
                      return Card(
                        margin: const EdgeInsets.only(bottom: 10),
                        child: Padding(
                          padding: const EdgeInsets.all(14),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(dept['department'] ?? '—',
                                      style: AppTheme.labelBold),
                                  Text('₹${_fmt(dept['net_total'])}',
                                      style: const TextStyle(
                                          fontWeight: FontWeight.bold,
                                          color: AppTheme.secondary,
                                          fontSize: 15)),
                                ],
                              ),
                              const SizedBox(height: 4),
                              Text('${dept['staff_count']} staff · Gross ₹${_fmt(dept['gross_total'])}',
                                  style: AppTheme.bodySmall),
                              if (statuses.isNotEmpty) ...[
                                const Divider(height: 16),
                                Wrap(
                                  spacing: 6,
                                  children: statuses.map((s) {
                                    final st = s as Map<String, dynamic>;
                                    final col = _statusColor(st['status'] ?? '');
                                    return Chip(
                                      avatar: Icon(_statusIcon(st['status'] ?? ''),
                                          size: 14, color: col),
                                      label: Text(
                                        '${st['status']}: ${st['count']}',
                                        style: TextStyle(fontSize: 11, color: col),
                                      ),
                                      backgroundColor: col.withOpacity(.08),
                                      visualDensity: VisualDensity.compact,
                                      padding: EdgeInsets.zero,
                                    );
                                  }).toList(),
                                ),
                              ],
                            ],
                          ),
                        ),
                      );
                    }),
                  ] else
                    Center(
                      child: Padding(
                        padding: const EdgeInsets.all(32),
                        child: Text('No payroll records this month.',
                            style: AppTheme.bodySmall),
                      ),
                    ),
                ],
              ),
            ),
    );
  }

  Widget _statusChip(String label, int count, Color color) => Expanded(
    child: Container(
      padding: const EdgeInsets.symmetric(vertical: 10),
      decoration: BoxDecoration(
        color: color.withOpacity(.08),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: color.withOpacity(.3)),
      ),
      child: Column(children: [
        Text('$count', style: TextStyle(
            fontSize: 20, fontWeight: FontWeight.bold, color: color)),
        Text(label, style: AppTheme.bodySmall.copyWith(fontSize: 11)),
      ]),
    ),
  );

  String _fmt(dynamic v) {
    final n = double.tryParse(v?.toString() ?? '0') ?? 0;
    return NumberFormat('#,##,##0', 'en_IN').format(n);
  }
}
