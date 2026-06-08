import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../core/services/student_service.dart';
import '../../core/services/auth_service.dart';
import '../../core/utils/app_theme.dart';
import '../../widgets/stat_card.dart';
import '../../widgets/section_header.dart';

class ParentHomeScreen extends ConsumerStatefulWidget {
  const ParentHomeScreen({super.key});

  @override
  ConsumerState<ParentHomeScreen> createState() => _ParentHomeScreenState();
}

class _ParentHomeScreenState extends ConsumerState<ParentHomeScreen> {
  final _student = StudentService();
  final _auth    = AuthService();

  List<dynamic> _children = [];
  List<dynamic> _notifications = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final profile = await _student.getProfile();
      if (profile['success'] == true) {
        final data = profile['data'];
        setState(() => _children = data is List ? data : [data]);
      }
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _logout() async {
    await _auth.logout();
    if (mounted) context.go('/login');
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Parent Portal'),
        actions: [
          IconButton(icon: const Icon(Icons.notifications_outlined), onPressed: () => context.push('/notifications')),
          IconButton(icon: const Icon(Icons.logout_rounded), onPressed: _logout),
        ],
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _load,
              child: ListView(
                padding: const EdgeInsets.all(16),
                children: [
                  // Greeting
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(colors: [Color(0xFF2563EB), Color(0xFF4F46E5)]),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Row(
                      children: [
                        const Icon(Icons.family_restroom_rounded, color: Colors.white, size: 36),
                        const SizedBox(width: 16),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text('Welcome Back!', style: AppTheme.heading2.copyWith(color: Colors.white)),
                            Text('${_children.length} child(ren) linked', style: AppTheme.bodySmall.copyWith(color: Colors.white70)),
                          ],
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),

                  // Children Cards
                  const SectionHeader(title: 'My Children'),
                  const SizedBox(height: 8),
                  ..._children.map((child) => _buildChildCard(child)),

                  if (_children.isEmpty)
                    Card(
                      child: Padding(
                        padding: const EdgeInsets.all(24),
                        child: Center(child: Text('No children linked. Contact school.', style: AppTheme.bodySmall)),
                      ),
                    ),
                ],
              ),
            ),
    );
  }

  Widget _buildChildCard(Map<String, dynamic> child) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: Column(
        children: [
          ListTile(
            leading: CircleAvatar(
              backgroundColor: AppTheme.primary.withOpacity(0.1),
              child: Text(child['name'][0], style: const TextStyle(fontWeight: FontWeight.bold, color: AppTheme.primary)),
            ),
            title: Text(child['name'] ?? '', style: AppTheme.labelBold),
            subtitle: Text('Class ${child['class']} ${child['section']} · Adm: ${child['admission_no']}',
                style: AppTheme.bodySmall),
            trailing: Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: AppTheme.success.withOpacity(0.1),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Text(child['status'] ?? '', style: const TextStyle(fontSize: 11, color: AppTheme.success, fontWeight: FontWeight.w600)),
            ),
          ),
          const Divider(height: 1),
          Padding(
            padding: const EdgeInsets.all(12),
            child: Row(
              children: [
                _actionBtn(Icons.receipt_long_rounded, 'Pay Fees',    AppTheme.primary,
                    () => context.push('/parent/fees',    extra: child)),
                _actionBtn(Icons.bar_chart_rounded,     'Results',     AppTheme.secondary,
                    () => context.push('/parent/results', extra: child)),
                _actionBtn(Icons.calendar_today_rounded,'Attendance',  AppTheme.success,
                    () => context.push('/parent/attendance', extra: child)),
                _actionBtn(Icons.book_rounded,           'Homework',   AppTheme.warning,
                    () => context.push('/parent/homework',  extra: child)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _actionBtn(IconData icon, String label, Color color, VoidCallback onTap) {
    return Expanded(
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 8),
          child: Column(
            children: [
              Icon(icon, color: color, size: 24),
              const SizedBox(height: 4),
              Text(label, style: TextStyle(fontSize: 10, color: color, fontWeight: FontWeight.w600)),
            ],
          ),
        ),
      ),
    );
  }
}
