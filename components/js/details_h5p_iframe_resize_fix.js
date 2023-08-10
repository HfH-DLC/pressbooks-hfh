window.addEventListener("DOMContentLoaded", () => {
  const details = document.querySelectorAll("details");
  for (var i = 0; i < details.length; i++) {
    details[i].addEventListener(
      "toggle",
      () => window.dispatchEvent(new Event("resize")),
      false
    );
  }
});
