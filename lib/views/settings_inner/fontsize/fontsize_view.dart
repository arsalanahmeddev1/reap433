import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/services/fontsize_service.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

class FontSizeView extends StatelessWidget {
  const FontSizeView({super.key});

  @override
  Widget build(BuildContext context) {
    final service = FontSizeService.to;

    final options = [
      {'key': 'small', 'scale': 0.9, 'sample': 15.0},
      {'key': 'medium', 'scale': 1.0, 'sample': 18.0},
      {'key': 'large', 'scale': 1.1, 'sample': 22.0},
    ];

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
                        text: 'font_size'.tr,
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

              Center(
                child: CustomText(
                  text: 'font_size_desc'.tr,
                  fontSize: 14,
                  weight: FontWeight.w400,
                  color: ColorConstants.textColor,
                ),
              ),

              const SizedBox(height: 40),

              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(20),
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
                      child: CustomText2(
                        text: 'preview_text'.tr,
                        fontSize: 22,
                        weight: FontWeight.w600,
                        color: Colors.white,
                        lines: 2,
                        lineSpacing: 1,
                      ),
                    ),
                    const SizedBox(height: 10),
                    CustomText(
                      text: 'preview_text'.tr,
                      fontSize: 14,
                      weight: FontWeight.w400,
                      color: ColorConstants.textColor,
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 30),

              /// OPTIONS
              Obx(
                () => Column(
                  children: options.map((o) {
                    final isSelected = service.currentKey == o['key'];
                    return Padding(
                      padding: const EdgeInsets.only(bottom: 14),
                      child: GestureDetector(
                        onTap: () => service.setScale(o['scale'] as double),
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 18, vertical: 18),
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
                              SizedBox(
                                width: 44,
                                child: Text(
                                  'Aa',
                                  style: TextStyle(
                                    fontSize: o['sample'] as double,
                                    fontFamily: 'Parastoo',
                                    color: Colors.white,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              ),
                              const SizedBox(width: 14),
                              Expanded(
                                child: Text(
                                  (o['key'] as String).tr,
                                  style: const TextStyle(
                                    fontSize: 16,
                                    fontFamily: 'Poppins',
                                    color: Colors.white,
                                    fontWeight: FontWeight.w400,
                                  ),
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
                      ),
                    );
                  }).toList(),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}