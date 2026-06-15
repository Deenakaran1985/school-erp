import 'package:flutter/material.dart';
import '../../core/services/correspondent_service.dart';
import '../../core/utils/app_theme.dart';

class StaffListScreen extends StatefulWidget {
  const StaffListScreen({super.key});

  @override
  State<StaffListScreen> createState() => _StaffListScreenState();
}

class _StaffListScreenState extends State<StaffListScreen> {
  final _service = CorrespondentService();
  final _search  = TextEditingController();

  List<dynamic> _all      = [];
  List<dynamic> _filtered = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
    _search.addListener(_filter);
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final r = await _service.getStaff();
      if (r['success'] == true) {
        setState(() {
          _all      = r['data'] ?? [];
          _filtered = List.from(_all);
        });
      }
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  void _filter() {
    final q = _search.text.toLowerCase();
    setState(() {
      _filtered = _all.where((s) {
        final staff = s as Map<String, dynamic>;
        return staff['name'].toString().toLowerCase().contains(q) ||
            staff['employee_id'].toString().toLowerCase().contains(q) ||
            (staff['department'] ?? '').toString().toLowerCase().contains(q);
      }).toList();
    });
  }

  @override
  void dispose() {
    _search.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Staff List')),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: TextField(
              controller: _search,
              decoration: AppTheme.inputDecoration(
                  label: 'Search name / ID / dept', icon: Icons.search_rounded),
            ),
          ),
          Expanded(
            child: _loading
                ? const Center(child: CircularProgressIndicator())
                : RefreshIndicator(
                    onRefresh: _load,
                    child: _filtered.isEmpty
                        ? Center(
                            child: Text('No staff found.', style: AppTheme.bodySmall))
                        : ListView.separated(
                            padding: const EdgeInsets.symmetric(horizontal: 16),
                            itemCount: _filtered.length,
                            separatorBuilder: (_, __) => const Divider(height: 1),
                            itemBuilder: (_, i) {
                              final s = _filtered[i] as Map<String, dynamic>;
                              return ListTile(
                                leading: CircleAvatar(
                                  backgroundColor: AppTheme.secondary.withOpacity(.1),
                                  child: Text(
                                    s['name'][0],
                                    style: const TextStyle(
                                        fontWeight: FontWeight.bold,
                                        color: AppTheme.secondary),
                                  ),
                                ),
                                title: Text(s['name'] ?? '', style: AppTheme.labelBold),
                                subtitle: Text(
                                  '${s['designation']} · ${s['department'] ?? '—'}',
                                  style: AppTheme.bodySmall,
                                ),
                                trailing: Column(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  crossAxisAlignment: CrossAxisAlignment.end,
                                  children: [
                                    Text(s['employee_id'] ?? '',
                                        style: AppTheme.bodySmall),
                                    Container(
                                      padding: const EdgeInsets.symmetric(
                                          horizontal: 8, vertical: 2),
                                      decoration: BoxDecoration(
                                        color: (s['status'] == 'active'
                                                ? AppTheme.success
                                                : AppTheme.error)
                                            .withOpacity(.1),
                                        borderRadius: BorderRadius.circular(20),
                                      ),
                                      child: Text(
                                        s['status'] ?? '',
                                        style: TextStyle(
                                          fontSize: 10,
                                          color: s['status'] == 'active'
                                              ? AppTheme.success
                                              : AppTheme.error,
                                          fontWeight: FontWeight.w600,
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              );
                            },
                          ),
                  ),
          ),
          Padding(
            padding: const EdgeInsets.all(12),
            child: Text(
              '${_filtered.length} staff member(s)',
              style: AppTheme.bodySmall,
            ),
          ),
        ],
      ),
    );
  }
}
