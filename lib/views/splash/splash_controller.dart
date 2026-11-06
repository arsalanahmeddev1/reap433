import 'package:get/get.dart';
import 'package:reap433/services/storage_service.dart';
import 'package:reap433/views/auth/welcome/welcome_view.dart';
import 'package:reap433/views/bottom_navigation/bottom_navigation_view.dart';
import 'package:reap433/views/onboarding/onboarding_view.dart';

class SplashController extends GetxController {
  var isFirstTime = true.obs;
  var isLoggedIn = false.obs;

  Future<void> completeOnboarding() async {
    await StorageService.setBool('onboarding_completed', false);
  }

  Future<void> checkFirstTime() async {
    isFirstTime.value = StorageService.getBool('onboarding_completed');
    isLoggedIn.value = StorageService.getToken().isNotEmpty;

    navigateBasedOnState();
  }

  void navigateBasedOnState() {
    print(isLoggedIn.value);
    print(isFirstTime.value);
    if (isLoggedIn.value) {
      Get.offAll(BottomNavigation());
    } else if (isFirstTime.value == false) {
      Get.offAll(OnBoardingView());
    } else {
      Get.offAll(WelcomeView());
    }
  }
}
