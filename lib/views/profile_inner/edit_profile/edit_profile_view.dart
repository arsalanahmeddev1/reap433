import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/profile/profile_controller.dart';
import 'package:reap433/views/profile_inner/change_password/change_password_view.dart';
import 'package:reap433/widgets/common_button.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/custom_textfield.dart';
import 'package:reap433/widgets/gradient_text.dart';

class EditProfileView extends StatelessWidget {
  const EditProfileView({super.key});

  @override
  Widget build(BuildContext context) {
    final controller = Get.find<ProfileController>();

    // screen khulte hi prefill
    WidgetsBinding.instance.addPostFrameCallback((_) {
      controller.prefillEditProfile();
    });

    final provider = controller.user.value?.provider?.toLowerCase() ?? '';
    final isSocialLogin = provider == 'google' || provider == 'facebook';

    const prefixPad = EdgeInsets.only(left: 25, right: 10, top: 12, bottom: 12);

    return Scaffold(
      body: Stack(
        children: [
          SafeArea(
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
                            text: 'profile'.tr,
                            fontSize: 40,
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
          
                          /// AVATAR + NAME + EMAIL card
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 18,
                              vertical: 20,
                            ),
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(
                                color: ColorConstants.borderColor,
                                width: 0.5,
                              ),
                              gradient: const LinearGradient(
                                begin: Alignment.topLeft,
                                end: Alignment.bottomRight,
                                colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
                              ),
                            ),
                            child: Row(
                              children: [
                                /// avatar + edit badge — image select ya current
                                GestureDetector(
                                  onTap: () => controller.pickProfileImage(context),
                                  child: Stack(
                                    clipBehavior: Clip.none,
                                    children: [
                                      Obx(
                                        () => Container(
                                          width: 72,
                                          height: 72,
                                          decoration: BoxDecoration(
                                            shape: BoxShape.circle,
                                            border: Border.all(
                                              color: ColorConstants.gradientColor1,
                                              width: 2,
                                            ),
                                          ),
                                          child: ClipOval(
                                            child: _avatarImage(controller),
                                          ),
                                        ),
                                      ),
                                      Positioned(
                                        right: 2,
                                        bottom: 0,
                                        child: Container(
                                          width: 22,
                                          height: 22,
                                          decoration: BoxDecoration(
                                            shape: BoxShape.circle,
                                            gradient: LinearGradient(
                                              begin: Alignment.topCenter,
                                              end: Alignment.bottomCenter,
                                              colors: [
                                                ColorConstants.gradientColor1,
                                                ColorConstants.gradientColor2,
                                              ],
                                            ),
                                          ),
                                          child: Center(
                                            child: SvgPicture.asset(
                                              'assets/svg/edit.svg',
                                              height: 13,
                                              color: Colors.black,
                                            ),
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                const SizedBox(width: 18),
          
                                /// name + email (level ki jagah email)
                                Expanded(
                                  child: Obx(
                                    () => Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                        CustomText2(
                                          text: controller.user.value?.name ?? '',
                                          fontSize: 28,
                                          weight: FontWeight.w600,
                                          color: Colors.white,
                                          lineSpacing: 1,
                                          letterSpacing: -1,
                                        ),
                                        const SizedBox(height: 2),
                                        GradientText(
                                          child: CustomText(
                                            text: controller.user.value?.email ?? '',
                                            fontSize: 14,
                                            weight: FontWeight.w500,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
          
                          const SizedBox(height: 28),
          
                          /// FULL NAME
                          CustomText(
                            text: 'full_name'.tr,
                            fontSize: 16,
                            weight: FontWeight.w500,
                            color: Colors.white,
                          ),
                          const SizedBox(height: 5),
                          CustomTextField(
                            controller: controller.nameController,
                            hintText: 'full_name_hint'.tr,
                            prefixIcon: Padding(
                              padding: prefixPad,
                              child: SvgPicture.asset('assets/svg/user.svg'),
                            ),
                          ),
          
                          if (!isSocialLogin) ...[
                          const SizedBox(height: 18),
                          GestureDetector(
                            onTap: () => Get.to(() => const ChangePasswordView()),
                            child: Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 18,
                                vertical: 18,
                              ),
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(
                                  color: ColorConstants.borderColor,
                                  width: 0.5,
                                ),
                                gradient: const LinearGradient(
                                  begin: Alignment.topLeft,
                                  end: Alignment.bottomRight,
                                  colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
                                ),
                              ),
                              child: Row(
                                children: [
                                  Container(
                                    width: 37,
                                    height: 37,
                                    decoration: const BoxDecoration(
                                      shape: BoxShape.circle,
                                      color: Color(0xff211B14),
                                    ),
                                    child: Center(
                                      child: SvgPicture.asset(
                                        'assets/svg/lock.svg',
                                        height: 16,
                                        color: ColorConstants.gradientColor1,
                                      ),
                                    ),
                                  ),
                                  const SizedBox(width: 14),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                        CustomText(
                                          text: 'change_password'.tr,
                                          fontSize: 16,
                                          weight: FontWeight.w500,
                                          color: Colors.white,
                                        ),
                                        CustomText(
                                          text: 'change_password_sub'.tr,
                                          fontSize: 14,
                                          weight: FontWeight.w400,
                                          color: ColorConstants.textColor,
                                        ),
                                      ],
                                    ),
                                  ),
                                  SvgPicture.asset(
                                    'assets/svg/deck_arrow.svg',
                                    height: 12,
                                  ),
                                ],
                              ),
                            ),
                          )],
          
                          const SizedBox(height: 20),
          
                          /// SAVE CHANGES
                          Obx(
                            () => CommonButton(
                              title: 'save_changes'.tr,
                              textColor: Colors.black,
                              isLoading: controller.isUpdating.value,
                              onPressed: () => controller.updateProfile(context),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
          
                  /// DELETE ACCOUNT (baad me implement)
                  CommonButton(
                    title: 'delete_account'.tr,
                    isTransparent: true,
                    borderColor: ColorConstants.borderColor,
                    textColor: ColorConstants.textColor,
                    onPressed: () => _showDeleteDialog(context, controller),
                  ),
                  const SizedBox(height: 8),
                ],
              ),
            ),
          ),

          Obx(() {
            if (!controller.isDeleting.value) return const SizedBox.shrink();
            return Container(
              color: Colors.black.withValues(alpha: 0.6),
              child: Center(
                child: SizedBox(
                  width: 40,
                  height: 40,
                  child: CircularProgressIndicator(
                    strokeWidth: 3,
                    valueColor: AlwaysStoppedAnimation(
                      ColorConstants.gradientColor1,
                    ),
                  ),
                ),
              ),
            );
          }),
        ],
      ),
    );
  }

  /// avatar — nayi selected image / current network / placeholder
  Widget _avatarImage(ProfileController c) {
    final localPath = c.newProfileImagePath.value;
    if (localPath != null && localPath.isNotEmpty) {
      return Image.file(File(localPath), fit: BoxFit.cover, width: 72, height: 72);
    }
    final url = c.user.value?.profileImage;
    if (url != null && url.isNotEmpty) {
      return Image.network(
        url,
        fit: BoxFit.cover,
        width: 72,
        height: 72,
        errorBuilder: (_, __, ___) => Image.asset(
          'assets/png/avator_placeholder.png',
          fit: BoxFit.fill,
        ),
      );
    }
    return Image.asset('assets/png/avator_placeholder.png', fit: BoxFit.fill);
  }

  void _showDeleteDialog(BuildContext context, ProfileController controller) {
  Get.dialog(
    Dialog(
      backgroundColor: Colors.transparent,
      insetPadding: const EdgeInsets.symmetric(horizontal: 30),
      child: Container(
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: ColorConstants.borderColor, width: 0.5),
          gradient: const LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
          ),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            GradientText(
              child: CustomText2(
                text: 'delete_account'.tr,
                fontSize: 26,
                weight: FontWeight.w600,
                color: Colors.white,
                letterSpacing: -1,
              ),
            ),
            const SizedBox(height: 12),
            CustomText(
              text: 'delete_account_confirm'.tr,
              fontSize: 14,
              weight: FontWeight.w400,
              color: ColorConstants.textColor,
              textAlign: TextAlign.center,
              lineSpacing: 1.3,
            ),
            const SizedBox(height: 24),
            Row(
              children: [
                /// CANCEL
                Expanded(
                  child: CommonButton(
                    title: 'cancel'.tr,
                    isTransparent: true,
                    borderColor: ColorConstants.borderColor,
                    textColor: ColorConstants.textColor,
                    onPressed: () => Get.back(),
                    verticalPadding: 8,
                  ),
                ),
                const SizedBox(width: 12),
                /// DELETE
                Expanded(
                  child: CommonButton(
                    title: 'delete'.tr,
                    textColor: Colors.black,
                    verticalPadding: 8,
                    onPressed: () {
                      Get.back(); // dialog band
                      controller.deleteAccount(context);
                    },
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    ),
    barrierDismissible: true,
    barrierColor: Colors.black.withValues(alpha: 0.7),
  );
}
}