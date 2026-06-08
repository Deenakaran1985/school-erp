import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import 'package:go_router/go_router.dart';
import '../../core/services/correspondent_service.dart';
import '../../core/services/auth_service.dart';
import '../../core/utils/app_theme.dart';
import '../../widgets/section_header.dart';
import '../../widgets/stat_card.dart';

class CorrespondentHomeScreen extends StatefulWidget {
  const CorrespondentHomeScreen({super.key});

  @override
  State<CorrespondentHomeScreen> createState() => _CorrespondentHomeScreenState();
}

class _CorrespondentHomeScreenState extends State<CorrespondentHomeScreen> {
  final _service = CorrespondentService();
  final _auth    = AuthService();

  Map<String, dynamic>? _dashboard;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final d = await _service.getDashboard();
      if (d['success'] == true) setState(() => _dashboard = d['data']);
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Correspondent Dashboard'),
        actions: [
          IconButton(icon: const Icon(Icons.send_rounded), onPressed: () => context.push('/correspondent/send-notification')),
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
                  // Academic Year Banner
                  if (_dashboard != null) Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(colors: [Color(0xFF7C3AED), Color(0xFF2563EB)]),
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: Row(
                      children: [
                        const Icon(Icons.school_rounded, color: Colors.white),
                        const SizedBox(width: 10),
                        Text('Academic Year: ${_dashboard!['academic_year']}',
                          style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w600)),
                      ],
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Stats Grid
                  if (_dashboard != null) ...[
                    const SectionHeader(title: 'Overview'),
                    const SizedBox(height: 8),
                    GridView.count(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      crossAxisCount: 2,
                      crossAxisSpacing: 12,
                      mainAxisSpacing: 12,
                      childAspectRatio: 1.5,
                      children: [
                        StatCard(label: 'Total Students', value: '${_dashboard!['total_students']}',
                          icon: Icons.people_rounded, color: AppTheme.primary),
                        StatCard(label: 'Total Staff', value: '${_dashboard!['total_staff']}',
                          icon: Icons.badge_rounded, color: AppTheme.secondary),
                        StatCard(label: 'Fee Collected', value: '₹${_fmt(_dashboard!['fee_collected'])}',
                          icon: Icons.payments_rounded, color: AppTheme.success),
                        StatCard(label: 'Net Income', value: '₹${_fmt(_dashboard!['net_income'])}',
                          icon: Icons.trending_up_rounded, color: AppTheme.warning),
                      ],
                    ),
                    const SizedBox(height: 16),
                  ],

                  // Quick Actions
                  const SectionHeader(title: 'Management'),
                  const SizedBox(height: 8),
                  ...[
                    [Icons.payments_rounded,           'Fee Summary',      '/correspondent/fees',      AppTheme.success],
                    [Icons.people_alt_rounded,          'Staff List',       '/correspondent/staff',     AppTheme.secondary],
                    [Icons.account_balance_wallet_rounded,'Payroll Summary','/correspondent/payroll',   AppTheme.primary],
                    [Icons.receipt_rounded,             'Expenses',         '/correspondent/expenses',  AppTheme.warning],
                    [Icons.campaign_rounded,            'Send Notification','/correspondent/send-notification', AppTheme.error],
                  ].map((item) => Card(
                    margin: const EdgeInsets.only(bottom: 8),
                    child: ListTile(
                      leading: CircleAvatar(
                        backgroundColor: (item[3] as Color).withOpacity(0.1),
                        child: Icon(item[0] as IconData, color: item[3] as Color, size: 22),
                      ),
                      title: Text(item[1] as String, style: AppTheme.labelBold),
                      trailing: const Icon(Icons.arrow_forward_ios_rounded, size: 14, color: AppTheme.textSecondary),
                      onTap: () => context.push(item[2] as String),
                    ),
                  )),
                ],
              ),
            ),
    );
  }

  String _fmt(dynamic v) {
    if (v == null) return '0';
    final n = double.tryParse(v.toString()) ?? 0;
    if (n >= 100000) return '${(n / 100000).toStringAsFixed(1)}L';
    if (n >= 1000)   return '${(n / 1000).toStringAsFixed(1)}K';
    return n.toStringAsFixed(0);
  }
}
