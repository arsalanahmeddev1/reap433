import 'package:get/get.dart';
import 'package:shared_preferences/shared_preferences.dart';

class FontSizeService extends GetxService {
  static FontSizeService get to => Get.find<FontSizeService>();
  static const String _key = 'font_scale';

  // small=0.9, medium=1.0, large=1.1
  final scale = 1.0.obs;

  Future<FontSizeService> init() async {
    final prefs = await SharedPreferences.getInstance();
    scale.value = prefs.getDouble(_key) ?? 1.0;
    return this;
  }

  Future<void> setScale(double value) async {
    scale.value = value;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setDouble(_key, value);
  }

  /// current selected label ('small'|'medium'|'large')
  String get currentKey {
    if (scale.value <= 0.9) return 'small';
    if (scale.value >= 1.1) return 'large';
    return 'medium';
  }
}