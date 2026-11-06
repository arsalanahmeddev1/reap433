import 'package:shared_preferences/shared_preferences.dart';

class StorageService {
  static SharedPreferences? _prefs;

  static Future init() async {
    _prefs = await SharedPreferences.getInstance();
  }

  // ================= TOKEN =================

  static Future setToken(String token) async {
    await _prefs?.setString('token', token);
  }

  static String getToken() {
    return _prefs?.getString('token') ?? '';
  }

  static Future removeToken() async {
    await _prefs?.remove('token');
  }

  // ================= BOOL FLAGS =================

  static Future setBool(String key, bool value) async {
    await _prefs?.setBool(key, value);
  }

  static bool getBool(String key) {
    return _prefs?.getBool(key) ?? false;
  }

  static Future removeBool(String key) async {
    await _prefs?.remove(key);
  }

  static Future setString(String key, String value) async {
  await _prefs?.setString(key, value);
}

static String? getString(String key) {
  return _prefs?.getString(key);
}

  // ================= CLEAR ALL =================

  static Future clearAll() async {
    await _prefs?.clear();
  }
}