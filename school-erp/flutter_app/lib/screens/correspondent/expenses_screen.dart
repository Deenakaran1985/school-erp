import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/services/correspondent_service.dart';
import '../../core/utils/app_theme.dart';

class ExpensesScreen extends StatefulWidget {
  const ExpensesScreen({super.key});

  @override
  State<ExpensesScreen> createState() => _ExpensesScreenState();
}

class _ExpensesScreenState extends State<ExpensesScreen> {
  final _service = CorrespondentService();

  String _status = 'all';
  List<dynamic> _expenses = [];
  Map<String, dynamic>? _totals;
  bool _loading = true;

  final _statuses = ['all', 'draft', 'approved', 'rejected'];

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final r = await _service.getExpenses(status: _status == 'all' ? null : _status);
      if (r['success'] == true) {
        setState(() {
          _expenses = r['data'] ?? [];
          _totals   = r['totals'] as Map<String, dynamic>?;
        });
      }
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  Color _statusColor(String s) => switch (s) {
    'approved' => AppTheme.success,
    'rejected' => AppTheme.error,
    'draft'    => AppTheme.warning,
    _          => AppTheme.textSecondary,
  };

  IconData _categoryIcon(String? cat) => switch (cat) {
    'utilities'    => Icons.electric_bolt_rounded,
    'maintenance'  => Icons.build_rounded,
    'stationery'   => Icons.description_rounded,
    'salary'       => Icons.payments_rounded,
    'transport'    => Icons.directions_bus_rounded,
    'events'       => Icons.event_rounded,
    'other'        => Icons.receipt_long_rounded,
    _              => Icons.receipt_long_rounded,
  };

  @override
  Widget build(BuildContext context) {
    final approved = double.tryParse(_totals?['approved']?.toString() ?? '0') ?? 0;
    final pending  = double.tryParse(_totals?['pending']?.toString() ?? '0') ?? 0;

    return Scaffold(
      appBar: AppBar(title: const Text('Expenses')),
      body: Column(
        children: [
          // Totals banner
          Container(
            margin: const EdgeInsets.all(16),
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [AppTheme.error, Color(0xFFFF6B6B)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                _totalTile('Approved', approved, Colors.white),
                Container(width: 1, height: 40, color: Colors.white30),
                _totalTile('Pending', pending, Colors.white70),
              ],
            ),
          ),

          // Status filter
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: Row(
              children: _statuses.map((s) => Padding(
                padding: const EdgeInsets.only(right: 8),
                child: ChoiceChip(
                  label: Text(s[0].toUpperCase() + s.substring(1)),
                  selected: _status == s,
                  onSelected: (_) { setState(() => _status = s); _load(); },
                  selectedColor: AppTheme.primary.withOpacity(.15),
                  labelStyle: TextStyle(
                    color: _status == s ? AppTheme.primary : AppTheme.textSecondary,
                    fontWeight: _status == s ? FontWeight.bold : FontWeight.normal,
                    fontSize: 12,
                  ),
                ),
              )).toList(),
            ),
          ),
          const SizedBox(height: 8),

          // List
          Expanded(
            child: _loading
                ? const Center(child: CircularProgressIndicator())
                : RefreshIndicator(
                    onRefresh: _load,
                    child: _expenses.isEmpty
                        ? Center(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                const Icon(Icons.receipt_long_rounded,
                                    size: 56, color: AppTheme.textSecondary),
                                const SizedBox(height: 12),
                                Text('No expenses found.', style: AppTheme.bodySmall),
                              ],
                            ))
                        : ListView.separated(
                            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                            itemCount: _expenses.length,
                            separatorBuilder: (_, __) => const SizedBox(height: 8),
                            itemBuilder: (_, i) {
                              final e      = _expenses[i] as Map<String, dynamic>;
                              final status = e['status'] as String? ?? 'draft';
                              final color  = _statusColor(status);
                              final cat    = e['category'] as String?;

                              return Card(
                                child: Padding(
                                  padding: const EdgeInsets.all(14),
                                  child: Row(
                                    children: [
                                      CircleAvatar(
                                        radius: 22,
                                        backgroundColor: AppTheme.primary.withOpacity(.08),
                                        child: Icon(_categoryIcon(cat),
                                            color: AppTheme.primary, size: 20),
                                      ),
                                      const SizedBox(width: 12),
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Text(e['title'] ?? e['description'] ?? '',
                                                style: AppTheme.labelBold,
                                                maxLines: 1,
                                                overflow: TextOverflow.ellipsis),
                                            const SizedBox(height: 2),
                                            Text(
                                              '${cat ?? 'Other'} · ${_fmtDate(e['date'])}',
                                              style: AppTheme.bodySmall,
                                            ),
                                            if (e['approved_by'] != null) ...[
                                              const SizedBox(height: 2),
                                              Text('By: ${e['approved_by']}',
                                                  style: AppTheme.bodySmall
                                                      .copyWith(fontSize: 10)),
                                            ],
                                          ],
                                        ),
                                      ),
                                      const SizedBox(width: 8),
                                      Column(
                                        crossAxisAlignment: CrossAxisAlignment.end,
                                        children: [
                                          Text(
                                            '₹${_fmt(e['amount'])}',
                                            style: const TextStyle(
                                                fontWeight: FontWeight.bold,
                                                fontSize: 15),
                                          ),
                                          const SizedBox(height: 4),
                                          Container(
                                            padding: const EdgeInsets.symmetric(
                                                horizontal: 8, vertical: 2),
                                            decoration: BoxDecoration(
                                              color: color.withOpacity(.1),
                                              borderRadius: BorderRadius.circular(20),
                                            ),
                                            child: Text(status,
                                                style: TextStyle(
                                                    fontSize: 10,
                                                    color: color,
                                                    fontWeight: FontWeight.w600)),
                                          ),
                                        ],
                                      ),
                                    ],
                                  ),
                                ),
                              );
                            },
                          ),
                  ),
          ),
        ],
      ),
    );
  }

  Widget _totalTile(String label, double amount, Color textColor) => Column(
    children: [
      Text(label, style: TextStyle(color: textColor, fontSize: 12)),
      const SizedBox(height: 4),
      Text('₹${_fmt(amount)}',
          style: TextStyle(
              color: textColor, fontWeight: FontWeight.bold, fontSize: 17)),
    ],
  );

  String _fmt(dynamic v) {
    final n = double.tryParse(v?.toString() ?? '0') ?? 0;
    return NumberFormat('#,##,##0', 'en_IN').format(n);
  }

  String _fmtDate(dynamic d) {
    try { return DateFormat('d MMM y').format(DateTime.parse(d.toString())); }
    catch (_) { return d?.toString() ?? '—'; }
  }
}
