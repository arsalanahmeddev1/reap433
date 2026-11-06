import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/auth/login/login_view.dart';
import 'package:reap433/views/auth/register/register_view.dart';
import 'package:reap433/widgets/common_button.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

class WelcomeView extends StatelessWidget {
  const WelcomeView({super.key});
  @override
  Widget build(BuildContext context) {
    final h = MediaQuery.of(context).size.height;

    return Scaffold(
      backgroundColor: Colors.black,
      body: SafeArea(
        child: Padding(
          padding: EdgeInsets.symmetric(
            horizontal: ScreenConstants.horizontalPadding,
            vertical: ScreenConstants.verticalPadding,
          ),
          child: Column(
            children: [
              SizedBox(height: h * 0.05),

              /// LOGO
              Image.asset(
                'assets/png/app_logo.png',
                width: 233,
              ),
              GradientText(
                child: CustomText2(
                  text: 'welcome_to'.tr,
                  fontSize: 26,
                  weight: FontWeight.w600,
                  color: Colors.white,
                  textAlign: TextAlign.center,
                  lineSpacing: 0.5,
                ),
              ),
              SizedBox(height: 5),
              CustomText2(
                text: 'app_name'.tr,
                fontSize: 50,
                weight: FontWeight.w600,
                color: Color(0xffF2E1C7),
                textAlign: TextAlign.center,
                letterSpacing: -1,
                lineSpacing: 1,
              ),
              SizedBox(height: 5),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 40),
                child: CustomText(
                  text: 'welcome_subtitle'.tr,
                  fontSize: 18,
                  weight: FontWeight.w500,
                  color: Color(0xff43464C),
                  textAlign: TextAlign.center,
                ),
              ),

              const Spacer(),
              CommonButton(
                title: 'login'.tr,
                textColor: Colors.black,
                onPressed: () {
                  Get.to(LoginView());
                },
              ),
              const SizedBox(height: 12),
              CommonButton(
                title: 'registered'.tr,
                isTransparent: true,
                borderColor:ColorConstants.borderColor,
                textColor: ColorConstants.textColor,
                onPressed: () {
                  Get.to(RegisterView());
                },
              ),

              const SizedBox(height: 10),
            ],
          ),
        ),
      ),
    );
  }
}