import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/services/correspondent_service.dart';
import '../../core/utils/app_theme.dart';
import '../../widgets/stat_card.dart';

class FeeSummaryScreen extends StatefulWidget {
  const FeeSummaryScreen({super.key});

  @override
  State<FeeSummaryScreen> createState() => _FeeSummaryScreenState();
}

class _FeeSummaryScreenState extends State<FeeSummaryScreen> {
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
      final r = await _service.getFeeSummary(month: _month);
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

  @override
  Widget build(BuildContext context) {
    final collected = (_data?['collected'] as List<dynamic>?) ?? [];
    final total     = _data?['total_collected'];

    return Scaffold(
      appBar: AppBar(
        title: const Text('Fee Summary'),
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

                  // Total collected card
                  Card(
                    color: AppTheme.primary.withOpacity(.08),
                    child: Padding(
                      padding: const EdgeInsets.all(20),
                      child: Column(children: [
                        const Icon(Icons.payments_rounded,
                            color: AppTheme.primary, size: 32),
                        const SizedBox(height: 8),
                        Text(
                          '₹${_fmt(total)}',
                          style: const TextStyle(
                              fontSize: 28,
                              fontWeight: FontWeight.bold,
                              color: AppTheme.primary),
                        ),
                        Text('Total Collected', style: AppTheme.bodySmall),
                      ]),
                    ),
                  ),
                  const SizedBox(height: 16),

                  // By mode
                  if (collected.isNotEmpty) ...[
                    Text('By Payment Mode', style: AppTheme.labelBold),
                    const SizedBox(height: 8),
                    ...collected.map((c) {
                      final item = c as Map<String, dynamic>;
                      return Card(
                        margin: const EdgeInsets.only(bottom: 8),
                        child: ListTile(
                          leading: CircleAvatar(
                            backgroundColor: AppTheme.success.withOpacity(.1),
                            child: const Icon(Icons.payment_rounded,
                                color: AppTheme.success, size: 20),
                          ),
                          title: Text(
                            (item['mode'] as String).toUpperCase().replaceAll('_', ' '),
                            style: AppTheme.labelBold,
                          ),
                          subtitle: Text('${item['count']} transaction(s)',
                              style: AppTheme.bodySmall),
                          trailing: Text(
                            '₹${_fmt(item['total'])}',
                            style: const TextStyle(
                                fontWeight: FontWeight.bold,
                                fontSize: 16,
                                color: AppTheme.success),
                          ),
                        ),
                      );
                    }),
                  ] else
                    Center(
                      child: Padding(
                        padding: const EdgeInsets.all(32),
                        child: Text('No collections this month.',
                            style: AppTheme.bodySmall),
                      ),
                    ),
                ],
              ),
            ),
    );
  }

  String _fmt(dynamic v) {
    final n = double.tryParse(v?.toString() ?? '0') ?? 0;
    return NumberFormat('#,##,##0', 'en_IN').format(n);
  }
}
