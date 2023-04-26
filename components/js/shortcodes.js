window.addEventListener("DOMContentLoaded", (event) => {
  const sectionHeaders = document.querySelectorAll(
    ".chapter > .before-content-connection-line ~ h2"
  );
  if (sectionHeaders.length > 0) {
    const beforeLine = document.querySelector(
      ".before-content-connection-line"
    );
    beforeLine.setAttribute("style", "display:block;");
  }
  for (let i = 0; i < sectionHeaders.length - 1; i++) {
    const sectionHeader = sectionHeaders[i];
    const line = document.createElement("div");
    sectionHeader.appendChild(line);
    line.classList.add("content-connection-line");
  }
  resizeLines();
});

let resizeDebounce;

window.addEventListener("resize", (event) => {
  clearTimeout(resizeDebounce);
  resizeDebounce = setTimeout(function () {
    resizeLines();
  }, 20);
});

const resizeLines = () => {
  const sectionHeaders = document.querySelectorAll(".chapter > h2");
  for (let i = 0; i < sectionHeaders.length - 1; i++) {
    const sectionHeader = sectionHeaders[i];
    const nextSectionHeader = sectionHeaders[i + 1];
    const startOffset = sectionHeader.offsetTop;
    const endOffset = nextSectionHeader.offsetTop;
    const distance = endOffset - startOffset;
    const line = sectionHeader.querySelector(".content-connection-line");
    if (line) {
      line.setAttribute("style", `height: ${distance}px;`);
    }
  }
};
