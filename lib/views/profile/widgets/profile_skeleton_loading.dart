import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import 'package:reap433/utils/constants.dart';

class ProfileSkeletonLoading extends StatelessWidget {
  const ProfileSkeletonLoading({super.key});

  static const Color _base = Color(0xff1A1712);
  static const Color _highlight = Color(0xff2A251C);

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: Shimmer.fromColors(
        baseColor: _base,
        highlightColor: _highlight,
        child: Padding(
          padding: EdgeInsets.symmetric(
            horizontal: ScreenConstants.horizontalPadding,
            vertical: ScreenConstants.verticalPadding,
          ),
          child: SingleChildScrollView(
            physics: const NeverScrollableScrollPhysics(),
            child: Column(
              children: [
                /// TITLE ("Profile" — 40px, centered)
                Center(child: _box(140, 36, radius: 6)),

                const SizedBox(height: 15),

                /// AVATAR (96x96 circle)
                _box(96, 96, radius: 48),

                const SizedBox(height: 12),

                /// NAME (40px)
                _box(180, 34, radius: 6),
                const SizedBox(height: 8),
                /// LEVEL (16px)
                _box(80, 16, radius: 4),

                const SizedBox(height: 20),

                /// STAT CARDS (Streak / XP / Coins)
                Row(
                  children: [
                    Expanded(child: _statCard()),
                    const SizedBox(width: 10),
                    Expanded(child: _statCard()),
                    const SizedBox(width: 10),
                    Expanded(child: _statCard()),
                  ],
                ),

                const SizedBox(height: 22),

                /// MENU ROWS (3)
                _menuRow(),
                const SizedBox(height: 14),
                _menuRow(),
                const SizedBox(height: 14),
                _menuRow(),

                const SizedBox(height: 20),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _statCard() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 12),
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
        children: [
          _box(59, 59, radius: 30), 
          const SizedBox(height: 5),
          _box(40, 20, radius: 4),   
          const SizedBox(height: 2),
          _box(50, 14, radius: 4),    
        ],
      ),
    );
  }

  Widget _menuRow() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 19),
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
          _box(37, 37, radius: 19),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                _box(120, 16, radius: 4),
                const SizedBox(height: 6),
                _box(200, 14, radius: 4), 
              ],
            ),
          ),
          _box(12, 12, radius: 3),
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