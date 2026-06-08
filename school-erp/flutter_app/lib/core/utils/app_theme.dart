import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AppTheme {
  static const Color primary    = Color(0xFF2563EB);
  static const Color secondary  = Color(0xFF7C3AED);
  static const Color success    = Color(0xFF16A34A);
  static const Color warning    = Color(0xFFD97706);
  static const Color error      = Color(0xFFDC2626);
  static const Color background = Color(0xFFF1F5F9);
  static const Color surface    = Colors.white;
  static const Color textPrimary   = Color(0xFF1E293B);
  static const Color textSecondary = Color(0xFF64748B);
  static const Color border     = Color(0xFFE2E8F0);

  static ThemeData get lightTheme => ThemeData(
    useMaterial3: true,
    colorScheme: ColorScheme.fromSeed(
      seedColor: primary,
      brightness: Brightness.light,
    ),
    textTheme: GoogleFonts.outfitTextTheme(),
    scaffoldBackgroundColor: background,
    appBarTheme: AppBarTheme(
      backgroundColor: surface,
      elevation: 0,
      centerTitle: false,
      iconTheme: const IconThemeData(color: textPrimary),
      titleTextStyle: GoogleFonts.outfit(
        color: textPrimary,
        fontSize: 18,
        fontWeight: FontWeight.w600,
      ),
    ),
    cardTheme: CardThemeData(
      color: surface,
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
        side: const BorderSide(color: border),
      ),
    ),
  );

  // Text Styles
  static TextStyle get heading1 => GoogleFonts.outfit(
    fontSize: 24, fontWeight: FontWeight.w700, color: textPrimary);
  static TextStyle get heading2 => GoogleFonts.outfit(
    fontSize: 18, fontWeight: FontWeight.w600, color: textPrimary);
  static TextStyle get bodyMedium => GoogleFonts.outfit(
    fontSize: 14, fontWeight: FontWeight.w400, color: textPrimary);
  static TextStyle get bodySmall => GoogleFonts.outfit(
    fontSize: 12, fontWeight: FontWeight.w400, color: textSecondary);
  static TextStyle get labelBold => GoogleFonts.outfit(
    fontSize: 13, fontWeight: FontWeight.w600, color: textPrimary);

  // Input Decoration
  static InputDecoration inputDecoration({required String label, IconData? icon}) =>
      InputDecoration(
        labelText: label,
        prefixIcon: icon != null ? Icon(icon, size: 20, color: textSecondary) : null,
        filled: true,
        fillColor: background,
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: border),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: border),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: primary, width: 2),
        ),
        labelStyle: GoogleFonts.outfit(color: textSecondary, fontSize: 14),
      );

  // Status badge
  static Color statusColor(String status) => switch (status.toLowerCase()) {
    'active' || 'present' || 'paid' || 'pass' => success,
    'pending' || 'draft' || 'late'            => warning,
    'inactive' || 'absent' || 'fail'          => error,
    _ => textSecondary,
  };
}
