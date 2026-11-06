class SignInModel {
    SignInModel({
        required this.success,
        required this.code,
        required this.message,
        required this.data,
    });

    final bool? success;
    final int? code;
    final String? message;
    final Data? data;

    factory SignInModel.fromJson(Map<String, dynamic> json){ 
        return SignInModel(
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
        required this.user,
        required this.token,
        required this.tokenType,
    });

    final User? user;
    final String? token;
    final String? tokenType;

    factory Data.fromJson(Map<String, dynamic> json){ 
        return Data(
            user: json["user"] == null ? null : User.fromJson(json["user"]),
            token: json["token"],
            tokenType: json["token_type"],
        );
    }

    Map<String, dynamic> toJson() => {
        "user": user?.toJson(),
        "token": token,
        "token_type": tokenType,
    };

}

class User {
    User({
        required this.id,
        required this.name,
        required this.email,
        required this.profileImage,
        required this.role,
    });

    final int? id;
    final String? name;
    final String? email;
    final String? profileImage;
    final String? role;

    factory User.fromJson(Map<String, dynamic> json){ 
        return User(
            id: json["id"],
            name: json["name"],
            email: json["email"],
            profileImage: json["profile_image"],
            role: json["role"],
        );
    }

    Map<String, dynamic> toJson() => {
        "id": id,
        "name": name,
        "email": email,
        "profile_image": profileImage,
        "role": role,
    };

}
