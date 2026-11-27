import "katex/dist/katex.min.css";
import renderMathInElement from "katex/contrib/auto-render";

renderMathInElement(document.body, {
    delimiters: [
        { left: "$$", right: "$$", display: true }, // rumus di baris sendiri
        { left: "\\(", right: "\\)", display: false }, // rumus di dalam kalimat
    ],
});
