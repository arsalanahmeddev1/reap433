import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/utils/lottie_preloader.dart';
import 'package:reap433/views/splash/splash_controller.dart';

class SplashView extends StatefulWidget {
  const SplashView({super.key});

  @override
  State<SplashView> createState() => _SplashViewState();
}

class _SplashViewState extends State<SplashView> {

  @override
  Widget build(BuildContext context) {
    final SplashController splashController = Get.put(SplashController());
    Future.delayed(Duration(seconds: 2), () {
      splashController.checkFirstTime();
    });
    return Scaffold(
      backgroundColor: ColorConstants.blackColor,
      body: Stack(
        children: [
          Center(
            child: Image.asset(
              'assets/png/app_logo.png',
              width: 275,
              height: 141,
            ),
          ),
        ],
      ),
    );
  }
}
