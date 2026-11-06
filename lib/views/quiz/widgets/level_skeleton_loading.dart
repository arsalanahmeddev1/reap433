import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import 'package:reap433/utils/constants.dart';

class ChooseLevelSkeletonLoading extends StatelessWidget {
  const ChooseLevelSkeletonLoading({super.key});

  static const Color _base = Color(0xff1A1712);
  static const Color _highlight = Color(0xff2A251C);

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: _base,
      highlightColor: _highlight,
      child: ListView.separated(
        physics: const NeverScrollableScrollPhysics(),
        itemCount: 3,
        separatorBuilder: (_, __) => const SizedBox(height: 18),
        itemBuilder: (_, __) => _levelCard(),
      ),
    );
  }

  /// level card — icon circle + title/desc/badge + radio
  Widget _levelCard() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: ColorConstants.borderColor, width: 0.5),
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
        ),
      ),
      child: Row(
        children: [
          /// icon circle (67x67)
          _box(67, 67, radius: 34),
          const SizedBox(width: 16),

          /// title + desc + badge
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                _box(120, 22, radius: 4),   // title (24px)
                const SizedBox(height: 8),
                _box(double.infinity, 14, radius: 4),  // desc line 1
                const SizedBox(height: 6),
                _box(140, 14, radius: 4),    // desc line 2
                const SizedBox(height: 10),
                _box(70, 24, radius: 20),    // badge (Easy/Medium/Hard)
              ],
            ),
          ),

          const SizedBox(width: 40),

          /// radio (30x30)
          _box(30, 30, radius: 15),
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