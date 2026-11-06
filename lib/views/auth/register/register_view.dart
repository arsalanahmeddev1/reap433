import 'dart:io';
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

class RegisterView extends StatelessWidget {
  const RegisterView({super.key});

  @override
  Widget build(BuildContext context) {
    final controller = Get.put(AuthController());

    const prefixPad = EdgeInsets.only(left: 25, right: 10, top: 12, bottom: 12);
    final suffixPad = const EdgeInsets.only(
      left: 10,
      right: 25,
      top: 12,
      bottom: 12,
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
                /// TOP BAR — back (left) + title (center)
                SizedBox(
                  width: double.infinity,
                  height: 48,
                  child: Stack(
                    alignment: Alignment.center,
                    children: [
                      Align(
                        alignment: Alignment.centerLeft,
                        child: Container(
                          margin: const EdgeInsets.only(top: 10),
                          child: GestureDetector(
                            onTap: () => Get.back(),
                            child: SvgPicture.asset(
                              'assets/svg/back_arrow.svg',
                            ),
                          ),
                        ),
                      ),
                      GradientText(
                        child: CustomText2(
                          text: 'registered'.tr,
                          fontSize: 40,
                          weight: FontWeight.w600,
                          color: Colors.white,
                          letterSpacing: -1,
                        ),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 25),

                /// PROFILE PICTURE
                Center(
                  child: GestureDetector(
                    onTap: () => controller.pickProfileImage(context),
                    child: Obx(
                      () => Stack(
                        clipBehavior: Clip.none,
                        children: [
                          Container(
                            width: 82,
                            height: 82,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              border: Border.all(
                                color: ColorConstants.borderColor,
                                width: 1,
                              ),
                            ),
                            child: ClipOval(
                              child: controller.profileImagePath.value == null
                                  ? Image.asset(
                                      'assets/png/avator_placeholder.png',
                                      fit: BoxFit.fill,
                                    )
                                  : Image.file(
                                      File(controller.profileImagePath.value!),
                                      fit: BoxFit.fill,
                                    ),
                            ),
                          ),

                          /// + badge (bottom-right)
                          Positioned(
                            right: 4,
                            bottom: 5,
                            child: Container(
                              width: 20,
                              height: 20,
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                gradient: GradientText.goldGradient,
                              ),
                              child: Padding(
                                padding: const EdgeInsets.all(4),
                                child: SvgPicture.asset('assets/svg/add.svg'),
                              )
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),

                const SizedBox(height: 12),

                Center(
                  child: CustomText(
                    text: 'add_profile_picture'.tr,
                    fontSize: 16,
                    weight: FontWeight.w500,
                    color: ColorConstants.textColor,
                  ),
                ),

                const SizedBox(height: 25),

                /// NAME
                CustomTextField(
                  controller: controller.nameController,
                  hintText: 'name_hint'.tr,
                  prefixIcon: Padding(
                    padding: prefixPad,
                    child: SvgPicture.asset('assets/svg/user.svg'),
                  ),
                ),

                const SizedBox(height: 10),

                /// EMAIL
                CustomTextField(
                  controller: controller.emailController,
                  hintText: 'reg_email_hint'.tr,
                  keyboardType: TextInputType.emailAddress,
                  prefixIcon: Padding(
                    padding: prefixPad,
                    child: SvgPicture.asset('assets/svg/email.svg'),
                  ),
                ),

                const SizedBox(height: 10),

                /// PASSWORD
                Obx(
                  () => CustomTextField(
                    controller: controller.passwordController,
                    hintText: 'reg_password_hint'.tr,
                    obscureText: controller.obscurePassword.value,
                    prefixIcon: Padding(
                      padding: prefixPad,
                      child: SvgPicture.asset('assets/svg/lock.svg'),
                    ),
                    suffixIcon: GestureDetector(
                      onTap: controller.toggleObscure,
                      child: Padding(
                        padding: suffixPad,
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

                /// CONFIRM PASSWORD
                Obx(
                  () => CustomTextField(
                    controller: controller.confirmPasswordController,
                    hintText: 'confirm_password_hint'.tr,
                    obscureText: controller.obscureConfirmPassword.value,
                    prefixIcon: Padding(
                      padding: prefixPad,
                      child: SvgPicture.asset('assets/svg/lock.svg'),
                    ),
                    suffixIcon: GestureDetector(
                      onTap: controller.toggleObscureConfirm,
                      child: Padding(
                        padding: suffixPad,
                        child: SvgPicture.asset(
                          controller.obscureConfirmPassword.value
                              ? 'assets/svg/eye_off.svg'
                              : 'assets/svg/eye.svg',
                        ),
                      ),
                    ),
                  ),
                ),

                const SizedBox(height: 10),

                /// REGISTERED BUTTON
                Obx(()=>
                  CommonButton(
                    title: 'registered'.tr,
                    textColor: Colors.black,
                    isLoading: controller.isLoading.value,
                    onPressed: () {
                      controller.signUp(context);
                    },
                  ),
                ),

                const SizedBox(height: 30),

                /// OR (divider)
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
                        text: 'or'.tr,
                        fontSize: 16,
                        weight: FontWeight.w600,
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

                const SizedBox(height: 30),

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
                //   title: 'continue_facebook'.tr,
                //   isTransparent: true,
                //   borderColor: ColorConstants.borderColor,
                //   textColor: ColorConstants.textColor,
                //   fontSize: 16,
                //   textWeight: FontWeight.w500,
                //   icon: Padding(
                //     padding: const EdgeInsets.symmetric(horizontal: 8),
                //     child: Image.asset('assets/png/facebook.png', height: 22),
                //   ),
                //   onPressed: () => controller.facebookLogin(context),
                // ),

                const SizedBox(height: 20),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
