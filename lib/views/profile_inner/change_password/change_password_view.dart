import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/profile/profile_controller.dart';
import 'package:reap433/widgets/common_button.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/custom_textfield.dart';
import 'package:reap433/widgets/gradient_text.dart';

class ChangePasswordView extends StatelessWidget {
  const ChangePasswordView({super.key});

  @override
  Widget build(BuildContext context) {
    final controller = Get.find<ProfileController>();

    const prefixPad = EdgeInsets.only(left: 25, right: 10, top: 12, bottom: 12);
    const suffixPad = EdgeInsets.only(left: 10, right: 25, top: 12, bottom: 12);

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
              /// TOP BAR
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
                        text: 'change_password'.tr,
                        fontSize: 30,
                        weight: FontWeight.w600,
                        color: Colors.white,
                        letterSpacing: -1,
                      ),
                    ),
                  ],
                ),
              ),

              Expanded(
                child: SingleChildScrollView(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const SizedBox(height: 30),

                      /// OLD PASSWORD
                      CustomText(
                        text: 'old_password'.tr,
                        fontSize: 16,
                        weight: FontWeight.w500,
                        color: Colors.white,
                      ),
                      const SizedBox(height: 8),
                      Obx(
                        () => CustomTextField(
                          controller: controller.oldPasswordController,
                          hintText: 'old_password_hint'.tr,
                          obscureText: controller.obscureOld.value,
                          prefixIcon: Padding(
                            padding: prefixPad,
                            child: SvgPicture.asset('assets/svg/lock.svg'),
                          ),
                          suffixIcon: GestureDetector(
                            onTap: controller.toggleOld,
                            child: Padding(
                              padding: suffixPad,
                              child: SvgPicture.asset(
                                controller.obscureOld.value
                                    ? 'assets/svg/eye_off.svg'
                                    : 'assets/svg/eye.svg',
                              ),
                            ),
                          ),
                        ),
                      ),

                      const SizedBox(height: 18),

                      /// NEW PASSWORD
                      CustomText(
                        text: 'new_password'.tr,
                        fontSize: 16,
                        weight: FontWeight.w500,
                        color: Colors.white,
                      ),
                      const SizedBox(height: 8),
                      Obx(
                        () => CustomTextField(
                          controller: controller.changeNewPasswordController,
                          hintText: 'new_password_hint'.tr,
                          obscureText: controller.obscureNew.value,
                          prefixIcon: Padding(
                            padding: prefixPad,
                            child: SvgPicture.asset('assets/svg/lock.svg'),
                          ),
                          suffixIcon: GestureDetector(
                            onTap: controller.toggleNew,
                            child: Padding(
                              padding: suffixPad,
                              child: SvgPicture.asset(
                                controller.obscureNew.value
                                    ? 'assets/svg/eye_off.svg'
                                    : 'assets/svg/eye.svg',
                              ),
                            ),
                          ),
                        ),
                      ),

                      const SizedBox(height: 18),

                      /// CONFIRM PASSWORD
                      CustomText(
                        text: 'confirm_password_label'.tr,
                        fontSize: 16,
                        weight: FontWeight.w500,
                        color: Colors.white,
                      ),
                      const SizedBox(height: 8),
                      Obx(
                        () => CustomTextField(
                          controller: controller.changeConfirmPasswordController,
                          hintText: 'confirm_password_hint'.tr,
                          obscureText: controller.obscureConfirm2.value,
                          prefixIcon: Padding(
                            padding: prefixPad,
                            child: SvgPicture.asset('assets/svg/lock.svg'),
                          ),
                          suffixIcon: GestureDetector(
                            onTap: controller.toggleConfirm2,
                            child: Padding(
                              padding: suffixPad,
                              child: SvgPicture.asset(
                                controller.obscureConfirm2.value
                                    ? 'assets/svg/eye_off.svg'
                                    : 'assets/svg/eye.svg',
                              ),
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),

              /// SAVE BUTTON
              Obx(
                () => CommonButton(
                  title: 'change_password'.tr,
                  textColor: Colors.black,
                  isLoading: controller.isChangingPassword.value,
                  onPressed: () => controller.changePassword(context),
                ),
              ),
              const SizedBox(height: 8),
            ],
          ),
        ),
      ),
    );
  }
}