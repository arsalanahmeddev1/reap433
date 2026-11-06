import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/profile_inner/achievement/achievement_controller.dart';
import 'package:reap433/views/profile_inner/achievement/widgets/achievement_skeleton_loading.dart';
import 'package:reap433/views/profile_inner/achievement/widgets/card_widget.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

class AchievementView extends StatelessWidget {
  const AchievementView({super.key});

  @override
  Widget build(BuildContext context) {
    final controller = Get.put(AchievementController());

    return Scaffold(
      backgroundColor: Colors.black,
      body: SafeArea(
        child: Padding(
          padding: EdgeInsets.symmetric(
            horizontal: ScreenConstants.horizontalPadding,
            vertical: ScreenConstants.verticalPadding,
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              /// TOP BAR — back + title (hamesha static)
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
                        text: 'achievements'.tr,
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

              /// TABS (All / Earned / Locked) — hamesha static, clickable
              Obx(
                () => Row(
                  children: [
                    Expanded(child: _tab(controller, 0, 'all'.tr, null)),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _tab(
                        controller,
                        1,
                        'earned'.tr,
                        'assets/svg/achievement.svg',
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _tab(
                        controller,
                        2,
                        'locked'.tr,
                        'assets/svg/gradient_lock.svg',
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 20),

              /// LIST — loading pe skeleton, warna real cards
              Expanded(
                child: Obx(() {
                  if (controller.isLoading.value) {
                    return const AchievementSkeletonLoading();
                  }
                  final list = controller.filtered;
                  return ListView.separated(
                    itemCount: list.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 16),
                    itemBuilder: (_, i) => card(controller, list[i]),
                  );
                }),
              ),
            ],
          ),
        ),
      ),
    );
  }

  /// TAB
  Widget _tab(AchievementController c, int index, String label, String? icon) {
    final selected = c.selectedTab.value == index;
    return GestureDetector(
      onTap: () => c.selectTab(index),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 10),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(9),
          gradient: selected ? GradientText.goldGradient : null,
          border: selected
              ? null
              : Border.all(color: ColorConstants.borderColor, width: 0.5),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          mainAxisSize: MainAxisSize.min,
          children: [
            if (!selected && icon != null) ...[
              SvgPicture.asset(icon, height: 18),
              const SizedBox(width: 6),
            ],
            CustomText(
              text: label,
              fontSize: 14,
              weight: FontWeight.w400,
              color: selected ? Colors.black : ColorConstants.gradientColor1,
            ),
          ],
        ),
      ),
    );
  }
}