import 'package:flutter/material.dart';
import '../../core/services/correspondent_service.dart';
import '../../core/utils/app_theme.dart';

class SendNotificationScreen extends StatefulWidget {
  const SendNotificationScreen({super.key});

  @override
  State<SendNotificationScreen> createState() => _SendNotificationScreenState();
}

class _SendNotificationScreenState extends State<SendNotificationScreen> {
  final _service  = CorrespondentService();
  final _formKey  = GlobalKey<FormState>();
  final _title    = TextEditingController();
  final _body     = TextEditingController();

  String _targetRole = 'all';
  String _type       = 'general';
  bool   _sending    = false;

  final _roles = ['all', 'parent', 'student', 'teacher'];
  final _types = ['general', 'exam', 'fee', 'holiday', 'result', 'urgent'];

  Future<void> _send() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _sending = true);

    try {
      final r = await _service.sendNotification(
        title:      _title.text.trim(),
        body:       _body.text.trim(),
        targetRole: _targetRole,
        type:       _type,
      );

      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: Text(r['message'] ?? 'Notification sent!'),
        backgroundColor: r['success'] == true ? AppTheme.success : AppTheme.error,
      ));
      if (r['success'] == true) {
        _title.clear();
        _body.clear();
        setState(() { _targetRole = 'all'; _type = 'general'; });
      }
    } catch (_) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Failed. Try again.'), backgroundColor: AppTheme.error));
    }
    if (mounted) setState(() => _sending = false);
  }

  @override
  void dispose() {
    _title.dispose();
    _body.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Send Notification')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Banner
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: AppTheme.primary.withOpacity(.08),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: AppTheme.primary.withOpacity(.2)),
                ),
                child: Row(children: [
                  const Icon(Icons.campaign_rounded, color: AppTheme.primary, size: 28),
                  const SizedBox(width: 12),
                  Expanded(child: Text(
                    'Send push notifications and SMS to selected recipients.',
                    style: AppTheme.bodySmall,
                  )),
                ]),
              ),
              const SizedBox(height: 20),

              // Target audience
              const Text('Send To', style: AppTheme.bodySmall_),
              const SizedBox(height: 8),
              Wrap(
                spacing: 8,
                children: _roles.map((role) => ChoiceChip(
                  label: Text(role[0].toUpperCase() + role.substring(1)),
                  selected: _targetRole == role,
                  onSelected: (_) => setState(() => _targetRole = role),
                  selectedColor: AppTheme.primary.withOpacity(.15),
                  labelStyle: TextStyle(
                    color: _targetRole == role ? AppTheme.primary : AppTheme.textSecondary,
                    fontWeight: _targetRole == role ? FontWeight.bold : FontWeight.normal,
                  ),
                )).toList(),
              ),
              const SizedBox(height: 16),

              // Type
              const Text('Type', style: AppTheme.bodySmall_),
              const SizedBox(height: 8),
              Wrap(
                spacing: 8,
                children: _types.map((t) => ChoiceChip(
                  label: Text(t[0].toUpperCase() + t.substring(1)),
                  selected: _type == t,
                  onSelected: (_) => setState(() => _type = t),
                  selectedColor: AppTheme.secondary.withOpacity(.15),
                  labelStyle: TextStyle(
                    color: _type == t ? AppTheme.secondary : AppTheme.textSecondary,
                    fontWeight: _type == t ? FontWeight.bold : FontWeight.normal,
                  ),
                )).toList(),
              ),
              const SizedBox(height: 16),

              // Title
              TextFormField(
                controller: _title,
                decoration: AppTheme.inputDecoration(
                    label: 'Notification Title *', icon: Icons.title_rounded),
                maxLength: 100,
                validator: (v) => v == null || v.isEmpty ? 'Required' : null,
              ),
              const SizedBox(height: 12),

              // Body
              TextFormField(
                controller: _body,
                decoration: AppTheme.inputDecoration(
                    label: 'Message *', icon: Icons.message_rounded),
                maxLines: 5,
                validator: (v) => v == null || v.isEmpty ? 'Required' : null,
              ),
              const SizedBox(height: 24),

              SizedBox(
                height: 52,
                child: ElevatedButton.icon(
                  onPressed: _sending ? null : _send,
                  icon: _sending
                      ? const SizedBox(width: 18, height: 18,
                          child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                      : const Icon(Icons.send_rounded, color: Colors.white),
                  label: Text(
                    _sending ? 'Sending…' : 'Send to ${_targetRole.toUpperCase()}',
                    style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w600),
                  ),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.primary,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// Quick style getter to avoid the static method issue
extension on TextStyle {
  static const bodySmall_ = TextStyle(
    fontSize: 12, color: AppTheme.textSecondary, fontWeight: FontWeight.w500);
}
