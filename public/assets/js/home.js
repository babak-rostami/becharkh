const catSlider = document.getElementById("cat-slider");
createSlider(catSlider, "cat-slider-item");

const questionSlider = document.getElementById("question-slider");
createSlider(questionSlider, "question-slider-item", 1);

const blogSlider = document.getElementById("blog-slider");
createSlider(blogSlider, "blog-slider-item");


var who_count = 0;

setInterval(() => {
    $("#page-desc2").css("opacity", 0);
    $("#page-desc2").animate(
        {
            opacity: 1
        },
        1000
    );
    $("#page-desc2").text(for_who[who_count]);
    who_count++;
    if (who_count >= for_who.length) {
        who_count = 0;
    }
}, 2500);

for_who = [
    "خریداران",
    "فروشندگان",
    "ادمین ها",
    "برنامه نویس ها",
    "ماشین بازان",
    "علاقه مندان فیلم و سریال",
    "سرمایه گذاران",
    "افراد جویای کار",
    "علاقه مندان مد",
    "علاقه مندان به حیوانات",
    "پزشکان",
    "کتاب خوان ها",
    "شما",
    "همه"
];
