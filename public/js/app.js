document.addEventListener("DOMContentLoaded", () => {
  const botaoMenu = document.querySelector("[data-menu-toggle]");
  const menu = document.querySelector("#menu");

  if (!botaoMenu || !menu) {
    return;
  }

  botaoMenu.addEventListener("click", () => {
    const aberto = menu.classList.toggle("aberto");

    botaoMenu.setAttribute("aria-expanded", String(aberto));
    botaoMenu.setAttribute("aria-label", aberto ? "Fechar menu" : "Abrir menu");

    botaoMenu.textContent = aberto ? "✕" : "☰";
  });

  menu.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      menu.classList.remove("aberto");
      botaoMenu.setAttribute("aria-expanded", "false");
      botaoMenu.setAttribute("aria-label", "Abrir menu");
      botaoMenu.textContent = "☰";
    });
  });
});
