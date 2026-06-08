import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/services/staff_service.dart';
import '../../core/services/auth_service.dart';
import '../../core/utils/app_theme.dart';
import '../../widgets/section_header.dart';

class StaffHomeScreen extends StatefulWidget {
  const StaffHomeScreen({super.key});

  @override
  State<StaffHomeScreen> createState() => _StaffHomeScreenState();
}

class _StaffHomeScreenState extends State<StaffHomeScreen> {
  final _service = StaffService();
  final _auth    = AuthService();

  Map<String, dynamic>? _profile;
  Map<String, dynamic>  _attendance = {};
  List<dynamic>         _classes    = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final [profile, att, classes] = await Future.wait([
        _service.getProfile(),
        _service.getAttendance(),
        _service.getMyClasses(),
      ]);
      if (profile['success'] == true) setState(() => _profile    = profile['data']);
      if (att['success']     == true) setState(() => _attendance  = att['summary'] ?? {});
      if (classes['success'] == true) setState(() => _classes     = classes['data'] ?? []);
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Staff Portal'),
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
                  if (_profile != null) _buildProfileCard(),
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
                      _actionCard(Icons.how_to_reg_rounded,   'Mark Attendance', AppTheme.primary,   '/staff/mark-attendance'),
                      _actionCard(Icons.book_outlined,         'Assign Homework', AppTheme.secondary, '/staff/homework'),
                      _actionCard(Icons.receipt_long_rounded,  'My Payslips',     AppTheme.success,   '/staff/payslips'),
                      _actionCard(Icons.calendar_today_rounded,'My Attendance',   AppTheme.warning,   '/staff/attendance'),
                    ],
                  ),
                  const SizedBox(height: 16),

                  // My Classes
                  const SectionHeader(title: 'My Classes'),
                  const SizedBox(height: 8),
                  if (_classes.isEmpty)
                    Card(child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Text('No classes assigned.', style: AppTheme.bodySmall),
                    ))
                  else
                    ..._classes.map((cls) => Card(
                      margin: const EdgeInsets.only(bottom: 8),
                      child: ListTile(
                        leading: CircleAvatar(
                          backgroundColor: AppTheme.primary.withOpacity(0.1),
                          child: Text(cls['section'] ?? '', style: const TextStyle(color: AppTheme.primary, fontWeight: FontWeight.bold)),
                        ),
                        title: Text('${cls['class']} - Section ${cls['section']}', style: AppTheme.labelBold),
                        subtitle: Text('${cls['student_count']} students', style: AppTheme.bodySmall),
                        trailing: const Icon(Icons.arrow_forward_ios_rounded, size: 14),
                        onTap: () => context.push('/staff/students', extra: cls),
                      ),
                    )),

                  const SizedBox(height: 16),

                  // Attendance Summary
                  const SectionHeader(title: 'My Attendance (This Month)'),
                  const SizedBox(height: 8),
                  Card(
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        children: [
                          _attBadge('Present', _attendance['present'] ?? 0, AppTheme.success),
                          _attBadge('Absent',  _attendance['absent']  ?? 0, AppTheme.error),
                          _attBadge('Late',    _attendance['late']    ?? 0, AppTheme.warning),
                          _attBadge('Leave',   _attendance['leave']   ?? 0, AppTheme.secondary),
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
    final p = _profile!;
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            CircleAvatar(
              radius: 28,
              backgroundColor: AppTheme.secondary.withOpacity(0.15),
              child: Text(p['name']?[0] ?? '?',
                style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: AppTheme.secondary)),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(p['name'] ?? '', style: AppTheme.heading2),
                  Text('${p['designation']} · ${p['department']}', style: AppTheme.bodySmall),
                  Text('Gross: ₹${p['gross_salary'] ?? 0}', style: AppTheme.bodySmall),
                ],
              ),
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
              Icon(icon, color: color, size: 28),
              const SizedBox(width: 10),
              Flexible(child: Text(label, style: AppTheme.labelBold, maxLines: 2)),
            ],
          ),
        ),
      ),
    );
  }

  Widget _attBadge(String label, int count, Color color) {
    return Column(
      children: [
        Text('$count', style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: color)),
        Text(label, style: AppTheme.bodySmall),
      ],
    );
  }
}
