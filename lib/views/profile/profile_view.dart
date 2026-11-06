import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/bottom_navigation/bottom_navigation_view.dart';
import 'package:reap433/views/profile/profile_controller.dart';
import 'package:reap433/views/profile/widgets/profile_skeleton_loading.dart';
import 'package:reap433/views/profile_inner/achievement/achievement_view.dart';
import 'package:reap433/views/profile_inner/edit_profile/edit_profile_view.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

class ProfileView extends StatelessWidget {
  const ProfileView({super.key});

  @override
  Widget build(BuildContext context) {
    final controller = Get.put(ProfileController());
    WidgetsBinding.instance.addPostFrameCallback((_) {
      controller.fetchProfile();
    });

    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: EdgeInsets.symmetric(
            horizontal: ScreenConstants.horizontalPadding,
            vertical: ScreenConstants.verticalPadding,
          ),
          child: Obx(() {
            if (controller.isLoading.value) {
              return const ProfileSkeletonLoading();
            }

            final u = controller.user.value;

            return SingleChildScrollView(
              child: Column(
                children: [
                  /// TITLE
                  GradientText(
                    child: CustomText2(
                      text: 'profile'.tr,
                      fontSize: 40,
                      weight: FontWeight.w600,
                      color: Colors.white,
                      letterSpacing: -1,
                    ),
                  ),

                  const SizedBox(height: 15),

                  /// AVATAR
                  GestureDetector(
                    onTap: () {},
                    child: Container(
                      width: 96,
                      height: 96,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border: Border.all(
                          color: ColorConstants.gradientColor1,
                          width: 2,
                        ),
                      ),
                      child: ClipOval(
                        child:
                            (u?.profileImage != null &&
                                u!.profileImage!.isNotEmpty)
                            ? Image.network(
                                u.profileImage!,
                                fit: BoxFit.cover,
                                errorBuilder: (_, __, ___) => Image.asset(
                                  'assets/png/avator_placeholder.png',
                                  fit: BoxFit.fill,
                                ),
                              )
                            : Image.asset(
                                'assets/png/avator_placeholder.png',
                                fit: BoxFit.fill,
                              ),
                      ),
                    ),
                  ),
                  SizedBox(height: 10),

                  /// NAME
                  Padding(
                    padding: EdgeInsets.symmetric(horizontal: 10),
                    child: CustomText2(
                      text: u?.name ?? '',
                      fontSize: 40,
                      weight: FontWeight.w600,
                      textAlign: TextAlign.center,
                      color: Colors.white,
                      lineSpacing: 1,
                      letterSpacing: -1,
                      lines: 2,
                    ),
                  ),
                  const SizedBox(height: 20),

                  /// STAT CARDS (Streak / XP / Coins)
                  Row(
                    children: [
                      Expanded(
                        child: _statCard(
                          'assets/svg/gradient_book.svg',
                          '${u?.totalStreak ?? 0}',
                          'streak'.tr,
                        ),
                      ),
                      const SizedBox(width: 10),
                      Expanded(
                        child: _statCard(
                          'assets/svg/achievement.svg',
                          '${u?.totalXp ?? 0}',
                          'xp'.tr,
                        ),
                      ),
                      const SizedBox(width: 10),
                      Expanded(
                        child: _statCard(
                          'assets/svg/coins.svg',
                          '${u?.totalCoins ?? 0}',
                          'coins'.tr,
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(height: 22),

                  /// MENU ROWS
                  _menuRow(
                    icon: 'assets/svg/user.svg',
                    title: 'edit_profile'.tr,
                    subtitle: 'edit_profile_sub'.tr,
                    onTap: () {
                      Get.to(EditProfileView());
                    },
                  ),
                  const SizedBox(height: 14),
                  _menuRow(
                    icon: 'assets/svg/bottom_nav_5.svg',
                    title: 'account_setting'.tr,
                    subtitle: 'account_setting_sub'.tr,
                    onTap: () {
                      Get.offAll(BottomNavigation(index: 4));
                    },
                  ),
                  const SizedBox(height: 14),
                  _menuRow(
                    icon: 'assets/svg/achievement.svg',
                    title: 'achievements'.tr,
                    subtitle: 'achievements_sub'.tr,
                    onTap: () {
                      Get.to(AchievementView());
                    },
                  ),

                  const SizedBox(height: 20),
                ],
              ),
            );
          }),
        ),
      ),
    );
  }

  Widget _statCard(String icon, String value, String label) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 12),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: ColorConstants.borderColor, width: 0.5),
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
        ),
      ),
      child: Column(
        children: [
          Container(
            width: 59,
            height: 59,
            decoration: const BoxDecoration(
              shape: BoxShape.circle,
              color: Color(0xff211B14),
            ),
            child: Center(child: SvgPicture.asset(icon, height: 22)),
          ),
          const SizedBox(height: 5),
          CustomText(
            text: value,
            fontSize: 20,
            weight: FontWeight.w500,
            color: Colors.white,
          ),
          const SizedBox(height: 2),
          CustomText(
            text: label,
            fontSize: 14,
            weight: FontWeight.w500,
            color: ColorConstants.textColor,
          ),
        ],
      ),
    );
  }

  Widget _menuRow({
    required String icon,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 19),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: ColorConstants.borderColor, width: 0.5),
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
                  icon,
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
                    text: title,
                    fontSize: 16,
                    weight: FontWeight.w500,
                    color: Colors.white,
                    lineSpacing: 1,
                    letterSpacing: -1,
                  ),
                  CustomText(
                    text: subtitle,
                    fontSize: 14,
                    weight: FontWeight.w400,
                    color: ColorConstants.textColor,
                  ),
                ],
              ),
            ),
            SvgPicture.asset('assets/svg/deck_arrow.svg', height: 12),
          ],
        ),
      ),
    );
  }
}
