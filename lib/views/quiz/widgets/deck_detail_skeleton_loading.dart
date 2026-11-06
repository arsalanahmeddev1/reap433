import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import 'package:reap433/utils/constants.dart';

class DeckDetailSkeletonLoading extends StatelessWidget {
  const DeckDetailSkeletonLoading({super.key});

  static const Color _base = Color(0xff1A1712);
  static const Color _highlight = Color(0xff2A251C);

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: _base,
      highlightColor: _highlight,
      child: SingleChildScrollView(
        physics: const NeverScrollableScrollPhysics(),
        padding: EdgeInsets.zero,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            /// BANNER (200 tall) — back button ki jagah bhi include
            Stack(
              children: [
                _box(double.infinity, 200, radius: 0),
                SafeArea(
                  child: Padding(
                    padding: const EdgeInsets.only(left: 16, top: 8),
                    child: _box(35, 35, radius: 18), // back button circle
                  ),
                ),
              ],
            ),

            Padding(
              padding: EdgeInsets.symmetric(
                horizontal: ScreenConstants.horizontalPadding,
                vertical: 10,
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const SizedBox(height: 6),

                  /// TITLE (40px)
                  _box(200, 36, radius: 6),

                  const SizedBox(height: 14),

                  /// PILLS (3)
                  Row(
                    children: [
                      _box(90, 32, radius: 18),
                      const SizedBox(width: 8),
                      _box(120, 32, radius: 18),
                      const SizedBox(width: 8),
                      _box(90, 32, radius: 18),
                    ],
                  ),

                  const SizedBox(height: 16),

                  /// DESCRIPTION (2 lines)
                  _box(double.infinity, 15, radius: 4),
                  const SizedBox(height: 8),
                  _box(220, 15, radius: 4),

                  const SizedBox(height: 20),

                  /// STAT ROWS (5) with lines
                  _statRow(),
                  _line(),
                  _statRow(),
                  _line(),
                  _statRow(),
                  _line(),
                  _statRow(),
                  _line(),
                  _statRow(),

                  const SizedBox(height: 16),

                  /// YOUR PROGRESS card
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 16,
                      vertical: 12,
                    ),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(
                        color: ColorConstants.borderColor,
                        width: 0.5,
                      ),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _box(110, 14, radius: 4),   // "Your Progress"
                        const SizedBox(height: 12),
                        Row(
                          children: [
                            Expanded(child: _box(double.infinity, 8, radius: 4)),
                            const SizedBox(width: 15),
                            _box(35, 14, radius: 4),   // %
                            const SizedBox(width: 10),
                          ],
                        ),
                        const SizedBox(height: 10),
                        _box(180, 12, radius: 4),   // "7 of 12 completed"
                      ],
                    ),
                  ),

                  const SizedBox(height: 20),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  /// stat row — icon + label ..... value
  Widget _statRow() {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 14),
      child: Row(
        children: [
          _box(19, 19, radius: 4),   // icon
          const SizedBox(width: 14),
          _box(120, 14, radius: 4),   // label
          const Spacer(),
          _box(60, 14, radius: 4),    // value
        ],
      ),
    );
  }

  Widget _line() => Container(height: 0.5, color: ColorConstants.textColor);

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