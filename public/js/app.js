document.addEventListener("DOMContentLoaded", () => {
  //Controlar menu responsivo
  const botaoMenu = document.querySelector("[data-menu-toggle]");
  const menu = document.querySelector("#menu");

  if (botaoMenu && menu) {
    botaoMenu.addEventListener("click", () => {
      const aberto = menu.classList.toggle("aberto");

      botaoMenu.setAttribute("aria-expanded", String(aberto));
      botaoMenu.setAttribute(
        "aria-label",
        aberto ? "Fechar menu" : "Abrir menu",
      );

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
  }

  //Controlar campos de pagamento
  const metodosPagamento = document.querySelectorAll("[data-metodo-pagamento]");
  const camposCartao = document.querySelector("[data-campos-cartao]");

  if (metodosPagamento.length && camposCartao) {
    const atualizarPagamento = () => {
      const cartaoSelecionado = document.querySelector(
        '[data-metodo-pagamento][value="CARTAO"]:checked',
      );

      camposCartao.hidden = !cartaoSelecionado;

      camposCartao.querySelectorAll("input").forEach((campo) => {
        campo.disabled = !cartaoSelecionado;
        campo.required = Boolean(cartaoSelecionado);
      });
    };

    metodosPagamento.forEach((metodo) => {
      metodo.addEventListener("change", atualizarPagamento);
    });

    atualizarPagamento();
  }
});
