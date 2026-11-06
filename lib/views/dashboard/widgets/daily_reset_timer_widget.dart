

import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_svg/svg.dart';
import 'package:get/get.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/widgets/custom_text.dart';

class DailyResetTimer extends StatefulWidget {
  final String resetTime;
  const DailyResetTimer({required this.resetTime});

  @override
  State<DailyResetTimer> createState() => DailyResetTimerState();
}

class DailyResetTimerState extends State<DailyResetTimer> {
  Timer? _timer;
  int _seconds = 0;

  @override
  void initState() {
    super.initState();
    _seconds = _parse(widget.resetTime);
    _timer = Timer.periodic(const Duration(seconds: 1), (_) {
      if (_seconds > 0) {
        setState(() => _seconds--);
      } else {
        _timer?.cancel();
      }
    });
  }

  int _parse(String t) {
    try {
      final p = t.split(':');
      return int.parse(p[0]) * 3600 + int.parse(p[1]) * 60 + int.parse(p[2]);
    } catch (_) {
      return 0;
    }
  }

  String get _formatted {
    final h = (_seconds ~/ 3600).toString().padLeft(2, '0');
    final m = ((_seconds % 3600) ~/ 60).toString().padLeft(2, '0');
    final s = (_seconds % 60).toString().padLeft(2, '0');
    return '$h:$m:$s';
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        SvgPicture.asset(
          'assets/svg/time.svg',
          height: 14,
          color: ColorConstants.gradientColor1,
        ),
        const SizedBox(width: 6),
        CustomText(
          text: '${'challenge_resets_in'.tr} $_formatted',
          fontSize: 13,
          weight: FontWeight.w400,
          color: ColorConstants.textColor,
        ),
      ],
    );
  }
}
