import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import 'package:reap433/utils/constants.dart';

class DashboardSkeletonLoading extends StatelessWidget {
  const DashboardSkeletonLoading({super.key});

  static const Color _base = Color(0xff1A1712);
  static const Color _highlight = Color(0xff2A251C);

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: Shimmer.fromColors(
        baseColor: _base,
        highlightColor: _highlight,
        child: SingleChildScrollView(
          physics: const NeverScrollableScrollPhysics(),
          child: Padding(
            padding: EdgeInsets.symmetric(
              horizontal: ScreenConstants.horizontalPadding,
              vertical: ScreenConstants.verticalPadding,
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                /// BELL (top-right) — height 24
                Row(
                  mainAxisAlignment: MainAxisAlignment.end,
                  children: [_box(24, 24, radius: 6)],
                ),

                const SizedBox(height: 20),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _box(110, 14, radius: 4), 
                          const SizedBox(height: 10),
                          _box(150, 36, radius: 6),
                        ],
                      ),
                    ),
                    _box(96, 62, radius: 8),
                  ],
                ),

                const SizedBox(height: 22),
                _box(double.infinity, 170, radius: 8),

                const SizedBox(height: 16),

                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    _box(170, 20, radius: 4),
                    _box(56, 16, radius: 4),
                  ],
                ),
                const SizedBox(height: 6),
                _box(double.infinity, 88, radius: 12),

                const SizedBox(height: 16),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    _box(120, 20, radius: 4),
                    _box(56, 16, radius: 4),
                  ],
                ),
                const SizedBox(height: 6),
                SizedBox(
                  height: 92,
                  child: ListView.separated(
                    scrollDirection: Axis.horizontal,
                    physics: const NeverScrollableScrollPhysics(),
                    itemCount: 6,
                    separatorBuilder: (_, __) => const SizedBox(width: 5),
                    itemBuilder: (_, __) => _box(78, 92, radius: 12),
                  ),
                ),

                const SizedBox(height: 16),
                _box(180, 20, radius: 4),
                const SizedBox(height: 6),
                _box(double.infinity, 100, radius: 12),

                const SizedBox(height: 30),
              ],
            ),
          ),
        ),
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