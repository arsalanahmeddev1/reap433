import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import 'package:reap433/utils/constants.dart';

class NotificationSkeletonLoading extends StatelessWidget {
  const NotificationSkeletonLoading({super.key});

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
          /// TOP ROW — back button ki jagah khaali, phir tabs + mark as read
          Row(
            children: [
              /// back button space (khaali — real view se aayega)
              const SizedBox(width: 24),
              const SizedBox(width: 12),
              /// All tab
              _box(70, 36, radius: 10),
              const SizedBox(width: 5),
              /// Unread tab
              _box(90, 36, radius: 10),
              const Spacer(),
              /// Mark as Read
              _box(120, 36, radius: 10),
            ],
          ),

          const SizedBox(height: 22),

          /// LIST
          Expanded(
            child: ListView.separated(
              physics: const NeverScrollableScrollPhysics(),
              itemCount: 6,
              separatorBuilder: (_, __) => const SizedBox(height: 16),
              itemBuilder: (_, __) => _notificationCard(),
            ),
          ),
        ],
      ),
    );
  }

  /// notification card — dot + title/body + time
  Widget _notificationCard() {
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
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          /// dot (9x9)
          Padding(
            padding: const EdgeInsets.only(top: 6),
            child: _box(9, 9, radius: 5),
          ),
          const SizedBox(width: 12),
          /// title + body
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                _box(160, 16, radius: 4),          // title
                const SizedBox(height: 8),
                _box(double.infinity, 12, radius: 4),  // body line 1
                const SizedBox(height: 6),
                _box(140, 12, radius: 4),           // body line 2
              ],
            ),
          ),
          const SizedBox(width: 12),
          /// time
          _box(40, 14, radius: 4),
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