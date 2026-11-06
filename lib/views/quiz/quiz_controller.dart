import 'package:get/get.dart';
import 'package:reap433/models/quiz_question_model.dart' as quiz;
import 'package:reap433/models/quiz_types_model.dart';
import 'package:reap433/services/share_service.dart';
import 'package:reap433/views/bottom_navigation/bottom_navigation_view.dart';
import 'package:reap433/views/quiz/deck_detail/deck_detail_view.dart';
import 'package:reap433/views/quiz/quiz_result/quiz_result_view.dart';
import 'package:reap433/views/quiz/quiz_start/quiz_start_view.dart';
import 'package:reap433/models/deck_categories_model.dart';
import 'package:reap433/services/api_service.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/utils/flushbar_helper.dart';
import 'package:reap433/models/deck_detail_model.dart' as deck;
import 'package:reap433/models/quiz_complete_model.dart' as complete;

class QuizOption {
  final int id;
  final String key;
  final String text;
  QuizOption({required this.id, required this.key, required this.text});
}

class QuizQuestion {
  final int id;
  final String question;
  final List<QuizOption> options;
  QuizQuestion({
    required this.id,
    required this.question,
    required this.options,
  });
}

class QuizController extends GetxController {
  var selectedIndex = 0.obs;
  var score = 0.obs;
  var totalQuestions = 0.obs;
  var accuracyPercent = 0.obs;
  var xpEarned = 0.obs;
  var coinsEarned = 0.obs;
  var isNewBest = false.obs;
  final _api = ApiService();
  final isCategoriesLoading = false.obs;
  final categories = <Category>[].obs;

  final Rxn<Category> selectedCategory = Rxn<Category>();

  final isQuizTypesLoading = false.obs;
  final quizTypes = <QuizType>[].obs;

  final selectedQuizTypeIndex = 0.obs;

  QuizType? get selectedQuizType =>
      quizTypes.isNotEmpty ? quizTypes[selectedQuizTypeIndex.value] : null;

  final isDeckDetailLoading = false.obs;
  final Rxn<deck.Category> deckDetail = Rxn<deck.Category>();

  final isDailyChallenge = false.obs;
  int? _dcCategoryId;
  int? _dcQuizTypeId;

  // ================= QUIZ STATE =================
  final questions = <QuizQuestion>[].obs;
  var currentIndex = 0.obs;
  final isQuizLoading = false.obs;

  final Rxn<String> selectedOption = Rxn<String>();
  final Rxn<int> selectedAnswerId = Rxn<int>();
  var showFeedback = false.obs;
  var submitting = false.obs;

  var isCorrect = false.obs;
  final Rxn<String> correctOption = Rxn<String>();
  var rewardXp = 0.obs;
  var rewardCoins = 0.obs;
  var bibleVerse = ''.obs;
  var bibleText = ''.obs;

  var hasAnsweredAny = false.obs;

  var quizFinished = false.obs;

  QuizQuestion get currentQuestion => questions[currentIndex.value];
  int get total => questions.length;
  double get progress => total == 0 ? 0 : (currentIndex.value + 1) / total;

  String get correctAnswerText {
    final k = correctOption.value;
    if (k == null) return '';
    final opt = currentQuestion.options.firstWhere(
      (o) => o.key == k,
      orElse: () => QuizOption(id: 0, key: '', text: ''),
    );
    return '${opt.key}. ${opt.text}';
  }

  Future<void> fetchCategories() async {
    isCategoriesLoading.value = true;
    try {
      final response = await _api.getApi(
        '${AppConstants.baseUrl}${AppConstants.categories}',
      );

      if (response['code'] == 200 || response['code'] == 201) {
        final model = DeckCategoriesModel.fromJson(response);
        categories.assignAll(model.data?.categories ?? []);
      } else {
        _showError(response['message'] ?? 'Failed to load categories');
      }
    } catch (e) {
      _showError(e.toString());
    } finally {
      isCategoriesLoading.value = false;
    }
  }

  void selectCategory(Category category) {
    selectedCategory.value = category;
  }

  Future<void> fetchQuizTypes() async {
    isQuizTypesLoading.value = true;
    try {
      final response = await _api.getApi(
        '${AppConstants.baseUrl}${AppConstants.quizTypes}?quiz_category_id=${selectedCategory.value?.id ?? 0}',
      );

      if (response['code'] == 200 || response['code'] == 201) {
        final model = QuizTypesModel.fromJson(response);
        final list = model.data?.quizTypes ?? [];
        list.sort((a, b) => (a.id ?? 0).compareTo(b.id ?? 0));
        quizTypes.assignAll(list);
        selectedQuizTypeIndex.value = 0;
      } else {
        _showError(response['message'] ?? 'Failed to load difficulty levels');
      }
    } catch (e) {
      _showError(e.toString());
    } finally {
      isQuizTypesLoading.value = false;
    }
  }

  void selectQuizType(int index) => selectedQuizTypeIndex.value = index;

  void selectLevel(int index) => selectedIndex.value = index;

  String get selectedDifficulty {
    switch (selectedIndex.value) {
      case 1:
        return 'intermediate';
      case 2:
        return 'expert';
      default:
        return 'beginner';
    }
  }

  /// difficulty select karke quiz shuru
  void onContinue() {
    if (selectedQuizType == null) return;
    startQuiz();
    Get.to(() => DeckDetailView());
  }

  Future<void> fetchDeckDetail() async {
    final slug = selectedCategory.value?.slug;
    final categoryId = selectedCategory.value?.id;
    final quizTypeId = selectedQuizType?.id;

    if (slug == null || categoryId == null || quizTypeId == null) {
      _showError('Missing category or difficulty');
      return;
    }

    isDeckDetailLoading.value = true;
    try {
      final response = await _api.getApi(
        '${AppConstants.baseUrl}${AppConstants.categories}/$slug'
        '?quiz_type_id=$quizTypeId&quiz_category_id=$categoryId',
      );

      if (response['code'] == 200 || response['code'] == 201) {
        final model = deck.DeckDetailModel.fromJson(response);
        deckDetail.value = model.data?.category;
      } else {
        _showError(response['message'] ?? 'Failed to load details');
      }
    } catch (e) {
      _showError(e.toString());
    } finally {
      isDeckDetailLoading.value = false;
    }
  }

  // Future<void> startQuiz() async {
  //   hasAnsweredAny.value = false;
  //   quizFinished.value = false;
  //   final categoryId = selectedCategory.value?.id;
  //   final quizTypeId = selectedQuizType?.id;
  //   if (categoryId == null || quizTypeId == null) {
  //     _showError('Missing category or difficulty');
  //     return;
  //   }

  //   isQuizLoading.value = true;
  //   try {
  //     final response = await _api.getApi(
  //       '${AppConstants.baseUrl}${AppConstants.quizStart}'
  //       '?quiz_category_id=$categoryId&quiz_type_id=$quizTypeId',
  //     );

  //     if (response['code'] == 200 || response['code'] == 201) {
  //       final model = quiz.QuizQuestionsModel.fromJson(response);
  //       final apiQuestions = model.data?.questions ?? [];

  //       // API question → app QuizQuestion (index se A/B/C/D key)
  //       final mapped = apiQuestions.map((q) {
  //         final opts = <QuizOption>[];
  //         for (int i = 0; i < q.options.length; i++) {
  //           final o = q.options[i];
  //           opts.add(
  //             QuizOption(
  //               id: o.id ?? 0,
  //               key: String.fromCharCode(65 + i), // 0→A, 1→B...
  //               text: o.answers ?? '',
  //             ),
  //           );
  //         }
  //         return QuizQuestion(
  //           id: q.id ?? 0,
  //           question: q.question ?? '',
  //           options: opts,
  //         );
  //       }).toList();

  //       questions.assignAll(mapped);
  //       currentIndex.value = 0;
  //       _resetForNewQuestion();
  //     } else {
  //       _showError(response['message'] ?? 'Failed to start quiz');
  //     }
  //   } catch (e) {
  //     _showError(e.toString());
  //   } finally {
  //     isQuizLoading.value = false;
  //   }
  // }

  void selectContinueCategory(dynamic cq) {
    selectedCategory.value = Category(
      id: cq.id,
      title: cq.title,
      slug: cq.slug,
      imageUrl: cq.imageUrl,
      description: cq.description,
      totalQuestions: cq.totalQuestions,
      reward: null,
      difficulty: const [],
      progress: null,
      lastQuestion: null,
      seoTitle: cq.seoTitle,
      seoDescription: cq.seoDescription,
      iconImageUrl: cq.iconImageUrl
    );
  }

  Future<void> startQuiz() async {
    quizFinished.value = false;
    final categoryId = isDailyChallenge.value ? _dcCategoryId : selectedCategory.value?.id;
    final quizTypeId = isDailyChallenge.value ? _dcQuizTypeId : selectedQuizType?.id;
    if (categoryId == null || quizTypeId == null) {
      _showError('Missing category or difficulty');
      return;
    }

    isQuizLoading.value = true;
    try {
      final response = await _api.getApi(
        '${AppConstants.baseUrl}${AppConstants.quizStart}'
        '?quiz_category_id=$categoryId&quiz_type_id=$quizTypeId',
      );

      if (response['code'] == 200 || response['code'] == 201) {
        final model = quiz.QuizQuestionsModel.fromJson(response);
        final apiQuestions = model.data?.questions ?? [];

        final mapped = apiQuestions.map((q) {
          final opts = <QuizOption>[];
          for (int i = 0; i < q.options.length; i++) {
            final o = q.options[i];
            opts.add(
              QuizOption(
                id: o.id ?? 0,
                key: String.fromCharCode(65 + i),
                text: o.answers ?? '',
              ),
            );
          }
          return QuizQuestion(
            id: q.id ?? 0,
            question: q.question ?? '',
            options: opts,
          );
        }).toList();

        questions.assignAll(mapped);

        // ===== RESUME LOGIC =====
        // deck detail se aaya last_question (jo aakhri attempt tha)
        final lastQId = deckDetail.value?.lastQuestion?.id;
        final completed = deckDetail.value?.progress?.completedQuestions ?? 0;
        final totalQ = deckDetail.value?.totalQuestions ?? mapped.length;

        int startIndex = 0;

        if (completed >= totalQ && totalQ > 0) {
          // sab complete → dobara pehle se
          startIndex = 0;
        } else if (lastQId != null && mapped.isNotEmpty) {
          final idx = mapped.indexWhere((q) => q.id == lastQId);
          if (idx != -1 && idx < mapped.length - 1) {
            startIndex = idx + 1; // last attempt ke BAAD wale se
          }
          // agar last question hi aakhri tha → 0 (default)
        }

        currentIndex.value = startIndex;
        _resetForNewQuestion();
      } else {
        _showError(response['message'] ?? 'Failed to start quiz');
      }
    } catch (e) {
      _showError(e.toString());
    } finally {
      isQuizLoading.value = false;
    }
  }

  void _resetForNewQuestion() {
    selectedOption.value = null;
    selectedAnswerId.value = null;
    showFeedback.value = false;
    correctOption.value = null;
    bibleVerse.value = '';
    bibleText.value = '';
  }

  /// option tap → select → submit
  void selectOption(String key) {
    if (showFeedback.value || submitting.value) return;
    selectedOption.value = key;
    // us key ki answer id nikaalo
    final opt = currentQuestion.options.firstWhere((o) => o.key == key);
    selectedAnswerId.value = opt.id;
    Future.delayed(const Duration(milliseconds: 350), submitAnswer);
  }

  /// SUBMIT ANSWER — POST quiz/answer
  Future<void> submitAnswer() async {
    if (selectedAnswerId.value == null || showFeedback.value) return;
    final q = currentQuestion;
    final categoryId = isDailyChallenge.value ? _dcCategoryId : selectedCategory.value?.id;
    final quizTypeId = isDailyChallenge.value ? _dcQuizTypeId : selectedQuizType?.id;

    submitting.value = true;
    try {
      final response = await _api
          .postApi('${AppConstants.baseUrl}${AppConstants.quizAnswer}', {
            'question_id': q.id,
            'answer_id': selectedAnswerId.value,
            'quiz_category_id': categoryId,
            'quiz_type_id': quizTypeId,
          });

      if (response['code'] == 200 || response['code'] == 201) {
        hasAnsweredAny.value = true;
        final answer = response['data']?['answer'] ?? {};

        final isRight = (answer['is_right'] == 1 || answer['is_right'] == true);
        isCorrect.value = isRight;

        // reward
        rewardXp.value = answer['xp'] ?? 0;
        rewardCoins.value = answer['coins'] ?? 0;

        // bible reference (title + description)
        bibleVerse.value = answer['bible_title']?.toString() ?? '';
        bibleText.value = answer['description']?.toString() ?? '';

        // correct answer nikaalna:
        // agar user sahi hai → wahi selected option correct hai
        // agar galat hai → response se pata nahi chalta kaunsa sahi tha (API sirf submitted answer deti)
        if (isRight) {
          correctOption.value = selectedOption.value;
        } else {
          correctOption.value =
              null; // API galat answer pe correct nahi bata rahi
        }

        showFeedback.value = true;
      } else {
        _showError(response['message'] ?? 'Failed to submit answer');
      }
    } catch (e) {
      _showError(e.toString());
    } finally {
      submitting.value = false;
    }
  }

  /// NEXT QUESTION — feedback screen se agla sawal
  void nextQuestion() {
    if (currentIndex.value < total - 1) {
      currentIndex.value++;
      _resetForNewQuestion();
    } else {
      completeQuiz();
    }
  }

  void playAgain() {
    startQuiz();
    Get.off(() => const QuizStartView());
  }

  /// COMPLETE — real: POST /api/quiz/complete (session_id) → result screen
  Future<void> completeQuiz({bool goToResult = true}) async {
    if (quizFinished.value) return; // pehle se complete ho chuka
    quizFinished.value = true;

    final categoryId = isDailyChallenge.value ? _dcCategoryId : selectedCategory.value?.id;
    final quizTypeId = isDailyChallenge.value ? _dcQuizTypeId : selectedQuizType?.id;


    if (goToResult) submitting.value = true;
    try {
      final response = await _api.postApi(
        '${AppConstants.baseUrl}${AppConstants.quizComplete}',
        {'quiz_category_id': categoryId, 'quiz_type_id': quizTypeId,'is_daily_challenge': isDailyChallenge.value ? 1 : 0,},
        
      );

      if (response['code'] == 200 || response['code'] == 201) {
        final model = complete.QuizCompleteModel.fromJson(response);
        final d = model.data;
        score.value = d?.score ?? 0;
        totalQuestions.value = d?.totalQuestion ?? 0;
        accuracyPercent.value = d?.accuracyPercent ?? 0;
        xpEarned.value = d?.answerXp ?? 0;
        coinsEarned.value = d?.answerCoins ?? 0;

        if (goToResult) {
          submitting.value = false;
          Get.off(() => const QuizResultView());
        }
      } else {
        if (goToResult) {
          submitting.value = false;
          _showError(response['message'] ?? 'Failed to complete quiz');
        }
      }
    } catch (e) {
      if (goToResult) {
        submitting.value = false;
        _showError(e.toString());
      }
    }
  }

  void backToHome() {
    isDailyChallenge.value = false; 
    Get.offAll(() => BottomNavigation(index: 0));
  }

  /// SHARE RESULT — native share (share_plus package)
  void shareResult() {
  ShareService.shareQuizResult(
    score: score.value,
    total: totalQuestions.value,
    accuracy: accuracyPercent.value,
    xp: xpEarned.value,
    coins: coinsEarned.value,
  );
}

  void setDailyChallenge(int categoryId, int quizTypeId) {
    _dcCategoryId = categoryId;
    _dcQuizTypeId = quizTypeId;
  }

  /// beech mein quiz chhodna — complete call karke back
  // Future<void> exitQuiz() async {
  //   // agar koi answer diya hai aur quiz abhi finish nahi hua → complete call
  //   if (hasAnsweredAny.value && !quizFinished.value) {
  //     await completeQuiz(
  //       goToResult: false,
  //     ); // background complete, result screen nahi
  //   }
  //   Get.back();
  // }

  /// beech mein quiz chhodna — bas back
  void exitQuiz() {
    isDailyChallenge.value = false;
    Get.back();
  }

  void _showError(String msg) {
    final ctx = Get.context;
    if (ctx != null) {
      FlushBarHelper.flushBarErrorMessage(msg, ctx);
    }
  }
}
