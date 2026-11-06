import 'dart:ui';
import 'package:get/get.dart';
import 'package:shared_preferences/shared_preferences.dart';

class LocalizationService {
  static const String _key = 'app_language';
  static const Locale fallbackLocale = Locale('en');

  /// Language selector isi list se banega (backend /api/languages se bhi la sakte ho)
  static const List<Map<String, String>> languages = [
    {'code': 'en', 'name': 'English'},
    {'code': 'es', 'name': 'Spanish'},
    {'code': 'fr', 'name': 'French'},
  ];

  static Future<Locale> getStartLocale() async {
    final prefs = await SharedPreferences.getInstance();
    final code = prefs.getString(_key);
    return code != null ? Locale(code) : fallbackLocale;
  }

  static Future<void> changeLocale(String code) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_key, code);
    Get.updateLocale(Locale(code));
  }
}