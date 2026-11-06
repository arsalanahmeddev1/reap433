import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/services/localization_service.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

class LanguageView extends StatelessWidget {
  const LanguageView({super.key});
  static const List<Map<String, String>> _languages = [
    {'code': 'en', 'name': 'English', 'native': 'English'},
    {'code': 'es', 'name': 'Spanish', 'native': 'Español'},
    {'code': 'fr', 'name': 'French', 'native': 'Français'},
  ];

  @override
  Widget build(BuildContext context) {
    final selectedCode = (Get.locale?.languageCode ?? 'en').obs;

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
                        text: 'language'.tr,
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
                child: CustomText(
                  text: 'select_language'.tr,
                  fontSize: 14,
                  weight: FontWeight.w400,
                  color: ColorConstants.textColor,
                  
                ),
              ),

              const SizedBox(height: 40),

              /// LANGUAGE LIST
              Expanded(
                child: Obx(() {
                  final selected = selectedCode.value;
                 return ListView.separated(
                    itemCount: _languages.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 15),
                    itemBuilder: (_, i) {
                      final lang = _languages[i];
                      final isSelected = selected == lang['code'];

                      return GestureDetector(
                        onTap: () async {
                          selectedCode.value = lang['code']!;
                          await LocalizationService.changeLocale(lang['code']!);
                        },
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 18,
                            vertical: 20,
                          ),
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(12),
                            gradient: const LinearGradient(
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight,
                              colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
                            ),
                            border: Border.all(
                              color: isSelected
                                  ? ColorConstants.gradientColor1
                                  : ColorConstants.borderColor,
                              width: isSelected ? 1 : 0.5,
                            ),
                            boxShadow: isSelected
                                ? [
                                    BoxShadow(
                                      color: ColorConstants.gradientColor1
                                          .withValues(alpha: 0.25),
                                      blurRadius: 14,
                                      spreadRadius: 1,
                                    ),
                                  ]
                                : [],
                          ),
                          child: Row(
                            children: [
                              /// native name + english name
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    CustomText(
                                      text: lang['native']!,
                                      fontSize: 16,
                                      weight: FontWeight.w500,
                                      color: Colors.white,
                                    ),
                                    const SizedBox(height: 2),
                                    CustomText(
                                      text: lang['name']!,
                                      fontSize: 14,
                                      weight: FontWeight.w400,
                                      color: ColorConstants.textColor,
                                    ),
                                  ],
                                ),
                              ),

                              /// radio
                              Container(
                                width: 20,
                                height: 20,
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  border: Border.all(
                                    color: ColorConstants.gradientColor1,
                                    width: 1.5,
                                  ),
                                ),
                                child: isSelected
                                    ? Center(
                                        child: Container(
                                          width: 10,
                                          height: 10,
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
                                        ),
                                      )
                                    : null,
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  );
                }),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
