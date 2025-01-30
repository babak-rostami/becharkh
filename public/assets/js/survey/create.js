function checkSurIsComplete() {
    if (
        $("#surop1box").length === 0 ||
        $.trim($("#surop1box").val()) === "" ||
        $("#surop2box").length === 0 ||
        $.trim($("#surop2box").val()) === ""
    ) {
        return 0;
    }
    return 1;
}

function removeSurvey() {
    $("#sur-box").hide();
    $("#add-survey-btn").show();
    $("#has_survey").val(0);
}

function addSurvey() {
    $("#sur-box").show();
    $("#add-survey-btn").hide();
    $("#has_survey").val(1);
}

function surOpX3() {
    $("#surop3box").hide();
    $("#surop3").val("");
}

function surOpX4() {
    $("#surop4box").hide();
    $("#sopx3").show();
    $("#suraddop").show();
    $("#surop4").val("");
}

function addSurOption() {
    if ($("#surop3box").css("display") == "none") {
        $("#surop3box").show();
        $("#sopx3").show();
    } else {
        $("#surop4box").show();
        $("#sopx3").hide();
        $("#sopx4").show();
        $("#suraddop").hide();
    }
}

function countSurCharacters(input, max = null) {
    var currentLength = $(input).val().length;
    if (currentLength > max) {
        $(input).val(
            $(input)
                .val()
                .substring(0, max)
        );
    }
}
