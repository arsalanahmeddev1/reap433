import 'package:flutter/material.dart';
import 'package:flutter_svg/svg.dart';
import 'package:get/get.dart';
import 'package:pinput/pinput.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/auth/auth_controller.dart';
import 'package:reap433/widgets/common_button.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/custom_textfield.dart';
import 'package:reap433/widgets/gradient_text.dart';

class ForgotPassStep2View extends StatelessWidget {
  const ForgotPassStep2View({super.key});

  @override
  Widget build(BuildContext context) {
    final controller = Get.find<AuthController>();

    const prefixPad = EdgeInsets.only(left: 25, right: 10, top: 12, bottom: 12);
    const suffixPad = EdgeInsets.only(left: 10, right: 25, top: 12, bottom: 12);

    // pinput theme — app ke gold/dark theme jaisa
    final defaultPin = PinTheme(
      width: 56,
      height: 56,
      textStyle: const TextStyle(
        fontSize: 20,
        color: Colors.white,
        fontFamily: 'Poppins',
      ),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: ColorConstants.borderColor, width: 0.5),
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
        ),
      ),
    );

    final focusedPin = defaultPin.copyWith(
      decoration: defaultPin.decoration!.copyWith(
        border: Border.all(color: ColorConstants.gradientColor1, width: 1),
      ),
    );

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
                          text: 'reset_password_title'.tr,
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
                  text: 'reset_password_sub'.tr,
                  fontSize: 15,
                  weight: FontWeight.w400,
                  color: ColorConstants.textColor,
                  lineSpacing: 1.3,
                ),

                const SizedBox(height: 30),

                /// OTP
                CustomText(
                  text: 'enter_otp'.tr,
                  fontSize: 16,
                  weight: FontWeight.w500,
                  color: ColorConstants.textColor,
                  letterSpacing: -1,
                ),
                const SizedBox(height: 12),
                Center(
                  child: Pinput(
                    length: 6,
                    controller: controller.otpController,
                    defaultPinTheme: defaultPin,
                    focusedPinTheme: focusedPin,
                    keyboardType: TextInputType.number,
                    separatorBuilder: (_) => const SizedBox(width: 8),
                  ),
                ),

                /// RESEND OTP
                Align(
                  alignment: Alignment.centerRight,
                  child: TextButton(
                    onPressed: () => controller.resendForgotOtp(context),
                    child: CustomText(
                      text: 'resend_otp'.tr,
                      fontSize: 14,
                      weight: FontWeight.w500,
                      color: ColorConstants.gradientColor1,
                    ),
                  ),
                ),

                const SizedBox(height: 20),

                /// NEW PASSWORD
                CustomText(
                  text: 'new_password'.tr,
                  fontSize: 16,
                  weight: FontWeight.w500,
                  color: ColorConstants.textColor,
                  letterSpacing: -1,
                ),
                const SizedBox(height: 10),
                Obx(
                  () => CustomTextField(
                    controller: controller.newPasswordController,
                    hintText: 'new_password_hint'.tr,
                    obscureText: controller.obscureNewPassword.value,
                    prefixIcon: Padding(
                      padding: prefixPad,
                      child: SvgPicture.asset('assets/svg/lock.svg'),
                    ),
                    suffixIcon: GestureDetector(
                      onTap: controller.toggleObscureNewPassword,
                      child: Padding(
                        padding: suffixPad,
                        child: SvgPicture.asset(
                          controller.obscureNewPassword.value
                              ? 'assets/svg/eye_off.svg'
                              : 'assets/svg/eye.svg',
                        ),
                      ),
                    ),
                  ),
                ),

                const SizedBox(height: 15),

                /// CONFIRM PASSWORD
                CustomText(
                  text: 'confirm_password_label'.tr,
                  fontSize: 16,
                  weight: FontWeight.w500,
                  color: ColorConstants.textColor,
                  letterSpacing: -1,
                ),
                const SizedBox(height: 10),
                Obx(
                  () => CustomTextField(
                    controller: controller.confirmNewPasswordController,
                    hintText: 'confirm_password_hint'.tr,
                    obscureText: controller.obscureConfirmNewPassword.value,
                    prefixIcon: Padding(
                      padding: prefixPad,
                      child: SvgPicture.asset('assets/svg/lock.svg'),
                    ),
                    suffixIcon: GestureDetector(
                      onTap: controller.toggleObscureConfirmNewPassword,
                      child: Padding(
                        padding: suffixPad,
                        child: SvgPicture.asset(
                          controller.obscureConfirmNewPassword.value
                              ? 'assets/svg/eye_off.svg'
                              : 'assets/svg/eye.svg',
                        ),
                      ),
                    ),
                  ),
                ),

                const SizedBox(height: 30),

                /// RESET BUTTON
                Obx(
                  () => CommonButton(
                    title: 'reset_password'.tr,
                    textColor: Colors.black,
                    isLoading: controller.isLoading.value,
                    onPressed: () => controller.resetPassword(context),
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