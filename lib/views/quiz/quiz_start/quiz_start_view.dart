import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/services/localization_service.dart';
import 'package:translator/translator.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/quiz/quiz_controller.dart';
import 'package:reap433/widgets/common_button.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

class QuizStartView extends StatefulWidget {
  const QuizStartView({super.key});

  @override
  State<QuizStartView> createState() => _QuizStartViewState();
}

class _QuizStartViewState extends State<QuizStartView>{
  final controller = Get.find<QuizController>();
  final translator = GoogleTranslator();

  String _lang = 'en';
  bool _translating = false;

  final Map<String, Map<String, dynamic>> _cache = {};

  @override
  void initState() {
    super.initState();
    _init();
  }

  @override
  void dispose() {
    super.dispose();
  }

  Future<void> _init() async {
    final locale = await LocalizationService.getStartLocale();
    _lang = locale.languageCode;

    if (_lang != 'en' && controller.questions.isNotEmpty) {
      setState(() => _translating = true);
      await _ensureTranslated(_lang);
      if (mounted) setState(() => _translating = false);
    } else {
      if (mounted) setState(() {});
    }
  }

  /// current question + options translate (verse alag, feedback pe hota hai)
  Future<void> _ensureTranslated(String lang) async {
    if (controller.questions.isEmpty) return;
    final q = controller.currentQuestion;
    final cacheKey = '${q.id}_$lang';
    if (_cache.containsKey(cacheKey)) return;

    final qT = await translator.translate(q.question, to: lang);
    final optsT = <String>[];
    for (final o in q.options) {
      final t = await translator.translate(o.text, to: lang);
      optsT.add(t.text);
    }

    _cache[cacheKey] = {
      'question': qT.text,
      'options': optsT,
    };
  }

  Future<void> _translateCurrentIfNeeded() async {
    if (_lang == 'en') return;
    setState(() => _translating = true);
    await _ensureTranslated(_lang);
    if (mounted) setState(() => _translating = false);
  }

  String _q(QuizController c) {
    if (_lang == 'en') return c.currentQuestion.question;
    return _cache['${c.currentQuestion.id}_$_lang']?['question'] ??
        c.currentQuestion.question;
  }

  String _opt(QuizController c, int i) {
    if (_lang == 'en') return c.currentQuestion.options[i].text;
    final list = _cache['${c.currentQuestion.id}_$_lang']?['options'] as List?;
    return (list != null && i < list.length)
        ? list[i]
        : c.currentQuestion.options[i].text;
  }

  /// chhota gradient loader
  Widget _loader() {
    return Center(
      child: SizedBox(
        width: 25,
        height: 25,
        child: CircularProgressIndicator(
          strokeWidth: 3,
          valueColor: AlwaysStoppedAnimation(ColorConstants.gradientColor1),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: EdgeInsets.symmetric(
            horizontal: ScreenConstants.horizontalPadding,
            vertical: ScreenConstants.verticalPadding,
          ),
          child: Obx(() {
            if (controller.isQuizLoading.value ||
                controller.questions.isEmpty) {
              return _loader();
            }
            return Column(
              children: [
                const SizedBox(height: 15),
                _topBar(controller),
                const SizedBox(height: 20),
                Expanded(
                  child: (_translating || controller.submitting.value)
                      ? _loader()
                      : controller.showFeedback.value
                          ? _feedbackBody(controller)
                          : _questionBody(controller),
                ),
                controller.showFeedback.value
                    ? CommonButton(
                        title: 'next_question'.tr,
                        textColor: Colors.black,
                        onPressed: () async {
                          controller.nextQuestion();
                          await _translateCurrentIfNeeded();
                        },
                      )
                    : const SizedBox.shrink(),
                const SizedBox(height: 8),
              ],
            );
          }),
        ),
      ),
    );
  }

  Widget _topBar(QuizController c) {
    return Column(
      children: [
        Row(
          children: [
            InkWell(
              onTap: c.exitQuiz,
              child: SvgPicture.asset('assets/svg/cross.svg', height: 14),
            ),
            const SizedBox(width: 50),
            Expanded(
              child: ClipRRect(
                child: LinearProgressIndicator(
                  value: c.progress,
                  minHeight: 8,
                  backgroundColor: const Color(0xff3F382D),
                  valueColor: const AlwaysStoppedAnimation(Color(0xffBD8638)),
                ),
              ),
            ),
          ],
        ),
        if (!c.showFeedback.value) ...[
          const SizedBox(height: 8),
          Align(
            alignment: Alignment.centerRight,
            child: CustomText(
              text:
                  '${'questions'.tr} ${c.currentIndex.value + 1} ${'of'.tr} ${c.total}',
              fontSize: 12,
              weight: FontWeight.w400,
              color: ColorConstants.textColor,
            ),
          ),
        ],
      ],
    );
  }

  Widget _questionBody(QuizController c) {
    final q = c.currentQuestion;
    return SingleChildScrollView(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const SizedBox(height: 50),
          GradientText(
            child: CustomText2(
              text: _q(c),
              fontSize: 40,
              weight: FontWeight.w600,
              color: Colors.white,
              lineSpacing: 0.9,
            ),
          ),
          const SizedBox(height: 30),
          ...List.generate(q.options.length, (i) {
            final opt = q.options[i];
            final selected = c.selectedOption.value == opt.key;
            return Padding(
              padding: const EdgeInsets.only(bottom: 12),
              child: GestureDetector(
                onTap: () => c.selectOption(opt.key),
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 200),
                  padding: const EdgeInsets.symmetric(
                    horizontal: 20,
                    vertical: 20,
                  ),
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(10),
                    gradient: selected ? GradientText.goldGradient : null,
                    border: Border.all(
                      color: selected
                          ? Colors.transparent
                          : ColorConstants.borderColor,
                      width: 0.5,
                    ),
                  ),
                  child: Row(
                    children: [
                      Container(
                        width: 17,
                        height: 17,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          border: Border.all(
                            color: selected
                                ? Colors.black
                                : ColorConstants.gradientColor1,
                            width: 1.5,
                          ),
                        ),
                        child: selected
                            ? Center(
                                child: Container(
                                  width: 9,
                                  height: 9,
                                  decoration: const BoxDecoration(
                                    shape: BoxShape.circle,
                                    color: Colors.black,
                                  ),
                                ),
                              )
                            : null,
                      ),
                      const SizedBox(width: 14),
                      Expanded(
                        child: CustomText(
                          text: '${opt.key}. ${_opt(c, i)}',
                          fontSize: 14,
                          weight: FontWeight.w400,
                          color: selected
                              ? Colors.black
                              : ColorConstants.textColor,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            );
          }),
        ],
      ),
    );
  }

  Widget _feedbackBody(QuizController c) {
    final correct = c.isCorrect.value;
    return SingleChildScrollView(
      child: Column(
        children: [
          const SizedBox(height: 30),
          Container(
            width: 124,
            height: 124,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              border: Border.all(
                color: ColorConstants.gradientColor1.withValues(alpha: 0.7),
              ),
              gradient: const LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
              ),
            ),
            child: Padding(
              padding: const EdgeInsets.all(40),
              child: SvgPicture.asset(
                width: 36,
                height: 27,
                correct ? 'assets/svg/check.svg' : 'assets/svg/cross.svg',
              ),
            ),
          ),
          const SizedBox(height: 10),
          GradientText(
            child: CustomText2(
              text: correct ? 'correct'.tr : 'incorrect'.tr,
              fontSize: 40,
              weight: FontWeight.w600,
              color: Colors.white,
              letterSpacing: -1,
              lineSpacing: 1,
            ),
          ),
          const SizedBox(height: 5),
          CustomText(
            text: correct ? 'well_done'.tr : 'better_luck'.tr,
            fontSize: 14,
            weight: FontWeight.w400,
            color: ColorConstants.textColor,
          ),
          const SizedBox(height: 30),

          /// CORRECT ANSWER box — sirf agar galat + correct answer pata ho
          if (!correct && c.correctOption.value != null)
            Container(
              width: double.infinity,
              margin: const EdgeInsets.only(bottom: 16),
              padding: const EdgeInsets.symmetric(vertical: 22, horizontal: 25),
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
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  CustomText(
                    text: 'correct_answer'.tr,
                    fontSize: 14,
                    weight: FontWeight.w400,
                    color: ColorConstants.textColor,
                  ),
                  const SizedBox(height: 6),
                  GradientText(
                    child: CustomText(
                      text: c.correctAnswerText,
                      fontSize: 16,
                      weight: FontWeight.w700,
                      color: Colors.white,
                    ),
                  ),
                ],
              ),
            ),

          /// BIBLE REFERENCE box — sirf agar bible data ho
          if (c.bibleVerse.value.isNotEmpty || c.bibleText.value.isNotEmpty)
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(vertical: 30, horizontal: 25),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(12),
                border:
                    Border.all(color: ColorConstants.borderColor, width: 0.5),
                gradient: const LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
                ),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  CustomText(
                    text: 'bible_reference'.tr,
                    fontSize: 14,
                    weight: FontWeight.w400,
                    color: ColorConstants.textColor,
                  ),
                  if (c.bibleVerse.value.isNotEmpty) ...[
                    const SizedBox(height: 6),
                    GradientText(
                      child: CustomText(
                        text: c.bibleVerse.value,
                        fontSize: 14,
                        weight: FontWeight.w700,
                      ),
                    ),
                  ],
                  if (c.bibleText.value.isNotEmpty) ...[
                    const SizedBox(height: 8),
                    _lang == 'en'
                        ? CustomText(
                            text: c.bibleText.value,
                            fontSize: 14,
                            weight: FontWeight.w400,
                            color: ColorConstants.textColor,
                            lineSpacing: 1.4,
                          )
                        : FutureBuilder<String>(
                            future: translator
                                .translate(c.bibleText.value, to: _lang)
                                .then((v) => v.text),
                            builder: (context, snap) {
                              return CustomText(
                                text: snap.data ?? c.bibleText.value,
                                fontSize: 14,
                                weight: FontWeight.w400,
                                color: ColorConstants.textColor,
                                lineSpacing: 1.4,
                              );
                            },
                          ),
                  ],
                ],
              ),
            ),

          if (correct) ...[
            const SizedBox(height: 35),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                _rewardChip('+${c.rewardXp.value} ${'xp'.tr}'),
                const SizedBox(width: 30),
                _rewardChip('+${c.rewardCoins.value} ${'coins'.tr}'),
              ],
            ),
          ],
          const SizedBox(height: 20),
        ],
      ),
    );
  }

  Widget _rewardChip(String text) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 22, vertical: 10),
      decoration: BoxDecoration(
        color: const Color(0xff211C15),
        borderRadius: BorderRadius.circular(10),
      ),
      child: CustomText(
        text: text,
        fontSize: 14,
        weight: FontWeight.w400,
        color: ColorConstants.textColor,
      ),
    );
  }
}