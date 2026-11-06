import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:reap433/services/api_service.dart';
import 'package:reap433/services/localization_service.dart';
import 'package:reap433/services/storage_service.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/auth/login/login_view.dart';

class SettingController extends GetxController {
  final _api = ApiService();

  final isLoggingOut = false.obs;

  final currentLanguage = 'English'.obs;

  Future<void> loadPreferences() async {
    final locale = await LocalizationService.getStartLocale();
    currentLanguage.value = _langName(locale.languageCode);
  }

  String _langName(String code) {
    switch (code) {
      case 'es':
        return 'Spanish';
      case 'fr':
        return 'French';
      case 'en':
      default:
        return 'English';
    }
  }

  // ================= LOGOUT =================
  Future<void> logout(BuildContext context) async {
    isLoggingOut.value = true;
    try {
      final response = await _api.postApi(
        '${AppConstants.baseUrl}${AppConstants.logout}',
        {},
      );
      await StorageService.removeToken();

      if (response['code'] == 200 || response['code'] == 201) {
        Get.offAll(() => const LoginView());
      } else {
        Get.offAll(() => const LoginView());
      }
    } catch (e) {
      await StorageService.removeToken();
      Get.offAll(() => const LoginView());
    } finally {
      isLoggingOut.value = false;
    }
  }
}