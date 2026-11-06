import 'package:firebase_core/firebase_core.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:reap433/firebase_options.dart';
import 'package:reap433/localization/app_translation.dart';
import 'package:reap433/services/firebase_messaging_service.dart';
import 'package:reap433/services/fontsize_service.dart';
import 'package:reap433/services/local_notification_service.dart';
import 'package:reap433/services/localization_service.dart';
import 'package:reap433/services/sound_service.dart';
import 'package:reap433/services/storage_service.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/auth/auth_controller.dart';
import 'package:reap433/views/bottom_navigation/bottom_navigation_controller.dart';
import 'package:reap433/views/quiz/quiz_controller.dart';
import 'package:reap433/views/splash/splash_view.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // ===== ye zaroori hain app khulne se pehle (fast hain) =====
  await GetStorage.init();
  await StorageService.init();
  await Get.putAsync(() => FontSizeService().init());
  await Get.putAsync(() => SoundService().init());
  final startLocale = await LocalizationService.getStartLocale();

  await Firebase.initializeApp(
    options: DefaultFirebaseOptions.currentPlatform,
  );
  runApp(MyApp(startLocale: startLocale));
  _initNotifications();
}

Future<void> _initNotifications() async {
  try {
    final localNotificationsService = LocalNotificationsService.instance();
    await localNotificationsService.init();

    final firebaseMessagingService = FirebaseMessagingService.instance();
    await firebaseMessagingService.init(
      localNotificationsService: localNotificationsService,
    );
  } catch (_) {
  }
}

class MyApp extends StatelessWidget {
  final Locale startLocale;
  const MyApp({super.key, required this.startLocale});

  @override
  Widget build(BuildContext context) {
    SystemChrome.setPreferredOrientations([DeviceOrientation.portraitUp]);
    return GetMaterialApp(
      debugShowCheckedModeBanner: false,
      initialBinding: InitialBindings(),
      translations: AppTranslations(),
      locale: startLocale,
      fallbackLocale: LocalizationService.fallbackLocale,
      theme: ThemeData(scaffoldBackgroundColor: ColorConstants.blackColor),
      home: SplashView(),
      title: 'Reap433',
    );
  }
}

class InitialBindings implements Bindings {
  @override
  void dependencies() {
    Get.put(AuthController(), permanent: true);
    Get.put(BottomNavbarController(), permanent: true);
    Get.put(QuizController(), permanent: true);
  }
}