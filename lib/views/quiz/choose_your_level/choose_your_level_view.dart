import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/models/quiz_types_model.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/quiz/quiz_controller.dart';
import 'package:reap433/views/quiz/widgets/level_skeleton_loading.dart';
import 'package:reap433/widgets/common_button.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

class ChooseYourLevelView extends StatelessWidget {
  const ChooseYourLevelView({super.key});

  Map<String, dynamic> _style(String? slug) {
    switch (slug) {
      case 'intermediate':
        return {
          'icon': 'assets/png/clover.png',
          'badgeColor': const Color(0xff856E55),
          'textColor': Colors.white,
        };
      case 'expert':
        return {
          'icon': 'assets/png/crown.png',
          'badgeColor': const Color(0xffDFB76E),
          'textColor': Colors.black,
        };
      case 'beginner':
      default:
        return {
          'icon': 'assets/png/leaf.png',
          'badgeColor': const Color(0xff211B14),
          'textColor': Colors.white,
        };
    }
  }

  @override
  Widget build(BuildContext context) {
    final controller = Get.find<QuizController>();
    controller.fetchQuizTypes();
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
                        text: 'choose_difficulty'.tr,
                        fontSize: 40,
                        weight: FontWeight.w600,
                        color: Colors.white,
                        letterSpacing: -1,
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 5),

              /// SUBTITLE
              Center(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  child: CustomText(
                    text: 'choose_difficulty_sub'.tr,
                    fontSize: 14,
                    weight: FontWeight.w400,
                    color: ColorConstants.textColor,
                    textAlign: TextAlign.center,
                    lineSpacing: 1.3,
                  ),
                ),
              ),

              const SizedBox(height: 28),

              /// LEVEL CARDS
              Expanded(
                child: Obx(() {
                  if (controller.isQuizTypesLoading.value) {
                    return const ChooseLevelSkeletonLoading(); 
                  }
                  if (controller.quizTypes.isEmpty) {
                    return Center(
                      child: CustomText(
                        text: 'No difficulty levels found.',
                        fontSize: 14,
                        weight: FontWeight.w400,
                        color: ColorConstants.textColor,
                      ),
                    );
                  }
                  return ListView.separated(
                    itemCount: controller.quizTypes.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 18),
                    itemBuilder: (_, i) {
                      final QuizType type = controller.quizTypes[i];
                      final style = _style(type.slug);
                      return Obx(() {
                        final isSelected =
                            controller.selectedQuizTypeIndex.value == i;
                        return GestureDetector(
                          onTap: () => controller.selectQuizType(i),
                          child: AnimatedContainer(
                            duration: const Duration(milliseconds: 200),
                            padding: const EdgeInsets.symmetric(
                              horizontal: 16,
                              vertical: 12,
                            ),
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(12),
                              gradient: const LinearGradient(
                                begin: Alignment.topLeft,
                                end: Alignment.bottomRight,
                                colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
                              ),
                              border: Border.all(
                                color: ColorConstants.borderColor,
                                width: isSelected ? 1 : 0.5,
                              ),
                              boxShadow: isSelected
                                  ? [
                                      BoxShadow(
                                        color: ColorConstants.gradientColor1
                                            .withValues(alpha: 0.35),
                                        blurRadius: 16,
                                        spreadRadius: 1,
                                      ),
                                    ]
                                  : [],
                            ),
                            child: Row(
                              children: [
                                /// icon circle
                                Container(
                                  width: 67,
                                  height: 67,
                                  decoration: BoxDecoration(
                                    shape: BoxShape.circle,
                                    border: Border.all(
                                      color: ColorConstants.gradientColor1
                                          .withValues(alpha: 0.6),
                                    ),
                                  ),
                                  child: Center(
                                    child: Image.asset(
                                      style['icon'] as String,
                                      height: 36,
                                      width: 42,
                                    ),
                                  ),
                                ),
                                const SizedBox(width: 16),

                                /// texts (API se)
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      CustomText2(
                                        text: type.title ?? '',
                                        fontSize: 24,
                                        weight: FontWeight.w400,
                                        color: Colors.white,
                                        letterSpacing: -1,
                                      ),
                                      CustomText(
                                        text: type.description ?? '',
                                        fontSize: 14,
                                        weight: FontWeight.w500,
                                        color: ColorConstants.textColor,
                                      ),
                                      const SizedBox(height: 5),
                                      Container(
                                        padding: const EdgeInsets.symmetric(
                                          horizontal: 14,
                                          vertical: 3,
                                        ),
                                        decoration: BoxDecoration(
                                          color: style['badgeColor'] as Color,
                                          borderRadius:
                                              BorderRadius.circular(20),
                                        ),
                                        child: CustomText(
                                          text: type.sloganText ?? '',
                                          fontSize: 14,
                                          weight: FontWeight.w400,
                                          color: style['textColor'] as Color,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),

                                const SizedBox(width: 40),

                                /// radio
                                Container(
                                  width: 30,
                                  height: 30,
                                  decoration: BoxDecoration(
                                    shape: BoxShape.circle,
                                    color: const Color(0xff1C1812),
                                    border: Border.all(
                                      color: ColorConstants.gradientColor1,
                                      width: 1.5,
                                    ),
                                  ),
                                  child: isSelected
                                      ? Center(
                                          child: SvgPicture.asset(
                                            'assets/svg/check.svg',
                                            height: 10,
                                            color:
                                                ColorConstants.gradientColor1,
                                          ),
                                        )
                                      : null,
                                ),
                              ],
                            ),
                          ),
                        );
                      });
                    },
                  );
                }),
              ),

              SizedBox(height: 15),

              CommonButton(
                title: 'next'.tr,
                textColor: Colors.black,
                onPressed: () => controller.onContinue(),
              ),
            ],
          ),
        ),
      ),
    );
  }
}