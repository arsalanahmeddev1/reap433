import 'package:another_flushbar/flushbar.dart';
import 'package:another_flushbar/flushbar_route.dart';
import 'package:flutter/material.dart';

class FlushBarHelper {

  static void flushBarErrorMessage(String message, BuildContext context){
    showFlushbar(context: context, flushbar: Flushbar(
      padding: const EdgeInsets.all(25),
      margin: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
      forwardAnimationCurve: Curves.decelerate,
      message: message,
      duration: const Duration(seconds: 3),
      borderRadius: BorderRadius.circular(8),
      backgroundColor: Colors.red,
      positionOffset: 20,
      flushbarPosition: FlushbarPosition.TOP,
      icon: const Icon(Icons.error, color: Colors.white,),
    )..show(context));
  }

   static void flushBarSuccessMessage(String message, BuildContext context){
    showFlushbar(context: context, flushbar: Flushbar(
      padding: const EdgeInsets.all(15),
      margin: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
      forwardAnimationCurve: Curves.decelerate,
      message: message,
      duration: const Duration(seconds: 3),
      borderRadius: BorderRadius.circular(8),
      backgroundColor: Colors.green,
      positionOffset: 20,
      flushbarPosition: FlushbarPosition.TOP,
      icon: const Icon(Icons.check, color: Colors.white,),
    )..show(context));
  }

  static void flushBarWarningMessage(String message, BuildContext context){
    showFlushbar(context: context, flushbar: Flushbar(
      padding: const EdgeInsets.all(25),
      margin: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
      forwardAnimationCurve: Curves.decelerate,
      message: message,
      duration: const Duration(seconds: 3),
      borderRadius: BorderRadius.circular(8),
      backgroundColor: Colors.orange,
      positionOffset: 20,
      flushbarPosition: FlushbarPosition.TOP,
    )..show(context));
  }
}