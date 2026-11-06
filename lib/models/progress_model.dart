class ProgressModel {
    ProgressModel({
        required this.success,
        required this.code,
        required this.message,
        required this.data,
    });

    final bool? success;
    final int? code;
    final String? message;
    final Data? data;

    factory ProgressModel.fromJson(Map<String, dynamic> json){ 
        return ProgressModel(
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
        required this.overallProgressPercent,
        required this.questionsAnswered,
        required this.decksComplete,
        required this.averageScorePercent,
    });

    final int? overallProgressPercent;
    final QuestionsAnswered? questionsAnswered;
    final DecksComplete? decksComplete;
    final int? averageScorePercent;

    factory Data.fromJson(Map<String, dynamic> json){ 
        return Data(
            overallProgressPercent: json["overall_progress_percent"],
            questionsAnswered: json["questions_answered"] == null ? null : QuestionsAnswered.fromJson(json["questions_answered"]),
            decksComplete: json["decks_complete"] == null ? null : DecksComplete.fromJson(json["decks_complete"]),
            averageScorePercent: json["average_score_percent"],
        );
    }

    Map<String, dynamic> toJson() => {
        "overall_progress_percent": overallProgressPercent,
        "questions_answered": questionsAnswered?.toJson(),
        "decks_complete": decksComplete?.toJson(),
        "average_score_percent": averageScorePercent,
    };

}

class DecksComplete {
    DecksComplete({
        required this.completed,
        required this.total,
    });

    final int? completed;
    final int? total;

    factory DecksComplete.fromJson(Map<String, dynamic> json){ 
        return DecksComplete(
            completed: json["completed"],
            total: json["total"],
        );
    }

    Map<String, dynamic> toJson() => {
        "completed": completed,
        "total": total,
    };

}

class QuestionsAnswered {
    QuestionsAnswered({
        required this.answered,
        required this.total,
    });

    final int? answered;
    final int? total;

    factory QuestionsAnswered.fromJson(Map<String, dynamic> json){ 
        return QuestionsAnswered(
            answered: json["answered"],
            total: json["total"],
        );
    }

    Map<String, dynamic> toJson() => {
        "answered": answered,
        "total": total,
    };

}
