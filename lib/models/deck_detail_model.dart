class DeckDetailModel {
    DeckDetailModel({
        required this.success,
        required this.code,
        required this.message,
        required this.data,
    });

    final bool? success;
    final int? code;
    final String? message;
    final Data? data;

    factory DeckDetailModel.fromJson(Map<String, dynamic> json){ 
        return DeckDetailModel(
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
        required this.category,
    });

    final Category? category;

    factory Data.fromJson(Map<String, dynamic> json){ 
        return Data(
            category: json["category"] == null ? null : Category.fromJson(json["category"]),
        );
    }

    Map<String, dynamic> toJson() => {
        "category": category?.toJson(),
    };

}

class Category {
    Category({
        required this.id,
        required this.title,
        required this.slug,
        required this.imageUrl,
        required this.description,
        required this.totalQuestions,
        required this.reward,
        required this.difficulty,
        required this.progress,
        required this.lastQuestion,
        required this.seoTitle,
        required this.seoDescription,
    });

    final int? id;
    final String? title;
    final String? slug;
    final String? imageUrl;
    final dynamic description;
    final int? totalQuestions;
    final Reward? reward;
    final List<LastQuestion> difficulty;
    final Progress? progress;
    final LastQuestion? lastQuestion;
    final String? seoTitle;
    final dynamic seoDescription;

    factory Category.fromJson(Map<String, dynamic> json){ 
        return Category(
            id: json["id"],
            title: json["title"],
            slug: json["slug"],
            imageUrl: json["image_url"],
            description: json["description"],
            totalQuestions: json["total_questions"],
            reward: json["reward"] == null ? null : Reward.fromJson(json["reward"]),
            difficulty: json["difficulty"] == null ? [] : List<LastQuestion>.from(json["difficulty"]!.map((x) => LastQuestion.fromJson(x))),
            progress: json["progress"] == null ? null : Progress.fromJson(json["progress"]),
            lastQuestion: json["last_question"] == null ? null : LastQuestion.fromJson(json["last_question"]),
            seoTitle: json["seo_title"],
            seoDescription: json["seo_description"],
        );
    }

    Map<String, dynamic> toJson() => {
        "id": id,
        "title": title,
        "slug": slug,
        "image_url": imageUrl,
        "description": description,
        "total_questions": totalQuestions,
        "reward": reward?.toJson(),
        "difficulty": difficulty.map((x) => x?.toJson()).toList(),
        "progress": progress?.toJson(),
        "last_question": lastQuestion?.toJson(),
        "seo_title": seoTitle,
        "seo_description": seoDescription,
    };

}

class LastQuestion {
    LastQuestion({
        required this.id,
        required this.title,
        required this.slug,
        required this.sloganText,
    });

    final int? id;
    final String? title;
    final String? slug;
    final String? sloganText;

    factory LastQuestion.fromJson(Map<String, dynamic> json){ 
        return LastQuestion(
            id: json["id"],
            title: json["title"],
            slug: json["slug"],
            sloganText: json["slogan_text"],
        );
    }

    Map<String, dynamic> toJson() => {
        "id": id,
        "title": title,
        "slug": slug,
        "slogan_text": sloganText,
    };

}

class Progress {
    Progress({
        required this.completedQuestions,
        required this.progressPercent,
    });

    final int? completedQuestions;
    final int? progressPercent;

    factory Progress.fromJson(Map<String, dynamic> json){ 
        return Progress(
            completedQuestions: json["completed_questions"],
            progressPercent: json["progress_percent"],
        );
    }

    Map<String, dynamic> toJson() => {
        "completed_questions": completedQuestions,
        "progress_percent": progressPercent,
    };

}

class Reward {
    Reward({
        required this.xp,
        required this.coins,
    });

    final int? xp;
    final int? coins;

    factory Reward.fromJson(Map<String, dynamic> json){ 
        return Reward(
            xp: json["xp"],
            coins: json["coins"],
        );
    }

    Map<String, dynamic> toJson() => {
        "xp": xp,
        "coins": coins,
    };

}
