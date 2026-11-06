import 'package:get/get.dart';
import 'package:reap433/models/profile_model.dart';
import 'package:reap433/models/deck_categories_model.dart';
import 'package:reap433/models/continue_quiz_model.dart' as cont;
import 'package:reap433/services/api_service.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/utils/flushbar_helper.dart';

class DashboardController extends GetxController {
  final _api = ApiService();

  final isLoading = false.obs;
  final Rxn<User> user = Rxn<User>();
  final categories = <Category>[].obs;
  final continueQuizzes = <cont.Category>[].obs; // continue learning list

  /// teeno ek saath — jab tak sab na aayein, loading
  Future<void> loadDashboard() async {
    isLoading.value = true;
    try {
      await Future.wait([
        _fetchProfile(),
        _fetchCategories(),
        _fetchContinue(),
      ]);
    } catch (e) {
      _showError(e.toString());
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> _fetchProfile() async {
    final response = await _api.getApi(
      '${AppConstants.baseUrl}${AppConstants.profile}',
    );
    if (response['code'] == 200 || response['code'] == 201) {
      final model = ProfileModel.fromJson(response);
      user.value = model.data?.user;
    } else {
      _showError(response['message'] ?? 'Failed to load profile');
    }
  }

  Future<void> _fetchCategories() async {
    final response = await _api.getApi(
      '${AppConstants.baseUrl}${AppConstants.categories}',
    );
    if (response['code'] == 200 || response['code'] == 201) {
      final model = DeckCategoriesModel.fromJson(response);
      categories.assignAll(model.data?.categories ?? []);
    } else {
      _showError(response['message'] ?? 'Failed to load categories');
    }
  }

  Future<void> _fetchContinue() async {
    final response = await _api.getApi(
      '${AppConstants.baseUrl}${AppConstants.continueQuiz}',
    );
    if (response['code'] == 200 || response['code'] == 201) {
      final model = cont.ContinueQuizModel.fromJson(response);
      continueQuizzes.assignAll(model.data?.categories ?? []);
    } else {
      _showError(response['message'] ?? 'Failed to load continue quiz');
    }
  }

  String get greetingKey {
    final hour = DateTime.now().hour;
    if (hour >= 5 && hour < 12) return 'good_morning';
    if (hour >= 12 && hour < 17) return 'good_afternoon';
    if (hour >= 17 && hour < 21) return 'good_evening';
    return 'good_night';
  }

  void _showError(String msg) {
    final ctx = Get.context;
    if (ctx != null) {
      FlushBarHelper.flushBarErrorMessage(msg, ctx);
    }
  }
}