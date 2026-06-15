import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/services/staff_service.dart';
import '../../core/utils/app_theme.dart';

class MarkAttendanceScreen extends StatefulWidget {
  final Map<String, dynamic>? classData;
  const MarkAttendanceScreen({super.key, this.classData});

  @override
  State<MarkAttendanceScreen> createState() => _MarkAttendanceScreenState();
}

class _MarkAttendanceScreenState extends State<MarkAttendanceScreen> {
  final _service = StaffService();

  List<dynamic> _classes  = [];
  List<dynamic> _students = [];
  Map<int, String> _statusMap = {};

  int?    _selectedClassId;
  int?    _selectedSectionId;
  String  _selectedClassName   = '';
  String  _date = DateFormat('yyyy-MM-dd').format(DateTime.now());
  bool _loadingClasses  = true;
  bool _loadingStudents = false;
  bool _saving          = false;

  @override
  void initState() {
    super.initState();
    _loadClasses();
    // Pre-select if opened from a class card
    if (widget.classData != null) {
      _selectedClassId   = widget.classData!['class_id'];  // adjust key if needed
      _selectedSectionId = widget.classData!['section_id'];
      _selectedClassName = '${widget.classData!['class']} - ${widget.classData!['section']}';
    }
  }

  Future<void> _loadClasses() async {
    try {
      final r = await _service.getMyClasses();
      if (r['success'] == true) setState(() => _classes = r['data'] ?? []);
    } catch (_) {}
    if (mounted) setState(() => _loadingClasses = false);
    if (_selectedClassId != null) _loadStudents();
  }

  Future<void> _loadStudents() async {
    if (_selectedClassId == null) return;
    setState(() { _loadingStudents = true; _students = []; _statusMap = {}; });
    try {
      final r = await _service.getStudents(
        classId:   _selectedClassId!,
        sectionId: _selectedSectionId,
      );
      if (r['success'] == true) {
        final list = (r['data'] as List<dynamic>?) ?? [];
        setState(() {
          _students  = list;
          _statusMap = { for (var s in list) s['id'] as int : 'present' };
        });
      }
    } catch (_) {}
    if (mounted) setState(() => _loadingStudents = false);
  }

  void _markAll(String status) {
    setState(() {
      for (var key in _statusMap.keys) {
        _statusMap[key] = status;
      }
    });
  }

  Future<void> _save() async {
    if (_students.isEmpty) return;
    setState(() => _saving = true);
    try {
      final records = _statusMap.entries
          .map((e) => {'student_id': e.key, 'status': e.value})
          .toList();

      final r = await _service.markAttendance(date: _date, records: records);
      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: Text(r['message'] ?? 'Saved!'),
        backgroundColor: r['success'] == true ? AppTheme.success : AppTheme.error,
      ));
    } catch (_) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
        content: Text('Save failed. Try again.'),
        backgroundColor: AppTheme.error,
      ));
    }
    if (mounted) setState(() => _saving = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Mark Attendance'),
        actions: [
          if (_students.isNotEmpty)
            TextButton(
              onPressed: _saving ? null : _save,
              child: _saving
                  ? const SizedBox(width: 18, height: 18,
                      child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                  : const Text('Save', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
        ],
      ),
      body: Column(
        children: [
          // Controls
          Container(
            color: AppTheme.surface,
            padding: const EdgeInsets.all(16),
            child: Column(
              children: [
                // Date picker
                InkWell(
                  onTap: () async {
                    final picked = await showDatePicker(
                      context: context,
                      initialDate: DateTime.parse(_date),
                      firstDate: DateTime.now().subtract(const Duration(days: 30)),
                      lastDate: DateTime.now(),
                    );
                    if (picked != null) {
                      setState(() => _date = DateFormat('yyyy-MM-dd').format(picked));
                    }
                  },
                  child: InputDecorator(
                    decoration: AppTheme.inputDecoration(
                        label: 'Date', icon: Icons.calendar_today_rounded),
                    child: Text(_date, style: AppTheme.bodyMedium),
                  ),
                ),
                const SizedBox(height: 12),

                // Class selector
                if (_loadingClasses)
                  const LinearProgressIndicator()
                else
                  DropdownButtonFormField<int>(
                    value: _selectedClassId,
                    decoration: AppTheme.inputDecoration(
                        label: 'Select Class / Section', icon: Icons.class_rounded),
                    items: _classes.map((cls) {
                      return DropdownMenuItem<int>(
                        value: cls['section_id'] as int,
                        child: Text('${cls['class']} - ${cls['section']}'),
                      );
                    }).toList(),
                    onChanged: (val) {
                      final cls = _classes.firstWhere((c) => c['section_id'] == val);
                      setState(() {
                        _selectedSectionId = val;
                        _selectedClassId   = val; // use section_id as key
                        _selectedClassName = '${cls['class']} - ${cls['section']}';
                      });
                      _loadStudents();
                    },
                  ),
              ],
            ),
          ),

          // Bulk mark buttons
          if (_students.isNotEmpty)
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              child: Row(
                children: [
                  Text('Mark All:', style: AppTheme.bodySmall),
                  const SizedBox(width: 12),
                  ...['present', 'absent', 'late', 'holiday'].map((s) =>
                    Padding(
                      padding: const EdgeInsets.only(right: 6),
                      child: OutlinedButton(
                        onPressed: () => _markAll(s),
                        style: OutlinedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                          side: BorderSide(color: _statusColor(s)),
                          foregroundColor: _statusColor(s),
                          minimumSize: Size.zero,
                          tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                        ),
                        child: Text(s[0].toUpperCase() + s.substring(1), style: const TextStyle(fontSize: 11)),
                      ),
                    )
                  ),
                ],
              ),
            ),

          // Students list
          Expanded(
            child: _loadingStudents
                ? const Center(child: CircularProgressIndicator())
                : _students.isEmpty
                    ? Center(child: Text('Select a class to load students.', style: AppTheme.bodySmall))
                    : ListView.separated(
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        itemCount: _students.length,
                        separatorBuilder: (_, __) => const Divider(height: 1),
                        itemBuilder: (_, i) {
                          final s   = _students[i] as Map<String, dynamic>;
                          final id  = s['id'] as int;
                          final cur = _statusMap[id] ?? 'present';

                          return ListTile(
                            dense: true,
                            leading: CircleAvatar(
                              radius: 16,
                              backgroundColor: AppTheme.primary.withOpacity(.1),
                              child: Text('${s['roll_number']}',
                                style: const TextStyle(fontSize: 11, color: AppTheme.primary, fontWeight: FontWeight.bold)),
                            ),
                            title: Text(s['name'] ?? '', style: AppTheme.bodyMedium),
                            trailing: DropdownButton<String>(
                              value: cur,
                              underline: const SizedBox(),
                              style: TextStyle(fontSize: 12, color: _statusColor(cur), fontWeight: FontWeight.w600),
                              items: ['present', 'absent', 'late', 'holiday'].map((st) =>
                                DropdownMenuItem(
                                  value: st,
                                  child: Text(st[0].toUpperCase() + st.substring(1),
                                    style: TextStyle(color: _statusColor(st), fontSize: 12)),
                                ),
                              ).toList(),
                              onChanged: (v) {
                                if (v != null) setState(() => _statusMap[id] = v);
                              },
                            ),
                          );
                        },
                      ),
          ),
        ],
      ),
    );
  }

  Color _statusColor(String s) => switch (s) {
    'present' => AppTheme.success,
    'absent'  => AppTheme.error,
    'late'    => AppTheme.warning,
    'holiday' => AppTheme.primary,
    _         => AppTheme.textSecondary,
  };
}
