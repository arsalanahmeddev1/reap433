import 'dart:core';
import 'package:flutter/material.dart';

class AppExceptions implements Exception {
  final String _message;
  final String _prefix;
  final IconData _icon;

  AppExceptions(this._message, this._prefix, this._icon);

  String get message => _message;
  String get prefix => _prefix;
  IconData get icon => _icon;

  @override
  String toString() {
    return _message;
  }

  Map<String, dynamic> getDisplayInfo() {
    return {
      'message': _message,
      'icon': _icon,
      'type': _prefix,
    };
  }
}

class NoInternetException extends AppExceptions {
  NoInternetException([String? message])
      : super(
          message ?? 'No Internet Connection',
          'No Internet Connection',
          Icons.wifi_off,
        );
}

class RequestTimeoutException extends AppExceptions {
  RequestTimeoutException([String? message])
      : super(
          message ?? 'Request Timeout',
          'Request Timeout',
          Icons.access_time,
        );
}

class FetchDataException extends AppExceptions {
  FetchDataException([String? message])
      : super(
          message ?? 'Error During Communication',
          'Error During Communication', 
          Icons.error_outline,
        );
}

class UnAuthorisedException extends AppExceptions {
  UnAuthorisedException([String? message])
      : super(
          message ?? 'Unauthorised Request',
          'Unauthorised Request',
          Icons.lock_outline,
        );
}

class ExceptionHelper {
  static Widget buildErrorWidget(AppExceptions exception) {
    final displayInfo = exception.getDisplayInfo();
    
    return Container(
      padding: const EdgeInsets.all(16.0),
      child: Row(
        children: [
          Icon(
            displayInfo['icon'],
            color: Colors.red,
            size: 24,
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              displayInfo['message'],
              style: const TextStyle(
                fontSize: 16,
                color: Colors.red,
              ),
            ),
          ),
        ],
      ),
    );
  }

  static void showErrorSnackBar(BuildContext context, AppExceptions exception) {
    final displayInfo = exception.getDisplayInfo();
    
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Row(
          children: [
            Icon(
              displayInfo['icon'],
              color: Colors.white,
              size: 20,
            ),
            const SizedBox(width: 8),
            Text(displayInfo['message']),
          ],
        ),
        backgroundColor: Colors.red,
        duration: const Duration(seconds: 3),
      ),
    );
  }

  static void showErrorDialog(BuildContext context, AppExceptions exception) {
    final displayInfo = exception.getDisplayInfo();
    
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Row(
            children: [
              Icon(
                displayInfo['icon'],
                color: Colors.red,
                size: 24,
              ),
              const SizedBox(width: 8),
              const Text('Error'),
            ],
          ),
          content: Text(displayInfo['message']),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(),
              child: const Text('OK'),
            ),
          ],
        );
      },
    );
  }
}