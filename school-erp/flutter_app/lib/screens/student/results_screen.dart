import 'package:flutter/material.dart';
import '../../core/services/student_service.dart';
import '../../core/utils/app_theme.dart';
import '../../widgets/section_header.dart';

class ResultsScreen extends StatefulWidget {
  const ResultsScreen({super.key});

  @override
  State<ResultsScreen> createState() => _ResultsScreenState();
}

class _ResultsScreenState extends State<ResultsScreen> {
  final _service = StudentService();

  Map<String, dynamic>? _data;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final r = await _service.getResults();
      if (r['success'] == true) setState(() => _data = r);
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  @override
  Widget build(BuildContext context) {
    final student = _data?['student'] as Map<String, dynamic>?;
    final groups  = (_data?['data'] as List<dynamic>?) ?? [];

    return Scaffold(
      appBar: AppBar(title: const Text('My Results')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : groups.isEmpty
              ? Center(child: Text('No results published yet.', style: AppTheme.bodySmall))
              : ListView(
                  padding: const EdgeInsets.all(16),
                  children: [
                    // Student Summary Card
                    if (student != null)
                      Card(
                        margin: const EdgeInsets.only(bottom: 16),
                        child: Padding(
                          padding: const EdgeInsets.all(16),
                          child: Row(children: [
                            CircleAvatar(
                              backgroundColor: AppTheme.primary.withOpacity(.1),
                              child: Text(student['name'][0],
                                style: const TextStyle(fontWeight: FontWeight.bold, color: AppTheme.primary)),
                            ),
                            const SizedBox(width: 12),
                            Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                              Text(student['name'] ?? '', style: AppTheme.labelBold),
                              Text('Class ${student['class']} · Roll ${student['roll_number']}',
                                style: AppTheme.bodySmall),
                            ]),
                          ]),
                        ),
                      ),

                    // Result groups by exam type
                    ...groups.map((g) => _buildGroup(g as Map<String, dynamic>)),
                  ],
                ),
    );
  }

  Widget _buildGroup(Map<String, dynamic> group) {
    final subjects = (group['subjects'] as List<dynamic>?) ?? [];
    final passed   = group['passed'] == true;
    final pct      = group['percentage'] as num? ?? 0;

    Color pctColor = pct >= 75
        ? AppTheme.success
        : pct >= 35
            ? AppTheme.warning
            : AppTheme.error;

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      clipBehavior: Clip.antiAlias,
      child: Column(
        children: [
          // Header
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            color: AppTheme.background,
            child: Row(
              children: [
                Text(group['exam_type'] ?? '', style: AppTheme.labelBold),
                const Spacer(),
                Text('${pct.toStringAsFixed(1)}%',
                    style: TextStyle(fontWeight: FontWeight.bold, color: pctColor, fontSize: 16)),
                const SizedBox(width: 8),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                  decoration: BoxDecoration(
                    color: passed ? AppTheme.success.withOpacity(.1) : AppTheme.error.withOpacity(.1),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    passed ? 'PASS' : 'FAIL',
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                      color: passed ? AppTheme.success : AppTheme.error,
                    ),
                  ),
                ),
              ],
            ),
          ),

          // Subject rows
          ...subjects.map((s) {
            final sub    = s as Map<String, dynamic>;
            final isPass = sub['passed'] == true;
            final absent = sub['absent'] == true;

            return ListTile(
              dense: true,
              title: Text(sub['subject'] ?? '', style: AppTheme.bodyMedium),
              subtitle: Text(sub['exam_date'] ?? '', style: AppTheme.bodySmall),
              trailing: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    absent ? 'AB' : '${sub['marks_obtained']}/${sub['max_marks']}',
                    style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14),
                  ),
                  const SizedBox(width: 8),
                  if (sub['grade'] != null)
                    Text(sub['grade'], style: const TextStyle(
                      color: AppTheme.primary, fontWeight: FontWeight.bold, fontSize: 14)),
                  const SizedBox(width: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                    decoration: BoxDecoration(
                      color: isPass
                          ? AppTheme.success.withOpacity(.1)
                          : AppTheme.error.withOpacity(.1),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      isPass ? 'Pass' : 'Fail',
                      style: TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.w600,
                        color: isPass ? AppTheme.success : AppTheme.error,
                      ),
                    ),
                  ),
                ],
              ),
            );
          }),
        ],
      ),
    );
  }
}
