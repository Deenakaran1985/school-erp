import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/services/student_service.dart';
import '../../core/utils/app_theme.dart';

class HomeworkScreen extends StatefulWidget {
  const HomeworkScreen({super.key});

  @override
  State<HomeworkScreen> createState() => _HomeworkScreenState();
}

class _HomeworkScreenState extends State<HomeworkScreen> with SingleTickerProviderStateMixin {
  final _service = StudentService();
  late TabController _tabs;

  List<dynamic> _all       = [];
  List<dynamic> _pending   = [];
  List<dynamic> _submitted = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _tabs = TabController(length: 3, vsync: this);
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final p = await _service.getProfile();
      if (p['success'] == true) {
        final data = p['data'];
        final s = data is List ? data[0] : data;
        final hw = await _service.getHomework(s['id']);
        if (hw['success'] == true) {
          final list = (hw['data'] as List<dynamic>?) ?? [];
          setState(() {
            _all       = list;
            _pending   = list.where((h) => h['submitted'] == false && h['overdue'] == false).toList();
            _submitted = list.where((h) => h['submitted'] == true).toList();
          });
        }
      }
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  @override
  void dispose() {
    _tabs.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Homework'),
        bottom: TabBar(
          controller: _tabs,
          tabs: [
            Tab(text: 'All (${_all.length})'),
            Tab(text: 'Pending (${_pending.length})'),
            Tab(text: 'Done (${_submitted.length})'),
          ],
        ),
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : TabBarView(
              controller: _tabs,
              children: [
                _buildList(_all),
                _buildList(_pending),
                _buildList(_submitted),
              ],
            ),
    );
  }

  Widget _buildList(List<dynamic> items) {
    if (items.isEmpty) {
      return Center(
        child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
          const Icon(Icons.check_circle_outline, size: 60, color: AppTheme.success),
          const SizedBox(height: 12),
          Text('Nothing here!', style: AppTheme.bodySmall),
        ]),
      );
    }

    return RefreshIndicator(
      onRefresh: _load,
      child: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: items.length,
        separatorBuilder: (_, __) => const SizedBox(height: 8),
        itemBuilder: (_, i) => _buildCard(items[i] as Map<String, dynamic>),
      ),
    );
  }

  Widget _buildCard(Map<String, dynamic> hw) {
    final submitted = hw['submitted'] == true;
    final overdue   = hw['overdue']   == true;

    Color badgeColor = submitted
        ? AppTheme.success
        : overdue
            ? AppTheme.error
            : AppTheme.warning;

    String badgeText = submitted ? 'Submitted' : overdue ? 'Overdue' : 'Pending';

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(hw['title'] ?? '', style: AppTheme.labelBold),
                      const SizedBox(height: 2),
                      Row(children: [
                        const Icon(Icons.book_outlined, size: 14, color: AppTheme.textSecondary),
                        const SizedBox(width: 4),
                        Text(hw['subject'] ?? '', style: AppTheme.bodySmall),
                      ]),
                    ],
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: badgeColor.withOpacity(.1),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(badgeText,
                    style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: badgeColor)),
                ),
              ],
            ),
            if (hw['description'] != null && (hw['description'] as String).isNotEmpty) ...[
              const SizedBox(height: 8),
              Text(hw['description'], style: AppTheme.bodySmall),
            ],
            const SizedBox(height: 10),
            Row(children: [
              const Icon(Icons.event_rounded, size: 14, color: AppTheme.textSecondary),
              const SizedBox(width: 4),
              Text(
                'Due: ${_formatDate(hw['due_date'])}',
                style: AppTheme.bodySmall.copyWith(
                  color: overdue ? AppTheme.error : AppTheme.textSecondary,
                  fontWeight: overdue ? FontWeight.w600 : FontWeight.normal,
                ),
              ),
            ]),
          ],
        ),
      ),
    );
  }

  String _formatDate(String? iso) {
    if (iso == null) return '—';
    try {
      return DateFormat('d MMM yyyy').format(DateTime.parse(iso));
    } catch (_) {
      return iso;
    }
  }
}
