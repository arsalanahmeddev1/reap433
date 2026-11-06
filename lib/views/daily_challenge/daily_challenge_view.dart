import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/daily_challenge/daily_challenge_controller.dart';
import 'package:reap433/widgets/common_button.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';
import 'package:reap433/widgets/shine_image.dart';

class DailyChallengeView extends StatelessWidget {
final int categoryId;
  final int quizTypeId;
  final int xp;
  final int coins;
  final String resetTime;

  const DailyChallengeView({
    super.key,
    required this.categoryId,
    required this.quizTypeId,
    required this.xp,
    required this.coins,
    required this.resetTime,
  });

  static const Color _cardBorder = Color(0xFF3A3223);

  @override
  Widget build(BuildContext context) {
    final controller = Get.put(DailyChallengeController(
      categoryId: categoryId,
      quizTypeId: quizTypeId,
      xp: xp,
      coins: coins,
      resetTime: resetTime,
    ));

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
                        text: 'daily_challenge'.tr,
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
                  child: Obx(
                    () => Column(
                      children: [
                        const SizedBox(height: 40),

                        /// CALENDAR IMAGE
                        ShineImage(
                         image:'assets/png/calendar.png',
                          height: 180,
                          fit: BoxFit.contain,
                        ),

                        const SizedBox(height: 20),

                        /// TITLE
                        GradientText(
                          child: CustomText2(
                            text: "todays_challenge".tr,
                            fontSize: 40,
                            weight: FontWeight.w600,
                            color: Colors.white,
                            letterSpacing: -1,
                          ),
                        ),
                        /// DESCRIPTION
                        Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 30),
                          child: CustomText(
                            text: 'daily_challenge_desc'.tr,
                            fontSize: 14,
                            weight: FontWeight.w400,
                            color: ColorConstants.textColor,
                            textAlign: TextAlign.center,
                          ),
                        ),

                        const SizedBox(height: 30),

                        /// REWARD CARD
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 13),
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
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              GradientText(
                                child: CustomText(
                                  text: 'reward'.tr,
                                  fontSize: 16,
                                  weight: FontWeight.w700,
                                ),
                              ),
                              const SizedBox(height: 10),
                              Row(
                                children: [
                                  Expanded(
                                    child: _rewardItem(
                                      'assets/svg/coins_earned.svg',
                                      'earned'.tr,
                                      '${controller.rewardXp.value}',
                                    ),
                                  ),
                                  Expanded(
                                    child: _rewardItem(
                                      'assets/svg/xp_earned.svg',
                                      'coin'.tr,
                                      '${controller.rewardCoins.value}',
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),

                        const SizedBox(height: 24),

                        /// START BUTTON
                        CommonButton(
                          title: 'start_challenge'.tr,
                          textColor: Colors.black,
                          onPressed: controller.startChallenge,
                        ),

                        const SizedBox(height: 24),

                        /// RESET TIMER
                        Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            SvgPicture.asset(
                              'assets/svg/time.svg',
                              height: 18,
                              color: ColorConstants.gradientColor1,
                            ),
                            const SizedBox(width: 8),
                            CustomText(
                              text:
                                  '${'challenge_resets_in'.tr} ${controller.resetTimeFormatted}',
                              fontSize: 14,
                              weight: FontWeight.w400,
                              color: ColorConstants.textColor,
                            ),
                          ],
                        ),

                        const SizedBox(height: 20),
                      ],
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  /// reward item (icon circle + label + value)
  Widget _rewardItem(String icon, String label, String value) {
    return Row(
      children: [
        Container(
          width: 43,
          height: 43,
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
          child: Center(child: SvgPicture.asset(icon, height: 20)),
        ),
        const SizedBox(width: 12),
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            CustomText(
              text: label,
              fontSize: 14,
              weight: FontWeight.w400,
              color: ColorConstants.textColor,
            ),
            GradientText(
              child: CustomText(
                text: value,
                fontSize: 20,
                weight: FontWeight.w700,
                color: Colors.white,
              ),
            ),
          ],
        ),
      ],
    );
  }
}