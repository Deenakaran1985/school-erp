import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/services/student_service.dart';
import '../../core/utils/app_theme.dart';

class AttendanceScreen extends StatefulWidget {
  const AttendanceScreen({super.key});

  @override
  State<AttendanceScreen> createState() => _AttendanceScreenState();
}

class _AttendanceScreenState extends State<AttendanceScreen> {
  final _service = StudentService();

  int?   _studentId;
  String _month = DateFormat('yyyy-MM').format(DateTime.now());

  Map<String, dynamic> _summary = {};
  List<dynamic>        _records = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _init();
  }

  Future<void> _init() async {
    try {
      final p = await _service.getProfile();
      if (p['success'] == true) {
        final data = p['data'];
        final s = data is List ? data[0] : data;
        _studentId = s['id'];
        await _loadAttendance();
      }
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _loadAttendance() async {
    if (_studentId == null) return;
    setState(() => _loading = true);
    try {
      final r = await _service.getAttendance(_studentId!, _month);
      if (r['success'] == true) {
        setState(() {
          _summary = r['summary'] ?? {};
          _records = r['records'] ?? [];
        });
      }
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _pickMonth() async {
    final now    = DateTime.now();
    final parsed = DateTime.parse('$_month-01');
    final picked = await showDatePicker(
      context: context,
      initialDate: parsed,
      firstDate: DateTime(now.year - 1),
      lastDate: now,
      initialDatePickerMode: DatePickerMode.year,
    );
    if (picked != null) {
      setState(() => _month = DateFormat('yyyy-MM').format(picked));
      _loadAttendance();
    }
  }

  Color _statusColor(String status) => switch (status) {
    'present' => AppTheme.success,
    'absent'  => AppTheme.error,
    'late'    => AppTheme.warning,
    'holiday' => AppTheme.primary,
    _         => AppTheme.textSecondary,
  };

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Attendance'),
        actions: [
          IconButton(
            icon: const Icon(Icons.calendar_month_rounded),
            onPressed: _pickMonth,
            tooltip: 'Change month',
          ),
        ],
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadAttendance,
              child: ListView(
                padding: const EdgeInsets.all(16),
                children: [
                  // Month Chip
                  Center(
                    child: ActionChip(
                      avatar: const Icon(Icons.calendar_today_rounded, size: 16),
                      label: Text(DateFormat('MMMM yyyy').format(DateTime.parse('$_month-01'))),
                      onPressed: _pickMonth,
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Summary row
                  Row(
                    children: [
                      _summaryCard('Present', _summary['present'] ?? 0, AppTheme.success),
                      _summaryCard('Absent',  _summary['absent']  ?? 0, AppTheme.error),
                      _summaryCard('Late',    _summary['late']    ?? 0, AppTheme.warning),
                      _summaryCard('Holiday', _summary['holiday'] ?? 0, AppTheme.primary),
                    ],
                  ),
                  const SizedBox(height: 16),

                  // Percentage bar
                  _buildPctBar(),
                  const SizedBox(height: 16),

                  // Daily records
                  Card(
                    clipBehavior: Clip.antiAlias,
                    child: Column(
                      children: _records.isEmpty
                          ? [const ListTile(title: Center(child: Text('No records this month.')))]
                          : _records.map((r) {
                              final rec    = r as Map<String, dynamic>;
                              final date   = DateTime.parse(rec['date']);
                              final status = rec['status'] as String;
                              return ListTile(
                                dense: true,
                                leading: CircleAvatar(
                                  radius: 18,
                                  backgroundColor: _statusColor(status).withOpacity(.1),
                                  child: Text(
                                    DateFormat('d').format(date),
                                    style: TextStyle(
                                      fontSize: 13,
                                      fontWeight: FontWeight.bold,
                                      color: _statusColor(status),
                                    ),
                                  ),
                                ),
                                title: Text(
                                  DateFormat('EEE, d MMM').format(date),
                                  style: AppTheme.bodyMedium,
                                ),
                                subtitle: rec['remarks'] != null
                                    ? Text(rec['remarks'], style: AppTheme.bodySmall)
                                    : null,
                                trailing: Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                  decoration: BoxDecoration(
                                    color: _statusColor(status).withOpacity(.1),
                                    borderRadius: BorderRadius.circular(20),
                                  ),
                                  child: Text(
                                    status[0].toUpperCase() + status.substring(1),
                                    style: TextStyle(
                                      fontSize: 11,
                                      fontWeight: FontWeight.w600,
                                      color: _statusColor(status),
                                    ),
                                  ),
                                ),
                              );
                            }).toList(),
                    ),
                  ),
                ],
              ),
            ),
    );
  }

  Widget _summaryCard(String label, int count, Color color) {
    return Expanded(
      child: Card(
        margin: const EdgeInsets.symmetric(horizontal: 3),
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 12),
          child: Column(
            children: [
              Text('$count', style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: color)),
              Text(label, style: AppTheme.bodySmall),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildPctBar() {
    final present = (_summary['present'] ?? 0) as int;
    final total   = _records.length;
    final pct     = total > 0 ? present / total : 0.0;
    final color   = pct >= .75 ? AppTheme.success : pct >= .60 ? AppTheme.warning : AppTheme.error;

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('Attendance %', style: AppTheme.labelBold),
                Text('${(pct * 100).toStringAsFixed(1)}%',
                    style: TextStyle(fontWeight: FontWeight.bold, color: color)),
              ],
            ),
            const SizedBox(height: 8),
            ClipRRect(
              borderRadius: BorderRadius.circular(8),
              child: LinearProgressIndicator(
                value: pct,
                minHeight: 10,
                backgroundColor: color.withOpacity(.1),
                valueColor: AlwaysStoppedAnimation<Color>(color),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
