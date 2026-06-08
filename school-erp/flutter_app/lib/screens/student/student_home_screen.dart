import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/services/student_service.dart';
import '../../core/services/auth_service.dart';
import '../../core/utils/app_theme.dart';
import '../../widgets/section_header.dart';
import '../../widgets/info_row.dart';

class StudentHomeScreen extends StatefulWidget {
  const StudentHomeScreen({super.key});

  @override
  State<StudentHomeScreen> createState() => _StudentHomeScreenState();
}

class _StudentHomeScreenState extends State<StudentHomeScreen> {
  final _service = StudentService();
  final _auth    = AuthService();

  Map<String, dynamic>? _student;
  Map<String, dynamic>  _attendance = {};
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final profile = await _service.getProfile();
      if (profile['success'] == true) {
        final data = profile['data'];
        final s = data is List ? data[0] : data;
        setState(() => _student = s);

        final month = DateTime.now().toIso8601String().substring(0, 7);
        final att = await _service.getAttendance(s['id'], month);
        if (att['success'] == true) setState(() => _attendance = att['summary'] ?? {});
      }
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Student Portal'),
        actions: [
          IconButton(icon: const Icon(Icons.notifications_outlined), onPressed: () => context.push('/notifications')),
          IconButton(icon: const Icon(Icons.logout_rounded), onPressed: () async {
            await _auth.logout();
            if (context.mounted) context.go('/login');
          }),
        ],
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _load,
              child: ListView(
                padding: const EdgeInsets.all(16),
                children: [
                  // Profile Card
                  if (_student != null) _buildProfileCard(),
                  const SizedBox(height: 16),

                  // Quick Actions
                  const SectionHeader(title: 'Quick Actions'),
                  const SizedBox(height: 8),
                  GridView.count(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    crossAxisCount: 2,
                    crossAxisSpacing: 12,
                    mainAxisSpacing: 12,
                    childAspectRatio: 1.6,
                    children: [
                      _actionCard(Icons.bar_chart_rounded,     'My Results',   AppTheme.primary,    '/student/results'),
                      _actionCard(Icons.calendar_today_rounded,'Attendance',   AppTheme.success,    '/student/attendance'),
                      _actionCard(Icons.book_rounded,          'Homework',     AppTheme.secondary,  '/student/homework'),
                      _actionCard(Icons.notifications_outlined,'Notifications',AppTheme.warning,    '/notifications'),
                    ],
                  ),
                  const SizedBox(height: 16),

                  // Attendance Summary
                  const SectionHeader(title: 'This Month\'s Attendance'),
                  const SizedBox(height: 8),
                  Card(
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Row(
                        children: [
                          _attBadge('Present', _attendance['present'] ?? 0, AppTheme.success),
                          _attBadge('Absent',  _attendance['absent']  ?? 0, AppTheme.error),
                          _attBadge('Late',    _attendance['late']    ?? 0, AppTheme.warning),
                          _attBadge('Holiday', _attendance['holiday'] ?? 0, AppTheme.primary),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
    );
  }

  Widget _buildProfileCard() {
    final s = _student!;
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            CircleAvatar(
              radius: 30,
              backgroundColor: AppTheme.success.withOpacity(0.15),
              child: Text(s['name'][0],
                style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: AppTheme.success)),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(s['name'] ?? '', style: AppTheme.heading2),
                  Text('Class ${s['class_section']}', style: AppTheme.bodySmall),
                  Text('Adm: ${s['admission_no']} · Roll: ${s['roll_number']}', style: AppTheme.bodySmall),
                ],
              ),
            ),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: AppTheme.success.withOpacity(0.1),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Text(s['status'] ?? '',
                style: const TextStyle(fontSize: 11, color: AppTheme.success, fontWeight: FontWeight.w600)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _actionCard(IconData icon, String label, Color color, String route) {
    return InkWell(
      onTap: () => context.push(route),
      borderRadius: BorderRadius.circular(16),
      child: Card(
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              Icon(icon, color: color, size: 30),
              const SizedBox(width: 12),
              Text(label, style: AppTheme.labelBold),
            ],
          ),
        ),
      ),
    );
  }

  Widget _attBadge(String label, int count, Color color) {
    return Expanded(
      child: Column(
        children: [
          Text('$count', style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: color)),
          Text(label, style: AppTheme.bodySmall),
        ],
      ),
    );
  }
}
