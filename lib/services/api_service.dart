import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:reap433/exceptions/app_exceptions.dart';
import 'package:reap433/services/storage_service.dart';

class ApiService {
  static bool _isSessionExpiredHandled = false;

  Future<dynamic> getApi(String url) async {
    final token = StorageService.getToken();

    try {
      final response = await http
          .get(
            Uri.parse(url),
            headers: {
              "Accept": "application/json",
              "Authorization": "Bearer $token",
            },
          )
          .timeout(const Duration(seconds: 30));
      print(url);
      print(response.body);

      return await returnResponse(response);
    } on SocketException {
      throw NoInternetException("No Internet");
    } on TimeoutException {
      throw RequestTimeoutException("Request Time Out");
    }
  }

  Future<dynamic> postApi(String url, dynamic data) async {
    final token = StorageService.getToken();

    try {
      print(url);
      print(data);

      final response = await http
          .post(
            Uri.parse(url),
            body: jsonEncode(data),
            headers: {
              "Content-Type": "application/json",
              "Accept": "application/json",
              "Authorization": "Bearer $token",
            },
          )
          .timeout(const Duration(seconds: 30));

      print(response.body);

      return await returnResponse(response);
    } on SocketException {
      throw NoInternetException("No Internet");
    } on TimeoutException {
      throw RequestTimeoutException("Request Time Out");
    }
  }

  Future<dynamic> putApi(String url, dynamic data) async {
    final token = StorageService.getToken();

    try {
      final response = await http
          .put(
            Uri.parse(url),
            body: jsonEncode(data),
            headers: {
              "Content-Type": "application/json",
              "Accept": "application/json",
              "Authorization": "Bearer $token",
            },
          )
          .timeout(const Duration(seconds: 30));

      return await returnResponse(response);
    } on SocketException {
      throw NoInternetException("No Internet");
    } on TimeoutException {
      throw RequestTimeoutException("Request Time Out");
    }
  }

  Future<dynamic> patchApi(String url, dynamic data) async {
    final token = StorageService.getToken();

    try {
      final response = await http
          .patch(
            Uri.parse(url),
            body: jsonEncode(data),
            headers: {
              "Content-Type": "application/json",
              "Accept": "application/json",
              "Authorization": "Bearer $token",
            },
          )
          .timeout(const Duration(seconds: 30));

      return await returnResponse(response);
    } on SocketException {
      throw NoInternetException("No Internet");
    } on TimeoutException {
      throw RequestTimeoutException("Request Time Out");
    }
  }

  Future<dynamic> deleteApi(String url) async {
    final token = StorageService.getToken();

    try {
      final response = await http
          .delete(
            Uri.parse(url),
            headers: {
              "Accept": "application/json",
              "Authorization": "Bearer $token",
            },
          )
          .timeout(const Duration(seconds: 30));

      return await returnResponse(response);
    } on SocketException {
      throw NoInternetException("No Internet");
    } on TimeoutException {
      throw RequestTimeoutException("Request Time Out");
    }
  }

  Future<dynamic> postMultipart(
    String url,
    Map<String, String> fields, {
    String? filePath,
    String fileFieldName = 'profile_image',
  }) async {
    final token = StorageService.getToken();

    try {
      final request = http.MultipartRequest('POST', Uri.parse(url));
      request.headers.addAll({
        "Accept": "application/json",
        "Authorization": "Bearer $token",
      });

      request.fields.addAll(fields);

      if (filePath != null && filePath.isNotEmpty) {
        request.files.add(
          await http.MultipartFile.fromPath(fileFieldName, filePath),
        );
      }

      final streamed = await request.send().timeout(
        const Duration(seconds: 30),
      );
      final response = await http.Response.fromStream(streamed);

      print(response.body);

      return await returnResponse(response);
    } on SocketException {
      throw NoInternetException("No Internet");
    } on TimeoutException {
      throw RequestTimeoutException("Request Time Out");
    }
  }

  Future<dynamic> returnResponse(http.Response response) async {
    final body = jsonDecode(response.body);

    switch (response.statusCode) {
      case 200:
      case 201:
      case 400:
      case 403:
      case 404:
      case 409:
      case 422:
      case 429:
      case 500:
        return body;

      case 401:
          await _handleSessionExpired();

      default:
        throw FetchDataException(
          "Error occurred while communicating with server with StatusCode : ${response.statusCode}",
        );
    }
  }

  Future<void> _handleSessionExpired() async {
    if (_isSessionExpiredHandled) return;

    _isSessionExpiredHandled = true;

    await StorageService.removeToken();

    Future.delayed(const Duration(milliseconds: 200), () {});
  }

  static void resetSessionFlag() {
    _isSessionExpiredHandled = false;
  }
}
