class QuizCompleteModel {
    QuizCompleteModel({
        required this.success,
        required this.code,
        required this.message,
        required this.data,
    });

    final bool? success;
    final int? code;
    final String? message;
    final Data? data;

    factory QuizCompleteModel.fromJson(Map<String, dynamic> json){ 
        return QuizCompleteModel(
            success: json["success"],
            code: json["code"],
            message: json["message"],
            data: json["data"] == null ? null : Data.fromJson(json["data"]),
        );
    }

    Map<String, dynamic> toJson() => {
        "success": success,
        "code": code,
        "message": message,
        "data": data?.toJson(),
    };

}

class Data {
    Data({
        required this.score,
        required this.answerXp,
        required this.answerCoins,
        required this.totalQuestion,
        required this.accuracyPercent,
    });

    final int? score;
    final int? answerXp;
    final int? answerCoins;
    final int? totalQuestion;
    final int? accuracyPercent;

    factory Data.fromJson(Map<String, dynamic> json){ 
        return Data(
            score: json["score"],
            answerXp: json["answer_xp"],
            answerCoins: json["answer_coins"],
            totalQuestion: json["total_question"],
            accuracyPercent: json["accuracy_percent"],
        );
    }

    Map<String, dynamic> toJson() => {
        "score": score,
        "answer_xp": answerXp,
        "answer_coins": answerCoins,
        "total_question": totalQuestion,
        "accuracy_percent": accuracyPercent,
    };

}
