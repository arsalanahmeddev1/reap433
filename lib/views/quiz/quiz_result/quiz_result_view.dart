import 'package:audioplayers/audioplayers.dart';
import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:lottie/lottie.dart';
import 'package:reap433/services/sound_service.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/quiz/quiz_controller.dart';
import 'package:reap433/widgets/common_button.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

class QuizResultView extends StatefulWidget {
  const QuizResultView({super.key});

  @override
  State<QuizResultView> createState() => _QuizResultViewState();
}

class _QuizResultViewState extends State<QuizResultView>
    with SingleTickerProviderStateMixin {
  final controller = Get.find<QuizController>();
  late final AnimationController _lottieController;
  final _audioPlayer = AudioPlayer();

  @override
  void initState() {
    super.initState();
    _lottieController = AnimationController(vsync: this);
    _playWinSound(); // screen aate hi sound
  }

Future<void> _playWinSound() async {
  print('SOUND ON: ${SoundService.to.soundOn.value}');
  if (!SoundService.to.soundOn.value) return;
  try {
    await _audioPlayer.play(AssetSource('sounds/winning_sound.mp3'));
    print('SOUND PLAYED');
  } catch (e) {
    print('SOUND ERROR: $e');
  }
}

  @override
  void dispose() {
    _lottieController.dispose();
    _audioPlayer.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
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
              Expanded(
                child: SingleChildScrollView(
                  child: Obx(
                    () => Column(
                      children: [
                        const SizedBox(height: 10),
                        Align(
                          alignment: Alignment.centerLeft,
                          child: GestureDetector(
                            onTap: controller.backToHome,
                            child: SvgPicture.asset(
                              'assets/svg/back_arrow.svg',
                            ),
                          ),
                        ),

                        /// WINNING LOTTIE (sound iske saath bajta hai)
                        Lottie.asset(
                          'assets/lotties/reward_trophy.json',
                          height: 190,
                          fit: BoxFit.contain,
                          frameRate: FrameRate(30),
                          controller: _lottieController,
                          onLoaded: (composition) {
                            _lottieController
                              ..duration = composition.duration
                              ..forward();
                          },
                        ),

                        const SizedBox(height: 6),

                        GradientText(
                          child: CustomText2(
                            text: 'quiz_complete'.tr,
                            fontSize: 40,
                            weight: FontWeight.w600,
                            color: Colors.white,
                            letterSpacing: -1,
                          ),
                        ),

                        const SizedBox(height: 24),

                        Row(
                          children: [
                            Expanded(
                              child: _statCard(
                                'score'.tr,
                                '${controller.score.value} / ${controller.totalQuestions.value}',
                              ),
                            ),
                            const SizedBox(width: 14),
                            Expanded(
                              child: _statCard(
                                'accuracy'.tr,
                                '${controller.accuracyPercent.value}%',
                              ),
                            ),
                          ],
                        ),

                        const SizedBox(height: 18),

                        Row(
                          children: [
                            Expanded(
                              child: _rewardRow(
                                'assets/svg/coins_earned.svg',
                                'xp_earned'.tr,
                                '${controller.xpEarned.value}',
                              ),
                            ),
                            Expanded(
                              child: _rewardRow(
                                'assets/svg/xp_earned.svg',
                                'coins_earned'.tr,
                                '+${controller.coinsEarned.value}',
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              ),

              /// BUTTONS
              Obx(() {
                final isDaily = controller.isDailyChallenge.value;
                return Column(
                  children: [
                    if (!isDaily) ...[
                      CommonButton(
                        title: 'play_again'.tr,
                        textColor: Colors.black,
                        onPressed: controller.playAgain,
                      ),
                      const SizedBox(height: 12),
                    ],
                    CommonButton(
                      title: 'back_to_home'.tr,
                      isTransparent: true,
                      borderColor: ColorConstants.borderColor,
                      textColor: ColorConstants.textColor,
                      onPressed: controller.backToHome,
                    ),
                    if (!isDaily) ...[
                      const SizedBox(height: 12),
                      CommonButton(
                        title: 'share_result'.tr,
                        isTransparent: true,
                        borderColor: ColorConstants.borderColor,
                        textColor: ColorConstants.textColor,
                        onPressed: controller.shareResult,
                      ),
                    ],
                  ],
                );
              }),
            ],
          ),
        ),
      ),
    );
  }

  Widget _statCard(String label, String value) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 18),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: ColorConstants.borderColor, width: 0.5),
        color: Colors.transparent,
      ),
      child: Column(
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
              letterSpacing: -1,
            ),
          ),
        ],
      ),
    );
  }

  Widget _rewardRow(String icon, String label, String value) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
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
        const SizedBox(width: 10),
        Column(
          crossAxisAlignment: CrossAxisAlignment.center,
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
                letterSpacing: -1,
              ),
            ),
          ],
        ),
      ],
    );
  }
}
