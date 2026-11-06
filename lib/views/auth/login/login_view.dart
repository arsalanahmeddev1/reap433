import 'package:flutter/material.dart';
import 'package:flutter_svg/svg.dart';
import 'package:get/get.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/auth/auth_controller.dart';
import 'package:reap433/views/auth/forgot_password/forgot_pass_step_1/forgot_pass_step_1_view.dart';
import 'package:reap433/views/auth/register/register_view.dart';
import 'package:reap433/widgets/common_button.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/custom_textfield.dart';
import 'package:reap433/widgets/gradient_text.dart';

class LoginView extends StatelessWidget {
  const LoginView({super.key});

  @override
  Widget build(BuildContext context) {
    final controller = Get.put(AuthController());

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
                /// TOP BAR — back (left) + title (center)
                SizedBox(
                  width: double.infinity,
                  height: 48,
                  child: Stack(
                    alignment: Alignment.center,
                    children: [
                      GradientText(
                        child: CustomText2(
                          text: 'login'.tr,
                          fontSize: 40,
                          weight: FontWeight.w600,
                          color: Colors.white,
                          letterSpacing: -1,
                        ),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 50),

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
                      left: 25,
                      right: 10,
                      top: 12,
                      bottom: 12,
                    ),
                    child: SvgPicture.asset('assets/svg/email.svg'),
                  ),
                ),

                const SizedBox(height: 15),

                /// PASSWORD
                CustomText(
                  text: 'password_label'.tr,
                  letterSpacing: -1,
                  fontSize: 16,
                  weight: FontWeight.w500,
                  color: ColorConstants.textColor,
                ),
                const SizedBox(height: 10),
                Obx(
                  () => CustomTextField(
                    controller: controller.passwordController,
                    hintText: 'password_hint'.tr,
                    obscureText: controller.obscurePassword.value,
                    prefixIcon: Padding(
                      padding: const EdgeInsets.only(
                        left: 25,
                        right: 10,
                        top: 12,
                        bottom: 12,
                      ),
                      child: SvgPicture.asset('assets/svg/lock.svg'),
                    ),
                    suffixIcon: GestureDetector(
                      onTap: controller.toggleObscure,
                      child: Padding(
                        padding: const EdgeInsets.only(
                          left: 10,
                          right: 25,
                          top: 12,
                          bottom: 12,
                        ),
                        child: SvgPicture.asset(
                          controller.obscurePassword.value
                              ? 'assets/svg/eye_off.svg'
                              : 'assets/svg/eye.svg',
                        ),
                      ),
                    ),
                  ),
                ),

                const SizedBox(height: 10),

                /// FORGOT PASSWORD (right)
                Align(
                  alignment: Alignment.centerRight,
                  child: GestureDetector(
                    onTap: () {
                      Get.to(ForgotPassStep1View());
                    },
                    child: CustomText(
                      text: 'forgot_password'.tr,
                      fontSize: 15,
                      weight: FontWeight.w500,
                      color: ColorConstants.textColor,
                    ),
                  ),
                ),

                const SizedBox(height: 25),

                /// LOGIN BUTTON
                Obx(
                  () => CommonButton(
                    onPressed: () => controller.login(context),
                    title: 'login'.tr,
                    textColor: Colors.black,
                    isLoading: controller.isLoading.value,
                  ),
                ),

                const SizedBox(height: 35),

                /// OR CONTINUE WITH (divider)
                Row(
                  children: [
                    Expanded(
                      child: Container(
                        height: 1,
                        color: ColorConstants.borderColor.withValues(
                          alpha: 0.8,
                        ),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 12),
                      child: CustomText(
                        text: 'or_continue_with'.tr,
                        fontSize: 16,
                        weight: FontWeight.w500,
                        color: ColorConstants.textColor,
                      ),
                    ),
                    Expanded(
                      child: Container(
                        height: 1,
                        color: ColorConstants.borderColor.withValues(
                          alpha: 0.8,
                        ),
                      ),
                    ),
                  ],
                ),

                const SizedBox(height: 35),

                /// CONTINUE WITH GOOGLE
                CommonButton(
                  title: 'continue_google'.tr,
                  isTransparent: true,
                  borderColor: ColorConstants.borderColor,
                  textColor: ColorConstants.textColor,
                  fontSize: 16,
                  textWeight: FontWeight.w500,
                  icon: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 8),
                    child: Image.asset('assets/png/google.png', height: 22),
                  ),
                  onPressed: () => controller.googleLogin(context),
                ),

                const SizedBox(height: 14),

                /// CONTINUE WITH FACEBOOK
                // CommonButton(
                //     title: 'continue_facebook'.tr,
                //     isTransparent: true,
                //     borderColor: ColorConstants.borderColor,
                //     textColor: ColorConstants.textColor,
                //     fontSize: 16,
                //     textWeight: FontWeight.w500,
                //     icon: Padding(
                //       padding: const EdgeInsets.symmetric(horizontal: 8),
                //       child: Image.asset('assets/png/facebook.png', height: 22),
                //     ),
                //     onPressed: () =>
                //         controller.facebookLogin(context), // 👈 ye add
                //   ),

                const SizedBox(height: 35),

                Center(
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      CustomText(
                        text: 'no_account'.tr,
                        fontSize: 16,
                        weight: FontWeight.w500,
                        color: ColorConstants.textColor,
                      ),
                      GestureDetector(
                        onTap: () {
                          Get.to(RegisterView());
                        },
                        child: CustomText(
                          text: 'registered'.tr,
                          fontSize: 16,
                          weight: FontWeight.w700,
                          color: ColorConstants.textColor,
                        ),
                      ),
                    ],
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
