import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api/api_client.dart';
import '../../core/api/api_endpoints.dart';
import '../../core/utils/app_theme.dart';

class NotificationsScreen extends StatefulWidget {
  const NotificationsScreen({super.key});

  @override
  State<NotificationsScreen> createState() => _NotificationsScreenState();
}

class _NotificationsScreenState extends State<NotificationsScreen> {
  final _dio = ApiClient.instance;

  List<dynamic> _notifications = [];
  int _unread = 0;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final r = await _dio.get(ApiEndpoints.notifications);
      final d = r.data as Map<String, dynamic>;
      if (d['success'] == true) {
        setState(() {
          _notifications = d['data'] ?? [];
          _unread        = d['unread_count'] ?? 0;
        });
      }
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _markRead(int id) async {
    try {
      await _dio.post(ApiEndpoints.markRead(id));
      setState(() {
        for (var n in _notifications) {
          if ((n as Map<String, dynamic>)['id'] == id) {
            n['is_read'] = true;
          }
        }
        _unread = _notifications.where((n) => (n as Map)['is_read'] == false).length;
      });
    } catch (_) {}
  }

  Color _typeColor(String? type) => switch (type) {
    'exam'    => AppTheme.secondary,
    'fee'     => AppTheme.success,
    'result'  => AppTheme.primary,
    'holiday' => AppTheme.warning,
    'urgent'  => AppTheme.error,
    _         => AppTheme.textSecondary,
  };

  IconData _typeIcon(String? type) => switch (type) {
    'exam'    => Icons.quiz_rounded,
    'fee'     => Icons.payments_rounded,
    'result'  => Icons.bar_chart_rounded,
    'holiday' => Icons.celebration_rounded,
    'urgent'  => Icons.warning_rounded,
    _         => Icons.notifications_rounded,
  };

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Row(children: [
          const Text('Notifications'),
          if (_unread > 0) ...[
            const SizedBox(width: 8),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
              decoration: BoxDecoration(
                  color: AppTheme.error, borderRadius: BorderRadius.circular(20)),
              child: Text('$_unread',
                  style: const TextStyle(
                      color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold)),
            ),
          ],
        ]),
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _notifications.isEmpty
              ? Center(
                  child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                    const Icon(Icons.notifications_off_outlined,
                        size: 60, color: AppTheme.textSecondary),
                    const SizedBox(height: 12),
                    Text('No notifications yet.', style: AppTheme.bodySmall),
                  ]))
              : RefreshIndicator(
                  onRefresh: _load,
                  child: ListView.separated(
                    padding: const EdgeInsets.all(16),
                    itemCount: _notifications.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 8),
                    itemBuilder: (_, i) {
                      final n     = _notifications[i] as Map<String, dynamic>;
                      final isRead = n['is_read'] == true;
                      final type  = n['type'] as String?;
                      final color = _typeColor(type);

                      return InkWell(
                        onTap: () async {
                          if (!isRead) await _markRead(n['id']);
                          // Expand description
                          showDialog(
                            context: context,
                            builder: (_) => AlertDialog(
                              title: Text(n['title'] ?? ''),
                              content: Text(n['body'] ?? ''),
                              actions: [
                                TextButton(
                                  onPressed: () => Navigator.of(context).pop(),
                                  child: const Text('Close'),
                                ),
                              ],
                            ),
                          );
                        },
                        borderRadius: BorderRadius.circular(14),
                        child: Container(
                          padding: const EdgeInsets.all(14),
                          decoration: BoxDecoration(
                            color: isRead ? AppTheme.surface : color.withOpacity(.05),
                            borderRadius: BorderRadius.circular(14),
                            border: Border.all(
                              color: isRead ? AppTheme.border : color.withOpacity(.3),
                            ),
                          ),
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              CircleAvatar(
                                radius: 20,
                                backgroundColor: color.withOpacity(.1),
                                child: Icon(_typeIcon(type), color: color, size: 18),
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Row(
                                      children: [
                                        Expanded(
                                          child: Text(
                                            n['title'] ?? '',
                                            style: AppTheme.labelBold.copyWith(
                                              fontWeight: isRead ? FontWeight.w500 : FontWeight.bold,
                                            ),
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis,
                                          ),
                                        ),
                                        if (!isRead)
                                          Container(
                                            width: 8, height: 8,
                                            decoration: const BoxDecoration(
                                                color: AppTheme.primary,
                                                shape: BoxShape.circle),
                                          ),
                                      ],
                                    ),
                                    const SizedBox(height: 4),
                                    Text(
                                      n['body'] ?? '',
                                      style: AppTheme.bodySmall,
                                      maxLines: 2,
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                    const SizedBox(height: 6),
                                    Text(
                                      n['sent_at'] != null
                                          ? _formatDate(n['sent_at'])
                                          : '',
                                      style: AppTheme.bodySmall.copyWith(fontSize: 10),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),
    );
  }

  String _formatDate(String iso) {
    try {
      final dt = DateTime.parse(iso).toLocal();
      final now = DateTime.now();
      if (dt.day == now.day && dt.month == now.month && dt.year == now.year) {
        return 'Today, ${DateFormat('h:mm a').format(dt)}';
      }
      return DateFormat('d MMM y, h:mm a').format(dt);
    } catch (_) {
      return iso;
    }
  }
}
