import 'package:flutter/material.dart';
import '../../core/services/student_service.dart';
import '../../core/utils/app_theme.dart';

class ParentResultsScreen extends StatefulWidget {
  final int studentId;
  const ParentResultsScreen({super.key, required this.studentId});

  @override
  State<ParentResultsScreen> createState() => _ParentResultsScreenState();
}

class _ParentResultsScreenState extends State<ParentResultsScreen> {
  final _service = StudentService();

  List<dynamic> _results = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final r = await _service.getResults(studentId: widget.studentId);
      if (r['success'] == true) setState(() => _results = r['data'] ?? []);
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  Color _gradeColor(String? grade) => switch (grade) {
    'O' || 'A+' => AppTheme.success,
    'A'         => AppTheme.secondary,
    'B+' || 'B' => AppTheme.primary,
    'C'         => AppTheme.warning,
    _           => AppTheme.error,
  };

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Exam Results')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _results.isEmpty
              ? Center(
                  child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                    const Icon(Icons.bar_chart_rounded,
                        size: 60, color: AppTheme.textSecondary),
                    const SizedBox(height: 12),
                    Text('No results published yet.', style: AppTheme.bodySmall),
                  ]))
              : RefreshIndicator(
                  onRefresh: _load,
                  child: ListView.separated(
                    padding: const EdgeInsets.all(16),
                    itemCount: _results.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 12),
                    itemBuilder: (_, i) {
                      final exam     = _results[i] as Map<String, dynamic>;
                      final subjects = (exam['subjects'] as List<dynamic>?) ?? [];
                      final pct      = double.tryParse(exam['percentage']?.toString() ?? '0') ?? 0;

                      return Card(
                        child: Padding(
                          padding: const EdgeInsets.all(14),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              // Exam header
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Expanded(
                                    child: Text(exam['exam_name'] ?? '',
                                        style: AppTheme.labelBold),
                                  ),
                                  Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
                                    Text('${pct.toStringAsFixed(1)}%',
                                        style: TextStyle(
                                            fontWeight: FontWeight.bold,
                                            fontSize: 16,
                                            color: pct >= 50 ? AppTheme.success : AppTheme.error)),
                                    Text(exam['result'] == 'pass' ? 'PASS' : 'FAIL',
                                        style: TextStyle(
                                            fontSize: 10,
                                            fontWeight: FontWeight.w700,
                                            color: exam['result'] == 'pass'
                                                ? AppTheme.success
                                                : AppTheme.error)),
                                  ]),
                                ],
                              ),
                              const Divider(height: 16),
                              // Subject rows
                              ...subjects.map((sub) {
                                final s     = sub as Map<String, dynamic>;
                                final grade = s['grade'] as String?;
                                final isPas = (s['pass'] == true);
                                return Padding(
                                  padding: const EdgeInsets.symmetric(vertical: 4),
                                  child: Row(
                                    children: [
                                      Expanded(child: Text(s['subject'] ?? '',
                                          style: AppTheme.bodySmall)),
                                      Text('${s['marks_obtained']}/${s['max_marks']}',
                                          style: AppTheme.bodySmall),
                                      const SizedBox(width: 8),
                                      if (grade != null)
                                        Container(
                                          width: 32,
                                          alignment: Alignment.center,
                                          padding: const EdgeInsets.symmetric(
                                              vertical: 2),
                                          decoration: BoxDecoration(
                                            color: _gradeColor(grade).withOpacity(.12),
                                            borderRadius: BorderRadius.circular(6),
                                          ),
                                          child: Text(grade,
                                              style: TextStyle(
                                                  fontSize: 11,
                                                  fontWeight: FontWeight.bold,
                                                  color: _gradeColor(grade))),
                                        ),
                                      const SizedBox(width: 6),
                                      Icon(
                                        isPas ? Icons.check_circle_rounded : Icons.cancel_rounded,
                                        size: 14,
                                        color: isPas ? AppTheme.success : AppTheme.error,
                                      ),
                                    ],
                                  ),
                                );
                              }),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),
    );
  }
}
