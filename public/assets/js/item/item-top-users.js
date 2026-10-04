document.addEventListener("DOMContentLoaded", function () {
    const top_user_colors = [
        "#4f46e5", // آبی بنفش
        "#0ea5e9", // آبی آسمونی
        "#14b8a6", // سبز آبی
        "#f59e0b", // نارنجی
        "#ef4444", // قرمز
        "#64748b", // خاکستری آبی
    ];

    document.querySelectorAll(".top-user-avatar-circle").forEach((el) => {
        const top_user_name = el.dataset.name?.trim() || "?";
        const top_user_letter = top_user_name.slice(0, 2).toUpperCase();
        const bg = top_user_colors[Math.floor(Math.random() * top_user_colors.length)];

        el.textContent = top_user_letter;
        el.style.backgroundColor = bg;
        el.style.color = "#fff";
    });
});