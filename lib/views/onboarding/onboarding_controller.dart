import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:reap433/views/auth/welcome/welcome_view.dart';
import 'package:shared_preferences/shared_preferences.dart';

class OnBoardingController extends GetxController {
  var isOnboardingCompleted = false.obs;
  var currentIndex = 0.obs;

  final pageController = PageController();

    final images = [
    "assets/png/onboarding_1.png",
    "assets/png/onboarding_2.png",
    "assets/png/onboarding_3.png",
  ];

  /// Translation keys (text app_translations.dart mein hai)
  final labelKeys = ['onb1_label', 'onb2_label', 'onb3_label'];
  final titleKeys = ['onb1_title', 'onb2_title', 'onb3_title'];
  final descKeys = ['onb1_desc', 'onb2_desc', 'onb3_desc'];

  void onPageChanged(int index) => currentIndex.value = index;

  void next() {
    if (currentIndex.value < titleKeys.length - 1) {
      pageController.nextPage(
        duration: const Duration(milliseconds: 350),
        curve: Curves.easeInOut,
      );
    } else {
      completeOnboardingSignUp();
    }
  }

  void skip() => completeOnboardingSignUp();

  @override
  void onInit() {
    super.onInit();
    _checkOnboardingStatus();
  }

  Future<void> _checkOnboardingStatus() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    isOnboardingCompleted.value =
        prefs.getBool('onboarding_completed') ?? false;

    if (isOnboardingCompleted.value) {
      Get.off(WelcomeView());
    }
  }

  Future<void> completeOnboardingSignUp() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setBool('onboarding_completed', true);
    isOnboardingCompleted.value = true;
    print('onboarding completed');
    Get.offAll(WelcomeView());
  }

  @override
  void onClose() {
    pageController.dispose();
    super.onClose();
  }
}