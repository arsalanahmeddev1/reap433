import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import 'package:reap433/utils/constants.dart';

class ProgressSkeletonLoading extends StatelessWidget {
  const ProgressSkeletonLoading({super.key});

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
                Center(child: _box(200, 36, radius: 6)),

                const SizedBox(height: 30),
                _circle(245, 20),

                const SizedBox(height: 34),

                _statCard(),
                const SizedBox(height: 10),
                _statCard(),
                const SizedBox(height: 10),
                _statCard(),

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
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 15),
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
          _box(42, 42, radius: 21),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                _box(140, 22, radius: 4),  
                const SizedBox(height: 6),
                _box(90, 14, radius: 4),   
              ],
            ),
          ),
          _box(50, 18, radius: 4),
        ],
      ),
    );
  }

  Widget _circle(double size, double stroke) {
    return Container(
      width: size,
      height: size,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        border: Border.all(color: Colors.white, width: stroke),
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