class QuizTypesModel {
    QuizTypesModel({
        required this.success,
        required this.code,
        required this.message,
        required this.data,
    });

    final bool? success;
    final int? code;
    final String? message;
    final Data? data;

    factory QuizTypesModel.fromJson(Map<String, dynamic> json){ 
        return QuizTypesModel(
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
        required this.quizTypes,
    });

    final List<QuizType> quizTypes;

    factory Data.fromJson(Map<String, dynamic> json){ 
        return Data(
            quizTypes: json["quiz_types"] == null ? [] : List<QuizType>.from(json["quiz_types"]!.map((x) => QuizType.fromJson(x))),
        );
    }

    Map<String, dynamic> toJson() => {
        "quiz_types": quizTypes.map((x) => x?.toJson()).toList(),
    };

}

class QuizType {
    QuizType({
        required this.id,
        required this.title,
        required this.slug,
        required this.imageUrl,
        required this.sloganText,
        required this.description,
        required this.seoTitle,
        required this.seoDescription,
    });

    final int? id;
    final String? title;
    final String? slug;
    final String? imageUrl;
    final String? sloganText;
    final String? description;
    final String? seoTitle;
    final String? seoDescription;

    factory QuizType.fromJson(Map<String, dynamic> json){ 
        return QuizType(
            id: json["id"],
            title: json["title"],
            slug: json["slug"],
            imageUrl: json["image_url"],
            sloganText: json["slogan_text"],
            description: json["description"],
            seoTitle: json["seo_title"],
            seoDescription: json["seo_description"],
        );
    }

    Map<String, dynamic> toJson() => {
        "id": id,
        "title": title,
        "slug": slug,
        "image_url": imageUrl,
        "slogan_text": sloganText,
        "description": description,
        "seo_title": seoTitle,
        "seo_description": seoDescription,
    };

}
