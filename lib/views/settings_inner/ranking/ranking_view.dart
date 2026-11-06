import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/models/user_ranking_model.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/settings_inner/ranking/ranking_controller.dart';
import 'package:reap433/views/settings_inner/ranking/widgets/ranking_skeleton_loading.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

class RankingView extends StatelessWidget {
  const RankingView({super.key});

  static const Color _cardBorder = Color(0xFF3A3223);

  @override
  Widget build(BuildContext context) {
    final controller = Get.put(RankingController());

    // final tabs = ['daily', 'weekly', 'monthly', 'all_time'];
    final tabs = ['daily', 'weekly', 'monthly'];

    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: EdgeInsets.symmetric(
            horizontal: ScreenConstants.horizontalPadding,
            vertical: ScreenConstants.verticalPadding,
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 10),
              GestureDetector(
                onTap: () => Get.back(),
                child: SvgPicture.asset('assets/svg/back_arrow.svg'),
              ),

              const SizedBox(height: 20),

              /// TABS
              Container(
                decoration: BoxDecoration(
                  color: const Color(0xff1F1A14),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Obx(
                  () => Row(
                    children: List.generate(tabs.length, (i) {
                      final selected = controller.selectedTab.value == i;
                      return Expanded(
                        child: GestureDetector(
                          onTap: () => controller.selectTab(i),
                          child: Container(
                            padding: const EdgeInsets.symmetric(vertical: 10),
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(12),
                              gradient:
                                  selected ? GradientText.goldGradient : null,
                            ),
                            child: Center(
                              child: CustomText(
                                text: tabs[i].tr,
                                fontSize: 12,
                                weight: FontWeight.w400,
                                color: selected
                                    ? Colors.black
                                    : ColorConstants.gradientColor1,
                              ),
                            ),
                          ),
                        ),
                      );
                    }),
                  ),
                ),
              ),

              const SizedBox(height: 30),

              /// LIST
              Expanded(
                child: Obx(() {
                  if (controller.isLoading.value) {
                    return const RankingSkeletonLoading();
                  }
                  if (controller.users.isEmpty) {
                    return Center(
                      child: CustomText(
                        text: 'No ranking available.',
                        fontSize: 14,
                        weight: FontWeight.w400,
                        color: ColorConstants.textColor,
                      ),
                    );
                  }
                  return SingleChildScrollView(
                    child: Column(
                      children: [
                        ...controller.users.map(
                          (u) => Padding(
                            padding: const EdgeInsets.only(bottom: 14),
                            child: _rankCard(u, isCurrentUser: u.currentUser == true),
                          ),
                        ),
                        const SizedBox(height: 20),
                      ],
                    ),
                  );
                }),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _rankCard(User u, {bool isCurrentUser = false}) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 22),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: isCurrentUser
              ? ColorConstants.gradientColor1.withValues(alpha: 0.6)
              : ColorConstants.borderColor,
          width: isCurrentUser ? 1 : 0.5,
        ),
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
        ),
      ),
      child: Row(
        children: [
          _rankBadge(u),
          const SizedBox(width: 20),

          /// avatar — network
          ClipOval(
            child: Image.network(
              u.profileImage ?? '',
              width: 50,
              height: 50,
              fit: BoxFit.cover,
              errorBuilder: (_, __, ___) => Image.asset(
                'assets/png/avator_placeholder.png',
                width: 50,
                height: 50,
                fit: BoxFit.cover,
              ),
            ),
          ),
          const SizedBox(width: 14),

          /// name + xp
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                CustomText2(
                  text: u.name ?? '',
                  fontSize: 30,
                  weight: FontWeight.w500,
                  color: Colors.white,
                  letterSpacing: -1,
                  lineSpacing: 1,
                ),
                Row(
                  children: [
                    SvgPicture.asset('assets/svg/small_coin.svg', height: 18),
                    const SizedBox(width: 6),
                    CustomText(
                      text: '${_formatXp(u.totalXp ?? 0)} ${'xp'.tr}',
                      fontSize: 15,
                      weight: FontWeight.w500,
                      color: ColorConstants.gradientColor1,
                    ),
                  ],
                ),
              ],
            ),
          ),

          _trailingIcon(u),
        ],
      ),
    );
  }

  Widget _rankBadge(User u) {
    final rank = u.rank ?? 0;
    Color bg;
    switch (rank) {
      case 1:
        bg = const Color(0xffBE944D);
        break;
      case 2:
        bg = const Color(0xffBEB9B2);
        break;
      case 3:
        bg = const Color(0xffC28654);
        break;
      default:
        bg = _cardBorder;
    }

    // top 3 → colored circle, warna plain number
    if (rank <= 3 && rank > 0) {
      return Container(
        width: 32,
        height: 32,
        decoration: BoxDecoration(shape: BoxShape.circle, color: bg),
        child: Center(
          child: CustomText2(
            text: '$rank',
            fontSize: 25,
            weight: FontWeight.w500,
            color: Colors.black,
          ),
        ),
      );
    }
    // rank 4+ → plain number
    return SizedBox(
      width: 32,
      child: Center(
        child: CustomText2(
          text: '$rank',
          fontSize: 28,
          weight: FontWeight.w500,
          color: Colors.white,
        ),
      ),
    );
  }

  Widget _trailingIcon(User u) {
    String asset;
    switch (u.rank) {
      case 1:
        asset = 'assets/svg/champion_trophy.svg';
        break;
      case 2:
        asset = 'assets/svg/silver_trophy.svg';
        break;
      case 3:
        asset = 'assets/svg/bronze_badge.svg';
        break;
      default:
        asset = 'assets/svg/white_badge.svg';
    }
    return SvgPicture.asset(asset, height: 39);
  }

  String _formatXp(int xp) {
    final s = xp.toString();
    final buf = StringBuffer();
    for (int i = 0; i < s.length; i++) {
      if (i > 0 && (s.length - i) % 3 == 0) buf.write(',');
      buf.write(s[i]);
    }
    return buf.toString();
  }
}