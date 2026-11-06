import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import 'package:reap433/utils/constants.dart';

class CategoriesSkeletonLoading extends StatelessWidget {
  const CategoriesSkeletonLoading({super.key});

  static const Color _base = Color(0xff1A1712);
  static const Color _highlight = Color(0xff2A251C);

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: _base,
      highlightColor: _highlight,
      child: ListView.separated(
        padding: EdgeInsets.symmetric(
          horizontal: ScreenConstants.horizontalPadding,
          vertical: ScreenConstants.verticalPadding,
        ),
        physics: const NeverScrollableScrollPhysics(),
        itemCount: 7,
        separatorBuilder: (_, __) => const SizedBox(height: 14),
        itemBuilder: (_, __) => _deckCard(),
      ),
    );
  }

  /// deck card — thumbnail + title/questions + arrow
  Widget _deckCard() {
    return Container(
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
          /// thumbnail (56x52)
          _box(56, 52, radius: 6),
          const SizedBox(width: 14),
          /// title + questions
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                _box(140, 20, radius: 4),
                const SizedBox(height: 8),
                _box(90, 14, radius: 4),
              ],
            ),
          ),
          const SizedBox(width: 10),
          Padding(
            padding: const EdgeInsets.only(right: 6),
            child: _box(12, 12, radius: 3),
          ),
        ],
      ),
    );
  }

  Widget _box(double width, double height, {double radius = 8}) {
    return Container(
      width: width,
      height: height,
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(radius),
      ),
    );
  }
}