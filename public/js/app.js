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

  //Validar e formatar telefone br
  const campoTelefone = document.querySelector("#telefone");

  if (campoTelefone) {
    campoTelefone.addEventListener("input", () => {
      let numeros = campoTelefone.value.replace(/\D/g, "").slice(0, 11);

      if (numeros.length > 10) {
        numeros = numeros.replace(/^(\d{2})(\d{5})(\d{4}).*/, "($1) $2-$3");
      } else if (numeros.length > 6) {
        numeros = numeros.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, "($1) $2-$3");
      } else if (numeros.length > 2) {
        numeros = numeros.replace(/^(\d{2})(\d+)/, "($1) $2");
      } else if (numeros.length > 0) {
        numeros = `(${numeros}`;
      }

      campoTelefone.value = numeros;
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

  //Controlar taxa de entrega pelo CEP
  const campoCep = document.querySelector("[data-cep]");
  const linhaEntrega = document.querySelector("#linha-entrega");
  const valorTotal = document.querySelector("#valor-total");

  if (campoCep && linhaEntrega && valorTotal) {
    const subtotal = Number(valorTotal.dataset.subtotal);
    const taxaEntrega = 12;

    campoCep.addEventListener("input", () => {
      const numeros = campoCep.value.replace(/\D/g, "").slice(0, 8);

      campoCep.value =
        numeros.length > 5
          ? `${numeros.slice(0, 5)}-${numeros.slice(5)}`
          : numeros;

      const cepValido = numeros.length === 8;

      linhaEntrega.hidden = !cepValido;

      const total = cepValido ? subtotal + taxaEntrega : subtotal;

      valorTotal.textContent = total.toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL",
      });
    });
  }
});
