function openCrudModal(options) {
    // Title
    $("#crudModalTitle").text(options.title);

    // Button Text
    $("#crudModalSubmit").text(options.button);

    // Action URL + Method
    $("#crudForm").attr("action", options.url);

    if (options.method === "PUT") {
        $("#crudForm").append('<input type="hidden" name="_method" value="PUT">');
    } else {
        $("#crudForm input[name='_method']").remove();
    }

    $("#crudModalBody").html(options.fields);
    $("#crudModal").modal("show");
}
