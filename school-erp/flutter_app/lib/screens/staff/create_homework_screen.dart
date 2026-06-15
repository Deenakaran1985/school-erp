import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/services/staff_service.dart';
import '../../core/utils/app_theme.dart';

class CreateHomeworkScreen extends StatefulWidget {
  const CreateHomeworkScreen({super.key});

  @override
  State<CreateHomeworkScreen> createState() => _CreateHomeworkScreenState();
}

class _CreateHomeworkScreenState extends State<CreateHomeworkScreen> {
  final _service  = StaffService();
  final _formKey  = GlobalKey<FormState>();
  final _title    = TextEditingController();
  final _desc     = TextEditingController();

  List<dynamic> _classes = [];
  Map<String, dynamic>? _selectedClass;
  String _dueDate = DateFormat('yyyy-MM-dd').format(DateTime.now().add(const Duration(days: 1)));
  bool _saving   = false;
  bool _loading  = true;

  @override
  void initState() {
    super.initState();
    _loadClasses();
  }

  Future<void> _loadClasses() async {
    try {
      final r = await _service.getMyClasses();
      if (r['success'] == true) setState(() => _classes = r['data'] ?? []);
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _pickDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: DateTime.parse(_dueDate),
      firstDate: DateTime.now().add(const Duration(days: 1)),
      lastDate: DateTime.now().add(const Duration(days: 90)),
    );
    if (picked != null) setState(() => _dueDate = DateFormat('yyyy-MM-dd').format(picked));
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate() || _selectedClass == null) return;
    setState(() => _saving = true);

    try {
      final r = await _service.createHomework(
        subjectId: 1, // ideally from a subject picker — keeping simple for now
        classId:   _selectedClass!['section_id'] as int,
        title:     _title.text.trim(),
        description: _desc.text.trim().isEmpty ? null : _desc.text.trim(),
        dueDate:   _dueDate,
      );

      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: Text(r['message'] ?? 'Homework assigned!'),
        backgroundColor: r['success'] == true ? AppTheme.success : AppTheme.error,
      ));
      if (r['success'] == true) Navigator.of(context).pop();
    } catch (_) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Failed. Try again.'), backgroundColor: AppTheme.error));
    }
    if (mounted) setState(() => _saving = false);
  }

  @override
  void dispose() {
    _title.dispose();
    _desc.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Assign Homework')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // Class selector
                    DropdownButtonFormField<Map<String, dynamic>>(
                      value: _selectedClass,
                      decoration: AppTheme.inputDecoration(
                          label: 'Class / Section *', icon: Icons.class_rounded),
                      items: _classes.map((cls) => DropdownMenuItem(
                        value: cls as Map<String, dynamic>,
                        child: Text('${cls['class']} - ${cls['section']}'),
                      )).toList(),
                      onChanged: (v) => setState(() => _selectedClass = v),
                      validator: (v) => v == null ? 'Select a class' : null,
                    ),
                    const SizedBox(height: 16),

                    // Title
                    TextFormField(
                      controller: _title,
                      decoration: AppTheme.inputDecoration(
                          label: 'Homework Title *', icon: Icons.title_rounded),
                      validator: (v) =>
                          v == null || v.isEmpty ? 'Required' : null,
                    ),
                    const SizedBox(height: 16),

                    // Description
                    TextFormField(
                      controller: _desc,
                      maxLines: 4,
                      decoration: AppTheme.inputDecoration(
                          label: 'Description (optional)', icon: Icons.notes_rounded),
                    ),
                    const SizedBox(height: 16),

                    // Due date
                    InkWell(
                      onTap: _pickDate,
                      child: InputDecorator(
                        decoration: AppTheme.inputDecoration(
                            label: 'Due Date *', icon: Icons.event_rounded),
                        child: Text(
                          DateFormat('d MMMM yyyy').format(DateTime.parse(_dueDate)),
                          style: AppTheme.bodyMedium,
                        ),
                      ),
                    ),
                    const SizedBox(height: 24),

                    SizedBox(
                      height: 48,
                      child: ElevatedButton(
                        onPressed: _saving ? null : _submit,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppTheme.primary,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                        ),
                        child: _saving
                            ? const SizedBox(width: 20, height: 20,
                                child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                            : const Text('Assign Homework',
                                style: TextStyle(color: Colors.white, fontWeight: FontWeight.w600)),
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }
}
