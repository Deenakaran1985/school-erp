import 'package:flutter/material.dart';
import '../core/utils/app_theme.dart';

class SectionHeader extends StatelessWidget {
  final String title;
  final Widget? trailing;

  const SectionHeader({super.key, required this.title, this.trailing});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(title, style: AppTheme.labelBold.copyWith(color: AppTheme.textSecondary, letterSpacing: 0.5)),
        if (trailing != null) trailing!,
      ],
    );
  }
}
