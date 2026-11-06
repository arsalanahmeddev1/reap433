class QuizQuestionsModel {
    QuizQuestionsModel({
        required this.success,
        required this.code,
        required this.message,
        required this.data,
    });

    final bool? success;
    final int? code;
    final String? message;
    final Data? data;

    factory QuizQuestionsModel.fromJson(Map<String, dynamic> json){ 
        return QuizQuestionsModel(
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
        required this.totalQuestions,
        required this.questions,
    });

    final int? totalQuestions;
    final List<Question> questions;

    factory Data.fromJson(Map<String, dynamic> json){ 
        return Data(
            totalQuestions: json["total_questions"],
            questions: json["questions"] == null ? [] : List<Question>.from(json["questions"]!.map((x) => Question.fromJson(x))),
        );
    }

    Map<String, dynamic> toJson() => {
        "total_questions": totalQuestions,
        "questions": questions.map((x) => x?.toJson()).toList(),
    };

}

class Question {
    Question({
        required this.id,
        required this.quizCategoryId,
        required this.quizTypeId,
        required this.category,
        required this.type,
        required this.question,
        required this.slug,
        required this.description,
        required this.seoTitle,
        required this.seoDescription,
        required this.options,
    });

    final int? id;
    final int? quizCategoryId;
    final int? quizTypeId;
    final Category? category;
    final Category? type;
    final String? question;
    final String? slug;
    final String? description;
    final String? seoTitle;
    final String? seoDescription;
    final List<Option> options;

    factory Question.fromJson(Map<String, dynamic> json){ 
        return Question(
            id: json["id"],
            quizCategoryId: json["quiz_category_id"],
            quizTypeId: json["quiz_type_id"],
            category: json["category"] == null ? null : Category.fromJson(json["category"]),
            type: json["type"] == null ? null : Category.fromJson(json["type"]),
            question: json["question"],
            slug: json["slug"],
            description: json["description"],
            seoTitle: json["seo_title"],
            seoDescription: json["seo_description"],
            options: json["options"] == null ? [] : List<Option>.from(json["options"]!.map((x) => Option.fromJson(x))),
        );
    }

    Map<String, dynamic> toJson() => {
        "id": id,
        "quiz_category_id": quizCategoryId,
        "quiz_type_id": quizTypeId,
        "category": category?.toJson(),
        "type": type?.toJson(),
        "question": question,
        "slug": slug,
        "description": description,
        "seo_title": seoTitle,
        "seo_description": seoDescription,
        "options": options.map((x) => x?.toJson()).toList(),
    };

}

class Category {
    Category({
        required this.id,
        required this.title,
        required this.slug,
    });

    final int? id;
    final String? title;
    final String? slug;

    factory Category.fromJson(Map<String, dynamic> json){ 
        return Category(
            id: json["id"],
            title: json["title"],
            slug: json["slug"],
        );
    }

    Map<String, dynamic> toJson() => {
        "id": id,
        "title": title,
        "slug": slug,
    };

}

class Option {
    Option({
        required this.id,
        required this.slug,
        required this.answers,
        required this.bibleTitle,
        required this.bibleDescription,
    });

    final int? id;
    final String? slug;
    final String? answers;
    final dynamic bibleTitle;
    final String? bibleDescription;

    factory Option.fromJson(Map<String, dynamic> json){ 
        return Option(
            id: json["id"],
            slug: json["slug"],
            answers: json["answers"],
            bibleTitle: json["bible_title"],
            bibleDescription: json["bible_description"],
        );
    }

    Map<String, dynamic> toJson() => {
        "id": id,
        "slug": slug,
        "answers": answers,
        "bible_title": bibleTitle,
        "bible_description": bibleDescription,
    };

}
