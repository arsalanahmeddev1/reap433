import 'package:get/get.dart';
import 'package:reap433/services/storage_service.dart';

class SoundService extends GetxService {
  static SoundService get to => Get.find<SoundService>();
  static const String _key = 'sound_enabled';

  final soundOn = true.obs;

  Future<SoundService> init() async {
    final saved = StorageService.getString(_key);
    if (saved == null) {
      soundOn.value = true;
    } else {
      soundOn.value = saved == 'true';
    }
    return this;
  }

  Future<void> toggle(bool value) async {
    soundOn.value = value;
    await StorageService.setString(_key, value.toString());
  }
}