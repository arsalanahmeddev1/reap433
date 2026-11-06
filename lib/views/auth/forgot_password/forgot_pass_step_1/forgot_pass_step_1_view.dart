import 'package:flutter/material.dart';
import 'package:flutter_svg/svg.dart';
import 'package:get/get.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/auth/auth_controller.dart';
import 'package:reap433/widgets/common_button.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/custom_textfield.dart';
import 'package:reap433/widgets/gradient_text.dart';

class ForgotPassStep1View extends StatelessWidget {
  const ForgotPassStep1View({super.key});

  @override
  Widget build(BuildContext context) {
    final controller = Get.find<AuthController>();

    return Scaffold(
      backgroundColor: Colors.black,
      body: SafeArea(
        child: Padding(
          padding: EdgeInsets.symmetric(
            horizontal: ScreenConstants.horizontalPadding,
            vertical: ScreenConstants.verticalPadding,
          ),
          child: SingleChildScrollView(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                /// TOP BAR — back + title
                SizedBox(
                  width: double.infinity,
                  height: 48,
                  child: Stack(
                    alignment: Alignment.center,
                    children: [
                      Align(
                        alignment: Alignment.centerLeft,
                        child: GestureDetector(
                          onTap: () => Get.back(),
                          child: SvgPicture.asset('assets/svg/back_arrow.svg'),
                        ),
                      ),
                      GradientText(
                        child: CustomText2(
                          text: 'forgot_password_title'.tr,
                          fontSize: 34,
                          weight: FontWeight.w600,
                          color: Colors.white,
                          letterSpacing: -1,
                        ),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 30),

                /// SUBTITLE
                CustomText(
                  text: 'forgot_password_sub'.tr,
                  fontSize: 15,
                  weight: FontWeight.w400,
                  color: ColorConstants.textColor,
                  lineSpacing: 1.3,
                ),

                const SizedBox(height: 30),

                /// EMAIL
                CustomText(
                  text: 'email_label'.tr,
                  fontSize: 16,
                  weight: FontWeight.w500,
                  color: ColorConstants.textColor,
                  letterSpacing: -1,
                ),
                const SizedBox(height: 10),
                CustomTextField(
                  controller: controller.emailController,
                  hintText: 'email_hint'.tr,
                  keyboardType: TextInputType.emailAddress,
                  prefixIcon: Padding(
                    padding: const EdgeInsets.only(
                        left: 25, right: 10, top: 12, bottom: 12),
                    child: SvgPicture.asset('assets/svg/email.svg'),
                  ),
                ),

                const SizedBox(height: 30),

                /// SEND OTP BUTTON
                Obx(
                  () => CommonButton(
                    title: 'send_otp'.tr,
                    textColor: Colors.black,
                    isLoading: controller.isLoading.value,
                    onPressed: () => controller.sendForgotOtp(context),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}