import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import 'package:reap433/utils/constants.dart';

class AchievementSkeletonLoading extends StatelessWidget {
  const AchievementSkeletonLoading({super.key});

  static const Color _base = Color(0xff1A1712);
  static const Color _highlight = Color(0xff2A251C);

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: _base,
      highlightColor: _highlight,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const SizedBox(height: 5),
          /// LIST (cards)
          Expanded(
            child: ListView.separated(
              physics: const NeverScrollableScrollPhysics(),
              itemCount: 5,
              separatorBuilder: (_, __) => const SizedBox(height: 16),
              itemBuilder: (_, __) => _achievementCard(),
            ),
          ),
        ],
      ),
    );
  }

  Widget _achievementCard() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 18),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: ColorConstants.borderColor, width: 0.5),
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
        ),
      ),
      child: Row(
        children: [
          /// icon circle (left)
          _box(58, 58, radius: 29),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                _box(130, 18, radius: 4), 
                const SizedBox(height: 8),
                _box(180, 14, radius: 4),
                const SizedBox(height: 8),
                _box(150, 14, radius: 4),
              ],
            ),
          ),
          const SizedBox(width: 10),
          _box(40, 40, radius: 20),
        ],
      ),
    );
  }

  Widget _box(double width, double height, {double radius = 8}) {
    return Container(
      width: width,
      height: height,
      decoration: BoxDecoration(
        color: Colors.white, // shimmer overlay
        borderRadius: BorderRadius.circular(radius),
      ),
    );
  }
}