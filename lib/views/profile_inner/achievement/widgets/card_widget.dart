import 'package:flutter/material.dart';
import 'package:flutter_svg/svg.dart';
import 'package:get/get.dart';
import 'package:reap433/models/achievements_model.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/profile_inner/achievement/achievement_controller.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

Widget card(AchievementController c, Achievement a) {
  return Container(
    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
    decoration: BoxDecoration(
      borderRadius: BorderRadius.circular(12),
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
          width: 59,
          height: 59,
          decoration: const BoxDecoration(
            shape: BoxShape.circle,
            color: Color(0xff211B14),
          ),
          child: Center(
            child: ClipOval(
              child: Padding(
                padding: const EdgeInsets.all(18),
                child: Image.network(
                  a.imageUrl ?? '',
                  height: 20,
                  fit: BoxFit.contain,
                  errorBuilder: (_, __, ___) => SvgPicture.asset(
                    'assets/svg/achievement.svg',
                    height: 20,
                    color: ColorConstants.gradientColor1,
                  ),
                ),
              ),
            ),
          ),
        ),
        const SizedBox(width: 14),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              CustomText2(
                text: a.title ?? '',
                fontSize: 22,
                weight: FontWeight.w400,
                color: Colors.white,
                lineSpacing: 1,
              ),
              CustomText(
                text: a.description ?? '',
                fontSize: 14,
                weight: FontWeight.w400,
                color: ColorConstants.textColor,
              ),
              const SizedBox(height: 4),
              Row(
                children: [
                  GradientText(
                    child: CustomText(
                      text: '+${a.xp ?? 0} ${'xp'.tr}',
                      fontSize: 14,
                      weight: FontWeight.w400,
                    ),
                  ),
                  const SizedBox(width: 6),
                  SvgPicture.asset('assets/svg/small_coin.svg', height: 10),
                  const SizedBox(width: 6),
                  GradientText(
                    child: CustomText(
                      text: '+${a.coins ?? 0} ${'coins'.tr}',
                      fontSize: 14,
                      weight: FontWeight.w400,
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
        const SizedBox(width: 10),
        _stateWidget(c, a),
      ],
    ),
  );
}

Widget _stateWidget(AchievementController c, Achievement a) {
  final completed = (a.isCompleted ?? 0) == 1;
  final claimed = a.isClaim == true;

  // not completed → locked
  if (!completed) {
    return Container(
      width: 35,
      height: 35,
      decoration: const BoxDecoration(
        shape: BoxShape.circle,
        color: Color(0xff352D24),
      ),
      child: Center(
        child: SvgPicture.asset(
          'assets/svg/lock.svg',
          height: 15,
          color: ColorConstants.textColor,
        ),
      ),
    );
  }

  // claimed → gold tick
  if (claimed) {
    return Container(
      width: 33,
      height: 33,
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
          'assets/svg/check.svg',
          height: 13,
          color: Colors.black,
        ),
      ),
    );
  }

  return Obx(() {
    final isClaiming = c.claimingId.value == a.id;
    return GestureDetector(
      onTap: isClaiming ? null : () => c.claim(a),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8),
          gradient: GradientText.goldGradient,
        ),
        child: isClaiming
            ? const SizedBox(
                width: 18,
                height: 18,
                child: CircularProgressIndicator(
                  strokeWidth: 2,
                  valueColor: AlwaysStoppedAnimation(Colors.black),
                ),
              )
            : CustomText(
                text: 'claim'.tr,
                fontSize: 12,
                weight: FontWeight.w600,
                color: Colors.black,
              ),
      ),
    );
  });
}