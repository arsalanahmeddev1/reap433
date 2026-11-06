import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/models/deck_detail_model.dart' as deck;
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/quiz/quiz_controller.dart';
import 'package:reap433/views/quiz/quiz_start/quiz_start_view.dart';
import 'package:reap433/views/quiz/widgets/deck_detail_skeleton_loading.dart';
import 'package:reap433/widgets/common_button.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

class DeckDetailView extends StatelessWidget {
  const DeckDetailView({super.key});

  @override
  Widget build(BuildContext context) {
    final controller = Get.find<QuizController>();

    WidgetsBinding.instance.addPostFrameCallback((_) {
      controller.fetchDeckDetail();
    });

    return Scaffold(
      body: Obx(() {
        if (controller.isDeckDetailLoading.value) {
          return const DeckDetailSkeletonLoading();
        }

        final deck.Category? c = controller.deckDetail.value;
        if (c == null) {
          return const SizedBox.shrink();
        }
        final selectedType = controller.selectedQuizType;
        final difficultyLabel = selectedType?.sloganText ?? '';

        final rewardXp = c.reward?.xp ?? 0;
        final rewardCoins = c.reward?.coins ?? 0;
        final totalQuestions = c.totalQuestions ?? 0;
        final hasProgress = c.progress != null;
        final progressPercent = c.progress?.progressPercent ?? 0;
        final completedQuestions = c.progress?.completedQuestions ?? 0;

        return Column(
          children: [
            Expanded(
              child: SingleChildScrollView(
                padding: EdgeInsets.zero,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    /// BANNER + BACK
                    Stack(
                      children: [
                        Image.network(
                          c.imageUrl ?? '',
                          width: double.infinity,
                          height: 200,
                          fit: BoxFit.cover,
                          errorBuilder: (_, __, ___) => Image.asset(
                            'assets/png/dummy_deck.png',
                            width: double.infinity,
                            height: 200,
                            fit: BoxFit.cover,
                          ),
                        ),
                        Positioned.fill(
                          child: DecoratedBox(
                            decoration: BoxDecoration(
                              gradient: LinearGradient(
                                begin: Alignment.topCenter,
                                end: Alignment.bottomCenter,
                                colors: [
                                  Colors.transparent,
                                  Colors.transparent,
                                  Colors.black.withValues(alpha: 0.9),
                                ],
                                stops: const [0.0, 0.6, 1.0],
                              ),
                            ),
                          ),
                        ),
                        SafeArea(
                          child: Padding(
                            padding: const EdgeInsets.only(left: 16, top: 8),
                            child: GestureDetector(
                              onTap: () => Get.back(),
                              child: Container(
                                width: 35,
                                height: 35,
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  color: Colors.black.withValues(alpha: 0.45),
                                ),
                                child: Center(
                                  child: SvgPicture.asset(
                                    'assets/svg/back_arrow.svg',
                                    color: Colors.white,
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),

                    Padding(
                      padding: EdgeInsets.symmetric(
                        horizontal: ScreenConstants.horizontalPadding,
                        vertical: 10,
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          /// TITLE
                          GradientText(
                            child: CustomText2(
                              text: c.title ?? '',
                              fontSize: 40,
                              weight: FontWeight.w600,
                              color: Colors.white,
                              letterSpacing: -1,
                            ),
                          ),
                          Row(
                            children: [
                              _pill(
                                'assets/svg/level_black.svg',
                                difficultyLabel,
                              ),
                              const SizedBox(width: 8),
                              _pill(
                                'assets/svg/question_mark.svg',
                                '$totalQuestions ${'questions'.tr}',
                              ),
                            ],
                          ),

                          const SizedBox(height: 16),

                          /// DESCRIPTION (agar ho)
                          if (c.description != null &&
                              c.description.toString().isNotEmpty) ...[
                            CustomText(
                              text: c.description.toString(),
                              fontSize: 15,
                              weight: FontWeight.w400,
                              color: const Color(0xffC0AB93),
                              lineSpacing: 1.3,
                            ),
                            const SizedBox(height: 10),
                          ] else
                            const SizedBox(height: 6),

                          /// STATS
                          _statRow(
                            'assets/svg/questions.svg',
                            'questions'.tr,
                            '$totalQuestions',
                          ),
                          _line(),
                          _statRow(
                            'assets/svg/level.svg',
                            'difficulty'.tr,
                            difficultyLabel,
                          ),
                          _line(),
                          _statRow(
                            'assets/svg/reward.svg',
                            'reward'.tr,
                            '$rewardXp XP - $rewardCoins Coins',
                          ),

                          const SizedBox(height: 10),

                          /// YOUR PROGRESS (agar progress hai aur kuch complete hua)
                          if (hasProgress && totalQuestions > 0)
                            Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 16,
                                vertical: 12,
                              ),
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(14),
                                border: Border.all(
                                  color: ColorConstants.borderColor,
                                  width: 0.5,
                                ),
                              ),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  CustomText(
                                    text: 'your_progress'.tr,
                                    fontSize: 14,
                                    weight: FontWeight.w600,
                                    color: Colors.white,
                                  ),
                                  Row(
                                    children: [
                                      Expanded(
                                        child: ClipRRect(
                                          child: LinearProgressIndicator(
                                            value: progressPercent / 100,
                                            minHeight: 8,
                                            backgroundColor: const Color(
                                              0xff3F382D,
                                            ),
                                            valueColor:
                                                const AlwaysStoppedAnimation(
                                                  Color(0xffBD8638),
                                                ),
                                          ),
                                        ),
                                      ),
                                      const SizedBox(width: 15),
                                      CustomText(
                                        text: '$progressPercent%',
                                        fontSize: 14,
                                        weight: FontWeight.w400,
                                        color: Colors.white,
                                      ),
                                      const SizedBox(width: 10),
                                    ],
                                  ),
                                  CustomText(
                                    text:
                                        '$completedQuestions ${'of'.tr} $totalQuestions ${'questions_completed'.tr}',
                                    fontSize: 12,
                                    weight: FontWeight.w400,
                                    color: ColorConstants.textColor,
                                  ),
                                ],
                              ),
                            ),

                          const SizedBox(height: 20),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),

            /// START DECK
            SafeArea(
              child: Padding(
                padding: EdgeInsets.only(
                  left: ScreenConstants.horizontalPadding,
                  right: ScreenConstants.horizontalPadding,
                ),
                child: CommonButton(
                  title: 'start_deck'.tr,
                  textColor: ColorConstants.blackColor,
                  icon: SvgPicture.asset('assets/svg/play.svg', height: 14),
                  onPressed: () async {
                    await controller.startQuiz();
                    Get.to(() => const QuizStartView());
                  },
                ),
              ),
            ),
          ],
        );
      }),
    );
  }

  Widget _pill(String icon, String text) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 5),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(18),
        gradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [
            ColorConstants.gradientColor1,
            ColorConstants.gradientColor2,
          ],
        ),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          SvgPicture.asset(icon, height: 12),
          const SizedBox(width: 7),
          CustomText(
            text: text,
            fontSize: 14,
            weight: FontWeight.w400,
            color: ColorConstants.blackColor,
          ),
        ],
      ),
    );
  }

  Widget _statRow(String icon, String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 14),
      child: Row(
        children: [
          SvgPicture.asset(icon, height: 19),
          const SizedBox(width: 14),
          Expanded(
            child: CustomText(
              text: label,
              fontSize: 14,
              weight: FontWeight.w400,
              color: ColorConstants.textColor,
            ),
          ),
          CustomText(
            text: value,
            fontSize: 14,
            weight: FontWeight.w400,
            color: ColorConstants.textColor,
          ),
        ],
      ),
    );
  }

  Widget _line() => Container(height: 0.5, color: ColorConstants.textColor);
}
