import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/models/deck_categories_model.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/quiz/choose_your_level/choose_your_level_view.dart';
import 'package:reap433/views/quiz/quiz_controller.dart';
import 'package:reap433/views/quiz/widgets/categories_skeleton_loading.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

class DeckCategoriesView extends StatelessWidget {
  const DeckCategoriesView({super.key});

  @override
  Widget build(BuildContext context) {
    final controller = Get.find<QuizController>();
    WidgetsBinding.instance.addPostFrameCallback((_){
     controller.fetchCategories();
    });

    return Scaffold(
      backgroundColor: Colors.black,
      body: Column(
        children: [
          /// HEADER (static)
          SizedBox(
            height: 135,
            width: double.infinity,
            child: Stack(
              fit: StackFit.expand,
              children: [
                Image.asset(
                  'assets/png/categories_header_bg.png',
                  fit: BoxFit.cover,
                ),
                SafeArea(
                  child: Center(
                    child: GradientText(
                      child: CustomText2(
                        text: 'categories'.tr,
                        fontSize: 40,
                        letterSpacing: -1,
                        weight: FontWeight.w600,
                        color: Colors.white,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 10),

          /// LIST
          Expanded(
            child: Obx(() {
              if (controller.isCategoriesLoading.value) {
                return const CategoriesSkeletonLoading();
              }
              if (controller.categories.isEmpty) {
                return Center(
                  child: CustomText(
                    text: 'No categories found.',
                    fontSize: 14,
                    weight: FontWeight.w400,
                    color: ColorConstants.textColor,
                  ),
                );
              }
              return ListView.separated(
                padding: EdgeInsets.symmetric(
                  horizontal: ScreenConstants.horizontalPadding,
                  vertical: ScreenConstants.verticalPadding,
                ),
                itemCount: controller.categories.length,
                separatorBuilder: (_, __) => const SizedBox(height: 14),
                itemBuilder: (_, i) {
                  final c = controller.categories[i];
                  return _deckCard(
                    category: c,
                    onTap: () {
                      controller.selectCategory(c);
                      Get.to(() => const ChooseYourLevelView());
                    },
                  );
                },
              );
            }),
          ),
        ],
      ),
    );
  }

  Widget _deckCard({required Category category, required VoidCallback onTap}) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
        decoration: BoxDecoration(
          gradient: const LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
          ),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: ColorConstants.borderColor, width: 0.5),
        ),
        child: Row(
          children: [
            ClipRRect(
              borderRadius: BorderRadius.circular(6),
              child: Image.network(
                category.imageUrl ?? '',
                width: 56,
                height: 52,
                fit: BoxFit.cover,
                // load hote waqt chhota loader
                loadingBuilder: (context, child, progress) {
                  if (progress == null) return child;
                  return Container(
                    width: 56,
                    height: 52,
                    color: const Color(0xff1A1712),
                  );
                },
                errorBuilder: (context, error, stack) {
                  return Image.asset(
                    'assets/png/dummy_deck.png',
                    width: 56,
                    height: 52,
                    fit: BoxFit.cover,
                  );
                },
              ),
            ),
            const SizedBox(width: 14),

            /// title + questions
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  CustomText2(
                    text: category.title ?? '',
                    fontSize: 20,
                    weight: FontWeight.w500,
                    color: ColorConstants.creamColor,
                    lineSpacing: 1,
                  ),
                  CustomText(
                    text: '${category.totalQuestions ?? 0} ${'questions'.tr}',
                    fontSize: 14,
                    weight: FontWeight.w400,
                    color: const Color(0xffA2835F),
                  ),
                ],
              ),
            ),

            /// arrow
            Padding(
              padding: const EdgeInsets.only(right: 6),
              child: SvgPicture.asset(
                'assets/svg/deck_arrow.svg',
                height: 12.7,
                color: ColorConstants.gradientColor1,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
